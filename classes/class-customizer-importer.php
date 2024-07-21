<?php

/**
 *
 * Code is mostly from the Customizer Export/Import plugin.
 *
 * @see https://wordpress.org/plugins/customizer-export-import/
 *
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * The customizer import class.
 *
 */
class HDI_Customizer_Importer {

    /**
     * Imports uploaded mods
     *
     *
     * @access public
     * @param object $wp_customize An instance of WP_Customize_Manager.
     * @return void
     */
    public static function import($customizerFile, $excludeImages) {
        global $wp_customize;
        $template = get_template();
        $data = maybe_unserialize(file_get_contents($customizerFile));
        $excludeImages = $excludeImages == 'true' ? true : false;

        // Data checks.
        if ('array' != gettype($data)) {
            $error = __('Error importing settings! Please check that you uploaded a customizer export file.', 'hashthemes-demo-importer');
            return;
        }
        if (!isset($data['template']) || !isset($data['mods'])) {
            $error = __('Error importing settings! Please check that you uploaded a customizer export file.', 'hashthemes-demo-importer');
            return;
        }
        if ($data['template'] != $template) {
            $error = __('Error importing settings! The settings you uploaded are not for the current theme.', 'hashthemes-demo-importer');
            return;
        }

        // Import Images.
        if (!$excludeImages) {
            $data['mods'] = self::import_images($data['mods']);
        }

        // Import custom options.
        if (isset($data['options'])) {

            // Load WordPress Customize Setting Class
            if (!class_exists('WP_Customize_Setting')) {
                require_once (ABSPATH . WPINC . '/class-wp-customize-setting.php');
            }

            // Include Customizer Option class.
            include_once (dirname(__FILE__) . '/class-customizer-option.php');

            foreach ($data['options'] as $option_key => $option_value) {
                $option = new HDI_Customzer_Option(
                        $wp_customize, $option_key, array(
                    'default' => '',
                    'type' => 'option',
                    'capability' => 'edit_theme_options',
                        )
                );

                $option->import($option_value);
            }
        }

        // If wp_css is set then import it.
        if (function_exists('wp_update_custom_css_post') && isset($data['wp_css']) && '' !== $data['wp_css']) {
            wp_update_custom_css_post($data['wp_css']);
        }

        // Loop through theme mods and update them.
        if (!empty($data['mods'])) {
            foreach ($data['mods'] as $key => $value) {
                set_theme_mod($key, $value);
            }
        }
    }

    /**
     * Imports images for settings saved as mods.
     *
     * @param  array $mods An array of customizer mods.
     * @return array The mods array with any new import data.
     */
    private static function import_images($mods) {
        foreach ($mods as $key => $value) {

            //For repeater fields
            if (self::isJSON($value)) {
                $data_array = json_decode($value);
                foreach ($data_array as $data_key => $data_object) {
                    foreach ($data_object as $sub_data_key => $sub_data_value) {
                        if (self::is_image_url($sub_data_value)) {
                            $sub_data = self::media_handle_sideload($sub_data_value);
                            if (!is_wp_error($sub_data)) {
                                $data_object->$sub_data_key = $sub_data->url;
                            }
                        } else {
                            $data_object->$sub_data_key = $sub_data_value;
                        }
                    }
                    $data_array[$data_key] = $data_object;
                }

                $mods[$key] = json_encode($data_array);
            } else if (self::is_image_url($value)) {
                $data = self::media_handle_sideload($value);
                if (!is_wp_error($data)) {
                    $mods[$key] = $data->url;

                    // Handle header image controls.
                    if (isset($mods[$key . '_data'])) {
                        $mods[$key . '_data'] = $data;
                        update_post_meta($data->attachment_id, '_wp_attachment_is_custom_header', get_stylesheet());
                    }
                }
            }
        }

        return $mods;
    }

    /**
     * Taken from the core media_sideload_image function and
     * modified to return an array of data instead of html.
     *
     * @param  string $file The image file path.
     * @return array An array of image data.
     */
    private static function media_handle_sideload($file) {
        $data = new stdClass();

        if (!function_exists('media_handle_sideload')) {
            require_once (ABSPATH . 'wp-admin/includes/media.php');
            require_once (ABSPATH . 'wp-admin/includes/file.php');
            require_once (ABSPATH . 'wp-admin/includes/image.php');
        }

        if (!empty($file)) {
            // Set variables for storage, fix file filename for query strings.
            preg_match('/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file, $matches);
            $file_array = array();
            $file_array['name'] = basename($matches[0]);

            // Download file to temp location.
            $file_array['tmp_name'] = download_url($file);

            // If error storing temporarily, return the error.
            if (is_wp_error($file_array['tmp_name'])) {
                return $file_array['tmp_name'];
            }

            // Do the validation and storage stuff.
            $id = media_handle_sideload($file_array, 0);

            // If error storing permanently, unlink.
            if (is_wp_error($id)) {
                @unlink($file_array['tmp_name']);
                return $id;
            }

            // Build the object to return.
            $meta = wp_get_attachment_metadata($id);
            $data->attachment_id = $id;
            $data->url = wp_get_attachment_url($id);
            $data->thumbnail_url = wp_get_attachment_thumb_url($id);
            $data->height = $meta['height'];
            $data->width = $meta['width'];
        }

        return $data;
    }

    /**
     * Checks to see whether a url is an image url or not.
     *
     * @param  string $url The url to check.
     * @return bool Whether the url is an image url or not.
     */
    private static function is_image_url($url) {
        if (is_string($url) && preg_match('/\.(jpg|jpeg|png|gif)/i', $url)) {
            return true;
        }

        return false;
    }

    private static function isJSON($string) {
        return is_string($string) && is_array(json_decode($string, true)) ? true : false;
    }

}
