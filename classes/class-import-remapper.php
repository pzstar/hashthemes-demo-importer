<?php

/**
 * Points imported settings at this site's terms and pages.
 *
 * WordPress gives imported categories, tags and menus new IDs, and the order it exports them in
 * (by name) is rarely the order they were created in. Customizer settings and widgets that name a
 * term by ID, such as a front page section showing one category, would otherwise show another.
 * Links they hold to the demo site's pages (buttons, menus of boxes) would also lead back to it.
 *
 * The content import records which old term ID became which new one, and the demo site's address.
 * The customizer and widget imports then swap:
 * - IDs in settings whose name says they hold a category or menu: "category", "category2", "cat",
 *   "categories", "nav_menu"..., and in per-term setting names like "theme_category_5_color";
 * - links to the demo site for links to this one. Links to its uploads are left alone: the
 *   customizer import downloads those images itself, and they stay reachable where it does not.
 */
if (!defined('ABSPATH')) {
    exit;
}

class HDI_Import_Remapper {

    const OPTION = 'hdi_import_remap';

    /**
     * Saves what the content import of this demo changed: old => new term IDs and the demo site's address.
     */
    public static function save($demo_slug, $processed_terms, $xml_filepath) {
        $map = array();
        foreach ((array) $processed_terms as $old => $new) {
            if ((int) $old && (int) $new) {
                $map[(int) $old] = (int) $new;
            }
        }
        update_option(self::OPTION, array('demo' => $demo_slug, 'map' => $map, 'url' => self::demo_url($xml_filepath)), false);
    }

    public static function clear() {
        delete_option(self::OPTION);
    }

    /**
     * The term map for this demo. A map left by another demo's import is never used.
     */
    public static function get($demo_slug) {
        $saved = self::saved($demo_slug);
        if (empty($saved['map'])) {
            return array();
        }
        // Nothing to do when every term kept its ID.
        foreach ($saved['map'] as $old => $new) {
            if ((int) $old !== (int) $new) {
                return $saved['map'];
            }
        }
        return array();
    }

    /**
     * The demo site's address, when it differs from this site's.
     */
    public static function get_url($demo_slug) {
        $saved = self::saved($demo_slug);
        $url = empty($saved['url']) ? '' : untrailingslashit($saved['url']);
        return ($url && $url !== untrailingslashit(home_url())) ? $url : '';
    }

    private static function saved($demo_slug) {
        $saved = get_option(self::OPTION);
        return (is_array($saved) && isset($saved['demo']) && $saved['demo'] === $demo_slug) ? $saved : array();
    }

    /**
     * The home address the demo was exported from, read from the head of its content.xml.
     */
    private static function demo_url($xml_filepath) {
        $head = (string) @file_get_contents($xml_filepath, false, null, 0, 20000);
        if (preg_match('#<wp:base_blog_url>\s*(?:<!\[CDATA\[)?\s*(https?://[^<\]\s]+)#', $head, $m) || preg_match('#<wp:base_site_url>\s*(?:<!\[CDATA\[)?\s*(https?://[^<\]\s]+)#', $head, $m)) {
            return esc_url_raw(trim($m[1]));
        }
        return '';
    }

    /**
     * Theme mods: per-term setting names, term settings, and term fields inside JSON repeaters.
     */
    public static function remap_mods($mods, $map) {
        if (!$map || !is_array($mods)) {
            return $mods;
        }

        $remapped = array();
        foreach ($mods as $key => $value) {
            if (is_string($key) && preg_match('/^(.*_(?:category|cat|term)_)(\d+)(_.*)?$/', $key, $m) && isset($map[(int) $m[2]])) {
                $key = $m[1] . $map[(int) $m[2]] . (isset($m[3]) ? $m[3] : '');
            }

            if (self::is_term_key($key)) {
                $value = self::remap_ids($value, $map);
            } elseif (is_array($value)) {
                $value = self::remap_deep($value, $map);
            } elseif (is_string($value) && self::is_json_list($value)) {
                $decoded = json_decode($value, true);
                $new = self::remap_deep($decoded, $map);
                if ($new !== $decoded) {
                    $value = wp_json_encode($new);
                }
            }

            $remapped[$key] = $value;
        }

        return $remapped;
    }

    /**
     * Every term field, at any depth (repeater rows, widget tabs and items...).
     */
    public static function remap_deep($data, $map) {
        if (!$map || !is_array($data)) {
            return $data;
        }
        foreach ($data as $key => $value) {
            if (self::is_term_key($key)) {
                $data[$key] = self::remap_ids($value, $map);
            } elseif (is_array($value)) {
                $data[$key] = self::remap_deep($value, $map);
            }
        }
        return $data;
    }

