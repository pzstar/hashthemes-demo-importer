<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Imports demos from the command line.
 *
 * The browser flow splits the work across ajax steps to survive php time
 * limits. On the cli there is no such limit, so the same runners are called
 * one after another in a single process.
 */
class HDI_CLI_Command {

    /**
     * Lists the demos available for the active theme.
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     * ---
     *
     * ## EXAMPLES
     *
     *     wp hdi list
     */
    public function list_( $args, $assoc_args ) {
        $config = hdi_importer()->get_config();

        if (!is_array($config) || !$config) {
            WP_CLI::warning(__('No demos are defined for the active theme.', 'hashthemes-demo-importer'));
            return;
        }

        $rows = array();

        foreach ($config as $slug => $demo) {
            $rows[] = array(
                'slug' => $slug,
                'name' => isset($demo['name']) ? $demo['name'] : '',
                'type' => isset($demo['type']) ? $demo['type'] : 'free',
                'plugins' => isset($demo['plugins']) && is_array($demo['plugins']) ? count($demo['plugins']) : 0,
            );
        }

        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';

        WP_CLI\Utils\format_items($format, $rows, array('slug', 'name', 'type', 'plugins'));
    }

    /**
     * Imports a demo.
     *
     * ## OPTIONS
     *
     * <slug>
     * : The demo to import. Run `wp hdi list` to see what is available.
     *
     * [--reset]
     * : Reset the site before importing. This deletes all posts, pages, terms,
     * media and settings. There is no undo.
     *
     * [--exclude-images]
     * : Skip downloading media. Much faster, and useful when an import keeps
     * failing part way through.
     *
     * [--parts=<parts>]
     * : Comma separated list of what to import. Defaults to everything.
     * Available: content, customizer, options, widgets, forms.
     *
     * [--yes]
     * : Skip the confirmation prompt.
     *
     * ## EXAMPLES
     *
     *     wp hdi import main --reset
     *     wp hdi import el-main --parts=content,customizer --exclude-images
     */
    public function import( $args, $assoc_args ) {
        $importer = hdi_importer();
        $slug = isset($args[0]) ? $args[0] : '';

        if (!$importer->get_demo($slug)) {
            WP_CLI::error(sprintf(__('Unknown demo "%s". Run `wp hdi list` to see the available demos.', 'hashthemes-demo-importer'), $slug));
        }

        $reset = isset($assoc_args['reset']);
        $exclude_images = isset($assoc_args['exclude-images']) ? 'true' : 'false';
        $valid_parts = array_keys($importer->get_import_parts());

        if (isset($assoc_args['parts'])) {
            $parts = array_filter(array_map('trim', explode(',', $assoc_args['parts'])));
            $unknown = array_diff($parts, $valid_parts);

            if ($unknown) {
                WP_CLI::error(sprintf(__('Unknown part(s): %1$s. Available: %2$s', 'hashthemes-demo-importer'), implode(', ', $unknown), implode(', ', $valid_parts)));
            }
        } else {
            $parts = $valid_parts;
        }

        if ($reset) {
            WP_CLI::confirm(__('This will delete all existing content and settings on this site. Continue?', 'hashthemes-demo-importer'), $assoc_args);
        }

        $importer->reset_log();

        // The browser sends these; the runners still read them for the request
        // path, so set them for anything that falls back to $_POST.
        $_POST['demo'] = $slug;
        $_POST['excludeImages'] = $exclude_images;
        $_POST['parts'] = implode(',', $parts);

        $steps = array();

        if ($reset) {
            $steps[] = array(__('Resetting the site', 'hashthemes-demo-importer'), function () use ($importer) {
                return $importer->run_reset();
            });
        }

        $steps[] = array(__('Installing required plugins', 'hashthemes-demo-importer'), function () use ($importer, $slug) {
            return $importer->run_install_plugins($slug);
        });

        $steps[] = array(__('Activating required plugins', 'hashthemes-demo-importer'), function () use ($importer, $slug) {
            return $importer->run_activate_plugins($slug);
        });

        $steps[] = array(__('Downloading demo files', 'hashthemes-demo-importer'), function () use ($importer, $slug) {
            return $importer->run_download($slug);
        });

        $steps[] = array(__('Importing content', 'hashthemes-demo-importer'), function () use ($importer, $slug, $exclude_images, $parts) {
            return $importer->run_content($slug, $exclude_images, $parts);
        });

        $steps[] = array(__('Importing customizer settings', 'hashthemes-demo-importer'), function () use ($importer, $slug, $exclude_images, $parts) {
            return $importer->run_customizer($slug, $exclude_images, $parts);
        });

        $steps[] = array(__('Setting menus', 'hashthemes-demo-importer'), function () use ($importer, $slug, $parts) {
            return $importer->run_menus($slug, $parts);
        });

        $steps[] = array(__('Importing theme options', 'hashthemes-demo-importer'), function () use ($importer, $slug, $parts) {
            return $importer->run_theme_options($slug, $parts);
        });

        $steps[] = array(__('Importing widgets', 'hashthemes-demo-importer'), function () use ($importer, $slug, $parts) {
            return $importer->run_widgets($slug, $parts);
        });

        $steps[] = array(__('Importing forms', 'hashthemes-demo-importer'), function () use ($importer, $slug, $parts) {
            return $importer->run_hash_forms($slug, $parts);
        });

        $steps[] = array(__('Importing Revolution Slider', 'hashthemes-demo-importer'), function () use ($importer, $slug, $parts) {
            if (!$importer->has_revslider_file($slug)) {
                return array('error' => false, 'message' => __('No slider in this demo', 'hashthemes-demo-importer'));
            }

            return $importer->run_revslider($slug, $parts);
        });

        $steps[] = array(__('Finishing up', 'hashthemes-demo-importer'), function () use ($importer, $slug) {
            return $importer->run_finalize($slug);
        });

        $progress = WP_CLI\Utils\make_progress_bar(__('Importing demo', 'hashthemes-demo-importer'), count($steps));

        foreach ($steps as $step) {
            list($label, $callback) = $step;

            $result = call_user_func($callback);
            $progress->tick();

            if (!empty($result['error'])) {
                $progress->finish();
                WP_CLI::log($importer->get_log_text());
                WP_CLI::error(sprintf('%1$s: %2$s', $label, $result['message']));
            }

            WP_CLI::debug(sprintf('%1$s: %2$s', $label, $result['message']), 'hdi');
        }

        $progress->finish();

        WP_CLI::success(sprintf(__('Demo "%s" imported.', 'hashthemes-demo-importer'), $slug));
    }

}

// Public methods become subcommands; list_() maps to `wp hdi list`.
WP_CLI::add_command('hdi', 'HDI_CLI_Command');