    /**
     * Links to the demo site's pages become links to this site, at any depth and also when written
     * with escaped slashes inside JSON. Links into wp-content (uploads, theme files) are kept.
     */
    public static function remap_urls($data, $old_url) {
        if (!$old_url) {
            return $data;
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = self::remap_urls($value, $old_url);
            }
            return $data;
        }
        if (!is_string($data) || false === strpos(str_replace('\\/', '/', $data), $old_url)) {
            return $data;
        }

        $new_url = untrailingslashit(home_url());
        foreach (array(array($old_url, $new_url), array(str_replace('/', '\\/', $old_url), str_replace('/', '\\/', $new_url))) as $pair) {
            list($from, $to) = $pair;
            $slash = preg_quote(false !== strpos($from, '\\/') ? '\\/' : '/', '#');
            // The address, then the end of the string or a non-path character, or a path not into wp-content.
            $data = preg_replace('#' . preg_quote($from, '#') . '(?=$|["\'\s<?\#]|' . $slash . '(?!wp-content))#', addcslashes($to, '\\$'), $data);
        }
        return $data;
    }

    /**
     * Elementor pages name categories by ID in their widgets' settings ("category_ids",
     * "posts_category_ids", "post_cats", and the "category" of WordPress widgets placed in them).
     * Runs right after the content import, on the posts it created.
     */
    public static function remap_elementor_data($processed_posts, $processed_terms) {
        $map = array();
        foreach ((array) $processed_terms as $old => $new) {
            if ((int) $old && (int) $new && (int) $old !== (int) $new) {
                $map[(int) $old] = (int) $new;
            }
        }
        if (!$map) {
            return 0;
        }

        $updated = 0;
        foreach (array_unique(array_map('intval', (array) $processed_posts)) as $post_id) {
            $raw = get_post_meta($post_id, '_elementor_data', true);
            if (!$raw) {
                continue;
            }
            $data = is_array($raw) ? $raw : json_decode($raw, true);
            if (!is_array($data)) {
                continue;
            }
            $new = self::remap_deep($data, $map);
            if ($new !== $data) {
                update_post_meta($post_id, '_elementor_data', wp_slash(wp_json_encode($new)));
                $updated++;
            }
        }
        return $updated;
    }

    /**
     * "category", "category1", "cat", "cats", "categories", "nav_menu", "theme_ticker_category",
     * "category_ids", "posts_category_ids", "blog_cat_exclude", "portfolio_cat_menu"...
     * but not switches like "display_cat".
     */
    public static function is_term_key($key) {
        if (!is_string($key)) {
            return false;
        }
        $key = strtolower($key);
        if (false !== strpos($key, 'display') || false !== strpos($key, 'show_')) {
            return false;
        }
        return (bool) preg_match('/(^|_)(cat|cats|category|categories|term|terms|nav_menu)(_(id|ids|exclude|include|menu|list|in|not_in))?\d*$/', $key);
    }

    /**
     * One ID, a comma separated list ("3,7,-1"), a list of IDs, or IDs as array keys.
     * Anything else (-1 for "all", "", names) is left alone.
     */
    public static function remap_ids($value, $map) {
        if (is_int($value) || (is_string($value) && ctype_digit($value))) {
            return isset($map[(int) $value]) ? (is_int($value) ? $map[(int) $value] : (string) $map[(int) $value]) : $value;
        }

        if (is_string($value) && preg_match('/^-?\d+(\s*,\s*-?\d+)+$/', $value)) {
            $ids = array_map('trim', explode(',', $value));
            foreach ($ids as $i => $id) {
                if (ctype_digit($id) && isset($map[(int) $id])) {
                    $ids[$i] = (string) $map[(int) $id];
                }
            }
            return implode(',', $ids);
        }

        if (is_array($value)) {
            if (array_values($value) === $value) {
                foreach ($value as $i => $id) {
                    $value[$i] = self::remap_ids($id, $map);
                }
                return $value;
            }
            $by_key = array();
            foreach ($value as $id => $v) {
                $by_key[(is_int($id) && isset($map[$id])) ? $map[$id] : $id] = $v;
            }
            return $by_key;
        }

        return $value;
    }

    private static function is_json_list($value) {
        $value = ltrim($value);
        return ('[' === substr($value, 0, 1) || '{' === substr($value, 0, 1)) && is_array(json_decode($value, true));
    }

}
