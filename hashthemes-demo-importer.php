<?php
/**
 * Plugin Name: HashThemes Demo Importer
 * Plugin URI: https://github.com/pzstar/hashthemes-demo-importer
 * Description: Easily imports demo with just one click.
 * Version: 2.0
 * Author: hashthemes
 * Author URI:  https://hashthemes.com
 * Text Domain: hashthemes-demo-importer
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 */
if (!defined('ABSPATH'))
    exit;


define('HDI_VERSION', '2.0');

define('HDI_FILE', __FILE__);
define('HDI_PLUGIN_BASENAME', plugin_basename(HDI_FILE));
define('HDI_PATH', plugin_dir_path(HDI_FILE));
define('HDI_URL', plugins_url('/', HDI_FILE));

define('HDI_ASSETS_URL', HDI_URL . 'assets/');

if (!class_exists('HDI_Importer')) {

    class HDI_Importer {

        public $configFile;
        public $uploads_dir;
        public $plugin_install_count;
        public $plugin_active_count;
        public $ajax_response = array();
        public $page_hook = '';

        /*
         * Constructor
         */

        public function __construct() {

            $this->uploads_dir = wp_get_upload_dir();

            $this->plugin_install_count = 0;
            $this->plugin_active_count = 0;

            require_once HDI_PATH . 'classes/class-demo-importer.php';
            require_once HDI_PATH . 'classes/class-customizer-importer.php';
            require_once HDI_PATH . 'classes/class-widget-importer.php';

            if (defined('WP_CLI') && WP_CLI) {
                require_once HDI_PATH . 'classes/class-cli.php';
            }

            // Load translation files
            add_action('init', array($this, 'load_plugin_textdomain'));

            // WP-Admin Menu
            add_action('admin_menu', array($this, 'add_menu'));

            // Add necesary backend JS
            add_action('admin_enqueue_scripts', array($this, 'load_backends'));

            // Add Elementor required Changes
            add_action('admin_init', array($this, 'overwrite_elementor_settings'));

            // Allow SVG uploads
            add_filter('upload_mimes', array($this, 'file_types_to_uploads'));

            // Enable SVG for the WordPress Importer
            //add_filter('wp_import_allowed_mime_types', array($this, 'file_types_to_uploads'));

            //add_filter('wp_check_filetype_and_ext', array($this, 'fix_svg_file_check'), 10, 4);

            // Actions for the ajax call
            add_action('wp_ajax_hdi_install_demo', array($this, 'install_demo_process'));
            add_action('wp_ajax_hdi_install_plugin', array($this, 'install_plugin_process'));
            add_action('wp_ajax_hdi_activate_plugin', array($this, 'activate_plugin_process'));
            add_action('wp_ajax_hdi_download_files', array($this, 'download_files_process'));
            add_action('wp_ajax_hdi_import_xml', array($this, 'import_xml_process'));
            add_action('wp_ajax_hdi_import_customizer', array($this, 'import_customizer_process'));
            add_action('wp_ajax_hdi_import_menu', array($this, 'import_menu_process'));
            add_action('wp_ajax_hdi_import_theme_option', array($this, 'import_theme_option_process'));
            add_action('wp_ajax_hdi_import_widget', array($this, 'import_widget_process'));
            add_action('wp_ajax_hdi_import_hashform', array($this, 'import_hashform_process'));
            add_action('wp_ajax_hdi_import_revslider', array($this, 'import_revslider_process'));
            add_action('wp_ajax_hdi_custom_import_hook', array($this, 'add_custom_import_hook'));
            add_action('wp_ajax_hdi_import_log', array($this, 'import_log_process'));
            add_action('wp_ajax_hdi_preflight', array($this, 'preflight_process'));

            add_filter('plugin_action_links_' . plugin_basename(HDI_FILE), array($this, 'add_plugin_action_link'), 10, 1);
        }

        /*
         * The demo config is a large file, so only read it when it is actually
         * needed instead of on every page load.
         */

        public function get_config() {
            if (is_null($this->configFile)) {
                $this->configFile = include HDI_PATH . 'import_config.php';
            }

            return $this->configFile;
        }

        /*
         * Returns the config of a single demo, or false when the slug is unknown.
         */

        public function get_demo($slug) {
            $config = $this->get_config();

            if (!is_array($config) || !$slug || !isset($config[$slug]) || !is_array($config[$slug])) {
                return false;
            }

            return $config[$slug];
        }

        // 2. Bypass the filetype check error for SVGs
        public function fix_svg_file_check($data, $file, $filename, $mimes) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if ($ext === 'svg') {
                $data['ext'] = 'svg';
                $data['type'] = 'image/svg+xml';
            }
            return $data;
        }


        /*
         * Loads the translation files
         */

        public function load_plugin_textdomain() {
            load_plugin_textdomain('hashthemes-demo-importer', false, HDI_PATH . '/languages');
        }

        /*
         * WP-ADMIN Menu for importer
         */

        function add_menu() {
            $this->page_hook = add_submenu_page('themes.php', esc_html__('OneClick Demo Install', 'hashthemes-demo-importer'), esc_html__('HashThemes Demo Importer', 'hashthemes-demo-importer'), 'manage_options', 'hdi-demo-importer', array($this, 'display_demos'));
        }

        /*
         *  Overwrite some elementor settings for better demo
         */

        function overwrite_elementor_settings() {
            // Check if Elementor installed and activated
            if (!did_action('elementor/loaded')) {
                return;
            }

            $options = get_option('hdi_elementor_params_overwrite');

            if (!$options) {
                if ('yes' !== get_option('elementor_disable_color_schemes')) {
                    update_option('elementor_disable_color_schemes', 'yes');
                }

                if ('yes' !== get_option('elementor_disable_typography_schemes')) {
                    update_option('elementor_disable_typography_schemes', 'yes');
                }

                if ('active' !== get_option('elementor_experiment-container')) {
                    update_option('elementor_experiment-container', 'active');
                }

                if ('0' !== get_option('elementor_optimized_gutenberg_loading')) {
                    update_option('elementor_optimized_gutenberg_loading', '0');
                }

                if ('inactive' !== get_option('elementor_experiment-block_editor_assets_optimize')) {
                    update_option('elementor_experiment-block_editor_assets_optimize', 'inactive');
                }

                if ('1' !== get_option('elementor_unfiltered_files_upload')) {
                    update_option('elementor_unfiltered_files_upload', '1');
                }

                // Inside the branch, so this is written once instead of on
                // every admin request for the life of the site.
                update_option('hdi_elementor_params_overwrite', 'yes');
            }
        }

        /*
         *  Allow SVG uploads
         *
         *  Demos ship SVG assets, but an SVG can carry script, so only open the
         *  gate for users who are already trusted with unfiltered markup.
         */

        function file_types_to_uploads($file_types) {
            if (!current_user_can('manage_options')) {
                return $file_types;
            }

            $file_types['svg'] = 'image/svg+xml';
            return $file_types;
        }

        /*
         *  Display the available demos
         */

        function display_demos() {
            $config = $this->get_config();
            ?>
            <div class="hdi-demo-importer-wrap">
                <h2 class="hdi-demo-importer-title">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="71.83 71.83 379.17 377.43">
                        <defs>
                            <linearGradient id="b" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#4e73d8" />
                                <stop offset="100%" stop-color="#3f62c5" />
                            </linearGradient>
                            <linearGradient id="a" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#3552a7" />
                                <stop offset="100%" stop-color="#29458f" />
                            </linearGradient>
                            <filter id="c" x="-30%" y="-30%" width="160%" height="160%">
                                <feDropShadow dx="0" dy="12" stdDeviation="12" flood-color="#000" flood-opacity=".18" />
                            </filter>
                        </defs>
                        <g transform="rotate(-5 3059.682 -2803.682)">
                            <rect x="-170" y="-170" width="340" height="340" rx="42" fill="url(#a)" transform="translate(10 10)" />
                            <rect x="-170" y="-170" width="340" height="340" rx="42" fill="url(#b)" filter="url(#c)" />
                            <path d="M-45-100v200m90-200v200M-105-35h210m-210 70h210" fill="none" stroke="#fff" stroke-width="20" />
                        </g>
                    </svg>
                    <?php echo esc_html__('HashThemes OneClick Demo Importer', 'hashthemes-demo-importer'); ?>
                </h2>

                <div class="hdi-demo-importer-container">
                    <div class="wrap">
                        <h1></h1>
                    </div>

                    <?php
                    if (is_array($config) && !is_null($config) && !empty($config)) {
                        $tags = $pagebuilders = array();
                        foreach ($config as $demo_slug => $demo_pack) {
                            if (isset($demo_pack['tags']) && is_array($demo_pack['tags'])) {
                                foreach ($demo_pack['tags'] as $key => $tag) {
                                    $tags[$key] = $tag;
                                }
                            }
                        }

                        foreach ($config as $demo_slug => $demo_pack) {
                            if (isset($demo_pack['pagebuilder']) && is_array($demo_pack['pagebuilder'])) {
                                foreach ($demo_pack['pagebuilder'] as $key => $pagebuilder) {
                                    $pagebuilders[$key] = $pagebuilder;
                                }
                            }
                        }
                        asort($tags);
                        asort($pagebuilders);

                        // The bar is rendered even for a theme that defines no
                        // tags, because the search box and the grid layout both
                        // hang off it.
                        if (count($config) > 1 || !empty($tags) || !empty($pagebuilders)) {
                            ?>
                            <div class="hdi-tab-filter hdi-clearfix">
                                <?php
                                if (!empty($tags)) {
                                    ?>
                                    <div class="hdi-tab-group hdi-tag-group" data-filter-group="tag">
                                        <button type="button" class="hdi-tab" data-filter="*" aria-pressed="true">
                                            <?php esc_html_e('All', 'hashthemes-demo-importer'); ?>
                                        </button>
                                        <?php
                                        foreach ($tags as $key => $value) {
                                            ?>
                                            <button type="button" class="hdi-tab" data-filter=".<?php echo esc_attr($key); ?>" aria-pressed="false">
                                                <?php echo esc_html($value); ?>
                                            </button>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }

                                if (!empty($pagebuilders)) {
                                    ?>
                                    <div class="hdi-tab-group hdi-pagebuilder-group" data-filter-group="pagebuilder">
                                        <button type="button" class="hdi-tab" data-filter="*" aria-pressed="true">
                                            <?php esc_html_e('All', 'hashthemes-demo-importer'); ?>
                                        </button>
                                        <?php
                                        foreach ($pagebuilders as $key => $value) {
                                            ?>
                                            <button type="button" class="hdi-tab" data-filter=".<?php echo esc_attr($key); ?>" aria-pressed="false">
                                                <?php echo esc_html($value); ?>
                                            </button>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                <?php }
                                ?>

                                <div class="hdi-demo-search">
                                    <label class="screen-reader-text" for="hdi-demo-search-input"><?php esc_html_e('Search demos', 'hashthemes-demo-importer'); ?></label>
                                    <input type="search" id="hdi-demo-search-input" placeholder="<?php esc_attr_e('Search demos...', 'hashthemes-demo-importer'); ?>" />
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <div class="hdi-demo-box-wrap wp-clearfix">
                            <?php
                            // Loop through Demos
                            foreach ($config as $demo_slug => $demo_pack) {
                                $tags = $pagebuilders = '';
                                if (isset($demo_pack['tags'])) {
                                    $tags = implode(' ', array_keys($demo_pack['tags']));
                                }

                                if (isset($demo_pack['pagebuilder'])) {
                                    $pagebuilders = implode(' ', array_keys($demo_pack['pagebuilder']));
                                }

                                $classes = $tags . ' ' . $pagebuilders;

                                $type = isset($demo_pack['type']) ? $demo_pack['type'] : 'free';
                                ?>
                                <div id="<?php echo esc_attr($demo_slug); ?>" class="hdi-demo-box <?php echo esc_attr($classes); ?>">
                                    <div class="hdi-demo-elements">
                                        <?php if ($type == 'pro') { ?>
                                            <div class="hdi-ribbon"><span>Premium</span></div>
                                        <?php } ?>

                                        <img src="<?php echo isset($demo_pack['image']) ? esc_url($demo_pack['image']) : ''; ?>" alt="<?php echo esc_attr($demo_pack['name']); ?>" loading="lazy">

                                        <div class="hdi-demo-actions">

                                            <h4><?php echo esc_html($demo_pack['name']); ?></h4>

                                            <div class="hdi-demo-buttons">
                                                <a href="<?php echo isset($demo_pack['preview_url']) ? esc_url($demo_pack['preview_url']) : '#'; ?>" target="_blank" class="button button-primary">
                                                    <?php echo esc_html__('Preview', 'hashthemes-demo-importer'); ?>
                                                </a>

                                                <?php
                                                if ($type == 'pro') {
                                                    $buy_url = isset($demo_pack['buy_url']) ? $demo_pack['buy_url'] : '#';
                                                    ?>
                                                    <a target="_blank" href="<?php echo esc_url($buy_url) ?>" class="button button-primary">
                                                        <?php echo esc_html__('Buy Now', 'hashthemes-demo-importer') ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="#hdi-modal-<?php echo esc_attr($demo_slug) ?>" class="hdi-modal-button button button-primary">
                                                        <?php echo esc_html__('Install', 'hashthemes-demo-importer') ?>
                                                    </a>
                                                <?php }
                                                ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php }
                            ?>
                        </div>
                    <?php } else {
                        ?>
                        <div class="hdi-demo-wrap">
                            <?php esc_html_e("It looks like the config file for the demos is missing or contains errors. The demo install can't go any further!", 'hashthemes-demo-importer'); ?>
                        </div>
                    <?php }
                    ?>


                    <?php
                    /* Demo Modals */
                    if (is_array($config) && !is_null($config)) {
                        foreach ($config as $demo_slug => $demo_pack) {
                            ?>
                            <div id="hdi-modal-<?php echo esc_attr($demo_slug) ?>" class="hdi-modal" style="display: none;">

                                <div class="hdi-modal-header">
                                    <h2><?php printf(esc_html__('Import Demo - %s', 'hashthemes-demo-importer'), esc_html($demo_pack['name'])); ?></h2>
                                    <button type="button" class="hdi-modal-back" aria-label="<?php esc_attr_e('Close', 'hashthemes-demo-importer'); ?>"><span class="dashicons dashicons-no-alt"></span></button>
                                </div>
                                <div class="hdi-modal-container">
                                    <div class="hdi-modal-wrap">
                                        <p><?php echo sprintf(esc_html__('We recommend you backup your website content before attempting to import the demo so that you can recover your website if something goes wrong. You can use %s plugin for it.', 'hashthemes-demo-importer'), '<a href="https://wordpress.org/plugins/all-in-one-wp-migration/" target="_blank">' . esc_html__('All in one migration', 'hashthemes-demo-importer') . '</a>'); ?></p>

                                        <p><?php echo esc_html__('This process will install all the required plugins, import contents and setup customizer and theme options.', 'hashthemes-demo-importer'); ?></p>

                                        <div class="hdi-modal-recommended-plugins">
                                            <h4><?php esc_html_e('Required Plugins', 'hashthemes-demo-importer'); ?></h4>
                                            <p><?php esc_html_e('For your website to look exactly like the demo,the import process will install and activate the following plugin if they are not installed or activated.', 'hashthemes-demo-importer'); ?></p>
                                            <?php
                                            $plugins = isset($demo_pack['plugins']) ? $demo_pack['plugins'] : '';

                                            if (is_array($plugins)) {
                                                ?>
                                                <ul class="hdi-plugin-status">
                                                    <?php
                                                    foreach ($plugins as $plugin) {
                                                        $name = isset($plugin['name']) ? $plugin['name'] : '';
                                                        $status = HDI_Demo_Importer::plugin_active_status($plugin['file_path']);
                                                        if ($status == 'active') {
                                                            $plugin_class = '<span class="dashicons dashicons-yes-alt"></span>';
                                                        } else if ($status == 'inactive') {
                                                            $plugin_class = '<span class="dashicons dashicons-warning"></span>';
                                                        } else {
                                                            $plugin_class = '<span class="dashicons dashicons-dismiss"></span>';
                                                        }
                                                        ?>
                                                        <li class="hdi-<?php echo esc_attr($status); ?>">
                                                            <?php
                                                            echo wp_kses_post($plugin_class) . ' ' . esc_html($name) . ' - <i>' . esc_html($this->get_plugin_status($status)) . '</i>';
                                                            ?>
                                                        </li>
                                                    <?php }
                                                    ?>
                                                </ul>
                                                <?php
                                            } else {
                                                ?>
                                                <ul>
                                                    <li><?php esc_html_e('No Required Plugins Found.', 'hashthemes-demo-importer'); ?></li>
                                                </ul>
                                                <?php
                                            }
                                            ?>
                                        </div>

                                        <div class="hdi-preflight" data-demo-slug="<?php echo esc_attr($demo_slug); ?>">
                                            <h4><?php esc_html_e('Before you start', 'hashthemes-demo-importer'); ?></h4>
                                            <p><?php esc_html_e('A quick look at whether this server can handle the import. Warnings do not stop you, but they are the usual reason an import fails halfway.', 'hashthemes-demo-importer'); ?></p>
                                            <ul class="hdi-preflight-list">
                                                <?php
                                                foreach ($this->get_server_checks() as $check) {
                                                    ?>
                                                    <li class="hdi-check hdi-check-<?php echo esc_attr($check['status']); ?>">
                                                        <span class="hdi-check-label"><?php echo esc_html($check['label']); ?></span>
                                                        <span class="hdi-check-value"><?php echo esc_html($check['value']); ?></span>
                                                        <?php if ('ok' !== $check['status'] && $check['message']) { ?>
                                                            <span class="hdi-check-message"><?php echo esc_html($check['message']); ?></span>
                                                        <?php } ?>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                                <li class="hdi-check hdi-check-pending hdi-check-remote">
                                                    <span class="hdi-check-label"><?php esc_html_e('Demo package', 'hashthemes-demo-importer'); ?></span>
                                                    <span class="hdi-check-value"><?php esc_html_e('Checking...', 'hashthemes-demo-importer'); ?></span>
                                                    <span class="hdi-check-message"></span>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="hdi-import-parts">
                                            <h4><?php esc_html_e('What to Import', 'hashthemes-demo-importer'); ?></h4>
                                            <p><?php esc_html_e('Everything is imported by default. Uncheck anything you would rather keep as it is.', 'hashthemes-demo-importer'); ?></p>
                                            <ul class="hdi-import-parts-list">
                                                <?php
                                                foreach ($this->get_import_parts() as $part_key => $part_label) {
                                                    ?>
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" class="hdi-import-part" value="<?php echo esc_attr($part_key); ?>" checked="checked" />
                                                            <?php echo esc_html($part_label); ?>
                                                        </label>
                                                    </li>
                                                    <?php
                                                }
                                                ?>
                                            </ul>
                                        </div>

                                        <div class="hdi-exclude-image-checkbox">
                                            <h4><?php esc_html_e('Exclude Images', 'hashthemes-demo-importer') ?></h4>
                                            <p><?php esc_html_e('Check this option if importing demo fails multiple times. Excluding image will make the demo import process super quick.', 'hashthemes-demo-importer') ?></p>
                                            <label>
                                                <input id="checkbox-exclude-image-<?php echo esc_attr($demo_slug); ?>" type="checkbox" value='1' />
                                                <?php echo esc_html__('Yes, Exclude Images', 'hashthemes-demo-importer'); ?>
                                            </label>
                                        </div>

                                        <div class="hdi-reset-checkbox">
                                            <h4><?php esc_html_e('Reset Website', 'hashthemes-demo-importer') ?></h4>
                                            <p><?php esc_html_e('Reseting the website will delete all your post, pages, custom post types, categories, taxonomies, images and all other customizer and theme option settings.', 'hashthemes-demo-importer') ?></p>
                                            <p><?php esc_html_e('It is always recommended to reset the database for a complete demo import.', 'hashthemes-demo-importer') ?></p>
                                            <label class="hdi-reset-website-checkbox">
                                                <input id="checkbox-reset-<?php echo esc_attr($demo_slug); ?>" type="checkbox" value='1' checked="checked" />
                                                <?php echo esc_html__('Reset Website - Check this box only if you are sure to reset the website.', 'hashthemes-demo-importer'); ?>
                                            </label>
                                        </div>

                                        <a href="javascript:void(0)" data-demo-slug="<?php echo esc_attr($demo_slug) ?>" class="button button-primary hdi-import-demo"><?php esc_html_e('Import Demo', 'hashthemes-demo-importer'); ?></a>
                                        <a href="javascript:void(0)" class="button hdi-modal-cancel"><?php esc_html_e('Cancel', 'hashthemes-demo-importer'); ?></a>
                                    </div>

                                    <div class="hdi-modal-preview">
                                        <div class="hdi-demo-elements">
                                            <img src="<?php echo isset($demo_pack['image']) ? esc_url($demo_pack['image']) : ''; ?>" alt="<?php echo esc_attr($demo_pack['name']); ?>">
                                            <div class="hdi-demo-actions">
                                                <h4><?php echo esc_html($demo_pack['name']); ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                    <div id="hdi-import-progress" style="display: none">
                        <h2 class="hdi-import-progress-header"><?php echo esc_html__('Demo Import Progress', 'hashthemes-demo-importer'); ?></h2>
                        <div class="hdi-import-progress-container">
                            <div class="hdi-import-progress-wrap">
                                <div class="hdi-import-loader">
                                    <div class="hdi-loader-content">
                                        <div class="hdi-loader-content-inside">
                                            <div class="hdi-loader-rotater"></div>
                                            <div class="hdi-loader-line-point"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="hdi-import-progress-message"></div>

                                <div class="hdi-import-progress-bar">
                                    <div class="hdi-import-progress-bar-fill" style="width: 0%;"></div>
                                </div>
                                <div class="hdi-import-progress-step"></div>

                                <ul class="hdi-import-steps">
                                    <?php
                                    foreach ($this->get_import_steps() as $step) {
                                        ?>
                                        <li class="hdi-import-step hdi-step-pending" data-step="<?php echo esc_attr($step['action']); ?>">
                                            <span class="hdi-import-step-label"><?php echo esc_html($step['label']); ?></span>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>

                                <div class="hdi-import-actions">
                                    <button type="button" class="button button-primary hdi-import-retry"><?php esc_html_e('Try Again', 'hashthemes-demo-importer'); ?></button>
                                    <a class="button hdi-import-back" href="<?php echo esc_url(add_query_arg('page', 'hdi-demo-importer', admin_url('themes.php'))); ?>"><?php esc_html_e('Go Back', 'hashthemes-demo-importer'); ?></a>
                                    <button type="button" class="button-link hdi-import-details-toggle"><?php esc_html_e('Show error details', 'hashthemes-demo-importer'); ?></button>
                                </div>

                                <pre class="hdi-import-details" style="display: none;"></pre>
                            </div>

                            <div class="hdi-import-preview">
                                <div class="hdi-demo-elements">
                                    <img src="" alt="">
                                    <div class="hdi-demo-actions">
                                        <h4></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }

        /*
         *  The parts of a demo a user can choose to import. Plugins and the
         *  download are not listed: nothing else works without them.
         */

        public function get_import_parts() {
            return array(
                'content' => esc_html__('Posts, pages, media & menus', 'hashthemes-demo-importer'),
                'customizer' => esc_html__('Customizer settings', 'hashthemes-demo-importer'),
                'options' => esc_html__('Theme options', 'hashthemes-demo-importer'),
                'widgets' => esc_html__('Widgets', 'hashthemes-demo-importer'),
                'forms' => esc_html__('Forms', 'hashthemes-demo-importer'),
            );
        }

        /*
         *  Reads the chosen parts off the request. A request that says nothing
         *  about parts imports everything, so older callers keep working.
         */

        public function get_selected_parts() {
            $valid = array_keys($this->get_import_parts());

            if (!isset($_POST['parts'])) {
                return $valid;
            }

            $parts = sanitize_text_field(wp_unslash($_POST['parts']));
            $parts = array_filter(array_map('trim', explode(',', $parts)));

            return array_values(array_intersect($parts, $valid));
        }

        public function is_part_selected($part, $parts) {
            return is_array($parts) && in_array($part, $parts, true);
        }

        /*
         *  The import chain in order, with a short name for each step. This is
         *  the one place the order is defined: the progress checklist, the
         *  progress bar and the wp-cli command all read it from here.
         */

        public function get_import_steps() {
            return array(
                array('action' => 'hdi_install_demo', 'label' => esc_html__('Preparing', 'hashthemes-demo-importer')),
                array('action' => 'hdi_install_plugin', 'label' => esc_html__('Installing plugins', 'hashthemes-demo-importer')),
                array('action' => 'hdi_activate_plugin', 'label' => esc_html__('Activating plugins', 'hashthemes-demo-importer')),
                array('action' => 'hdi_download_files', 'label' => esc_html__('Downloading demo files', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_xml', 'label' => esc_html__('Posts, pages & media', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_customizer', 'label' => esc_html__('Customizer settings', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_menu', 'label' => esc_html__('Menus', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_theme_option', 'label' => esc_html__('Theme options', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_widget', 'label' => esc_html__('Widgets', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_hashform', 'label' => esc_html__('Forms', 'hashthemes-demo-importer')),
                array('action' => 'hdi_import_revslider', 'label' => esc_html__('Revolution Slider', 'hashthemes-demo-importer')),
                array('action' => 'hdi_custom_import_hook', 'label' => esc_html__('Finishing up', 'hashthemes-demo-importer')),
            );
        }

        /* ===== Import log =====
         *
         *  The importers write warnings to the output buffer, which used to be
         *  thrown away. Keeping it means a failed import can say why.
         */

        public function reset_log() {
            delete_transient('hdi_import_log');
        }

        public function log($message) {
            $message = trim(wp_strip_all_tags((string) $message));

            if (!$message) {
                return;
            }

            $log = get_transient('hdi_import_log');

            if (!is_array($log)) {
                $log = array();
            }

            $log[] = current_time('H:i:s') . ' - ' . $message;

            // Bound it: a noisy import should not fill up the options table.
            if (count($log) > 200) {
                $log = array_slice($log, -200);
            }

            set_transient('hdi_import_log', $log, DAY_IN_SECONDS);
        }

        public function get_log_text() {
            $log = get_transient('hdi_import_log');

            return is_array($log) ? implode("\n", $log) : '';
        }

        /*
         *  Runs a callback with the output buffer captured into the log, so a
         *  notice printed by an importer is kept instead of discarded.
         */

        public function capture($callback) {
            ob_start();
            $returned = call_user_func($callback);
            $output = ob_get_clean();

            if (trim($output)) {
                $this->log($output);
            }

            return $returned;
        }

        public function result($message, $error = false, $skipped = false) {
            $this->log(($error ? 'ERROR: ' : '') . $message);

            return array('error' => (bool) $error, 'message' => $message, 'skipped' => (bool) $skipped);
        }

        /* ===== Import runners =====
         *
         *  One method per step, each returning array('error', 'message'). The
         *  ajax steps and the wp-cli command both drive these, so the two can
         *  never drift apart.
         */

        public function run_reset() {
            $this->database_reset();

            return $this->result(esc_html__('Database reset complete', 'hashthemes-demo-importer'));
        }

        public function run_install_plugins($slug) {
            $this->capture(function () use ($slug) {
                $this->install_plugins($slug);
            });

            if ($this->plugin_install_count > 0) {
                return $this->result(esc_html__('All the required plugins installed', 'hashthemes-demo-importer'));
            }

            return $this->result(esc_html__('No plugin required to install', 'hashthemes-demo-importer'));
        }

        public function run_activate_plugins($slug) {
            $this->capture(function () use ($slug) {
                $this->activate_plugins($slug);
            });

            if ($this->plugin_active_count > 0) {
                return $this->result(esc_html__('All the required plugins activated', 'hashthemes-demo-importer'));
            }

            return $this->result(esc_html__('No plugin required to activate', 'hashthemes-demo-importer'));
        }

        public function run_download($slug) {
            $demo = $this->get_demo($slug);
            $external_url = isset($demo['external_url']) ? $demo['external_url'] : '';

            if ($this->download_files($external_url)) {
                return $this->result(esc_html__('All demo files downloaded', 'hashthemes-demo-importer'));
            }

            return $this->result(esc_html__('Demo import process failed. Demo files can not be downloaded', 'hashthemes-demo-importer'), true);
        }

        public function run_content($slug, $excludeImages, $parts) {
            if (!$this->is_part_selected('content', $parts)) {
                return $this->result(esc_html__('Content import skipped', 'hashthemes-demo-importer'), false, true);
            }

            if ('1' !== get_option('elementor_unfiltered_files_upload')) {
                update_option('elementor_unfiltered_files_upload', '1');
            }

            $xml_filepath = $this->demo_upload_dir($slug) . '/content.xml';

            if (!file_exists($xml_filepath)) {
                return $this->result(esc_html__('Demo import process failed. No content file found', 'hashthemes-demo-importer'), true);
            }

            $this->importDemoContent($xml_filepath, $excludeImages, $slug);

            return $this->result(esc_html__('All content imported', 'hashthemes-demo-importer'));
        }

        public function run_customizer($slug, $excludeImages, $parts) {
            if (!$this->is_part_selected('customizer', $parts)) {
                return $this->result(esc_html__('Customizer settings skipped', 'hashthemes-demo-importer'), false, true);
            }

            $customizer_filepath = $this->demo_upload_dir($slug) . '/customizer.dat';

            if (!file_exists($customizer_filepath)) {
                return $this->result(esc_html__('No customizer settings found', 'hashthemes-demo-importer'));
            }

            $this->capture(function () use ($customizer_filepath, $excludeImages) {
                HDI_Customizer_Importer::import($customizer_filepath, $excludeImages);
            });

            return $this->result(esc_html__('Customizer settings imported', 'hashthemes-demo-importer'));
        }

        public function run_menus($slug, $parts) {
            if (!$this->is_part_selected('content', $parts)) {
                return $this->result(esc_html__('Menus skipped', 'hashthemes-demo-importer'), false, true);
            }

            $demo = $this->get_demo($slug);
            $menu_array = isset($demo['menu_array']) ? $demo['menu_array'] : '';

            if (!$menu_array) {
                return $this->result(esc_html__('No menus saved', 'hashthemes-demo-importer'));
            }

            $this->setMenu($menu_array);

            return $this->result(esc_html__('Menus saved', 'hashthemes-demo-importer'));
        }

        public function run_theme_options($slug, $parts) {
            if (!$this->is_part_selected('options', $parts)) {
                return $this->result(esc_html__('Theme options skipped', 'hashthemes-demo-importer'), false, true);
            }

            $demo = $this->get_demo($slug);
            $options_array = isset($demo['options_array']) ? $demo['options_array'] : '';

            if (!is_array($options_array)) {
                return $this->result(esc_html__('No theme options found', 'hashthemes-demo-importer'));
            }

            foreach ($options_array as $theme_option) {
                $option_filepath = $this->demo_upload_dir($slug) . '/' . $theme_option . '.json';

                if (!file_exists($option_filepath)) {
                    continue;
                }

                $data = file_get_contents($option_filepath);

                if (!$data) {
                    continue;
                }

                $decoded = json_decode($data, true);

                // Never overwrite a live option with null from a truncated or
                // malformed json file.
                if (!is_null($decoded)) {
                    update_option($theme_option, $decoded);
                }
            }

            return $this->result(esc_html__('Theme options settings imported', 'hashthemes-demo-importer'));
        }

        public function run_widgets($slug, $parts) {
            if (!$this->is_part_selected('widgets', $parts)) {
                return $this->result(esc_html__('Widgets skipped', 'hashthemes-demo-importer'), false, true);
            }

            $widget_filepath = $this->demo_upload_dir($slug) . '/widget.wie';

            if (!file_exists($widget_filepath)) {
                return $this->result(esc_html__('No widgets found', 'hashthemes-demo-importer'));
            }

            $this->capture(function () use ($widget_filepath) {
                HDI_Widget_Importer::import($widget_filepath);
            });

            return $this->result(esc_html__('Widgets imported', 'hashthemes-demo-importer'));
        }

        public function run_hash_forms($slug, $parts) {
            if (!$this->is_part_selected('forms', $parts)) {
                return $this->result(esc_html__('Forms skipped', 'hashthemes-demo-importer'), false, true);
            }

            $demo = $this->get_demo($slug);
            $hash_forms = isset($demo['hash_forms']) ? $demo['hash_forms'] : '';

            if (!is_array($hash_forms) || !$hash_forms) {
                return $this->result(esc_html__('No Form files found', 'hashthemes-demo-importer'));
            }

            if (!class_exists('HashFormBuilder')) {
                return $this->result(esc_html__('Hash Form plugin not installed', 'hashthemes-demo-importer'));
            }

            if (class_exists('HashFormCreateTable')) {
                new HashFormCreateTable();
            }

            foreach ($hash_forms as $hash_form) {
                $filepath = $this->demo_upload_dir($slug) . '/' . $hash_form . '.json';

                if (!file_exists($filepath)) {
                    continue;
                }

                $imdat = json_decode(file_get_contents($filepath), true);

                // A malformed export used to fatal on the missing keys.
                if (!is_array($imdat) || !isset($imdat['options']) || !is_array($imdat['options'])) {
                    continue;
                }

                $options = $imdat['options'];

                $form = array(
                    'name' => isset($options['title']) ? esc_html($options['title']) : '',
                    'description' => isset($options['description']) ? esc_html($options['description']) : '',
                    'options' => $options,
                    'status' => isset($imdat['status']) ? $imdat['status'] : '',
                    'settings' => isset($imdat['settings']) ? $imdat['settings'] : '',
                    'styles' => isset($imdat['styles']) ? $imdat['styles'] : '',
                    'created_at' => current_time('mysql'),
                );

                $form_id = HashFormBuilder::create($form);

                if (!isset($imdat['field']) || !is_array($imdat['field']) || !class_exists('HashFormFields')) {
                    continue;
                }

                foreach ($imdat['field'] as $field) {
                    if (!is_array($field)) {
                        continue;
                    }

                    HashFormFields::create_row(
                        array(
                            'name' => isset($field['name']) ? $field['name'] : '',
                            'description' => isset($field['description']) ? $field['description'] : '',
                            'type' => isset($field['type']) ? $field['type'] : '',
                            'default_value' => isset($field['default_value']) ? $field['default_value'] : '',
                            'options' => isset($field['options']) ? $field['options'] : '',
                            'field_order' => isset($field['field_order']) ? $field['field_order'] : 0,
                            'form_id' => absint($form_id),
                            'required' => isset($field['required']) ? $field['required'] : 0,
                            'field_options' => isset($field['field_options']) ? $field['field_options'] : ''
                        )
                    );
                }
            }

            return $this->result(esc_html__('Forms imported', 'hashthemes-demo-importer'));
        }

        public function has_revslider_file($slug) {
            return file_exists($this->demo_upload_dir($slug) . '/revslider.zip');
        }

        public function run_revslider($slug, $parts) {
            if (!$this->is_part_selected('content', $parts)) {
                return $this->result(esc_html__('Revolution slider skipped', 'hashthemes-demo-importer'), false, true);
            }

            $sliderFile = $this->demo_upload_dir($slug) . '/revslider.zip';

            if (!class_exists('RevSlider')) {
                return $this->result(esc_html__('Revolution slider plugin not installed', 'hashthemes-demo-importer'));
            }

            $this->capture(function () use ($sliderFile) {
                $slider = new RevSlider();
                $slider->importSliderFromPost(true, true, $sliderFile);
            });

            return $this->result(esc_html__('Revolution slider installed', 'hashthemes-demo-importer'));
        }

        /*
         *  Last step: hand over to anything hooked in, then tidy up after
         *  ourselves.
         */

        public function run_finalize($slug) {
            do_action('hdi_after_demo_import', array(
                'slug' => $slug,
                'file_path' => $this->demo_upload_dir($slug)
            ));

            $this->cleanup_after_import($slug);

            update_option('hdi_last_imported_demo', array(
                'slug' => $slug,
                'time' => current_time('mysql'),
            ));

            return $this->result(esc_html__('Completed', 'hashthemes-demo-importer'));
        }

        /*
         *  Post import housekeeping. Permalinks in particular: imported pages
         *  404 until the rewrite rules are rebuilt.
         */

        public function cleanup_after_import($slug) {
            flush_rewrite_rules();

            if (did_action('elementor/loaded') && isset(Elementor\Plugin::$instance->files_manager)) {
                Elementor\Plugin::$instance->files_manager->clear_cache();
            }

            // Themes that need the demo files afterwards can keep them.
            if (apply_filters('hdi_delete_demo_files', true, $slug)) {
                $this->clear_uploads($this->demo_upload_dir());
            }

            wp_cache_flush();
        }

        /*
         *  Shared guard for every import step. Bails with an error the script
         *  can display instead of returning an empty response, which would
         *  leave the progress screen spinning forever.
         */

        public function verify_ajax_request() {
            if (!current_user_can('manage_options')) {
                $this->send_ajax_error(esc_html__('You are not allowed to import demos on this site.', 'hashthemes-demo-importer'));
            }

            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field(wp_unslash($_POST['demo'])) : '';

            if (!$this->get_demo($demo_slug)) {
                $this->send_ajax_error(esc_html__('Demo import process failed. The selected demo could not be found.', 'hashthemes-demo-importer'));
            }

            return $demo_slug;
        }

        public function get_exclude_images() {
            return isset($_POST['excludeImages']) ? sanitize_text_field(wp_unslash($_POST['excludeImages'])) : '';
        }

        /*
         *  Finishes an ajax step: reports what happened and where to go next.
         */

        public function send_step($result, $demo_slug, $next_step = '', $next_step_message = '') {
            if (!empty($result['error'])) {
                $this->send_ajax_error($result['message']);
            }

            $this->ajax_response['complete_message'] = $result['message'];
            $this->ajax_response['skipped'] = !empty($result['skipped']);
            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['excludeImages'] = $this->get_exclude_images();
            $this->ajax_response['next_step'] = $next_step;
            $this->ajax_response['next_step_message'] = $next_step_message;
            $this->send_ajax_response();
        }

        /*
         *  Do the install on ajax call
         */

        function install_demo_process() {
            $demo_slug = $this->verify_ajax_request();

            // A fresh run, so previous diagnostics are not mixed in.
            $this->reset_log();
            $this->log(sprintf('Import started: %s', $demo_slug));

            $result = $this->result('');

            if (isset($_POST['reset']) && $_POST['reset'] == 'true') {
                $result = $this->run_reset();
            }

            $this->send_step($result, $demo_slug, 'hdi_install_plugin', esc_html__('Installing required plugins', 'hashthemes-demo-importer'));
        }

        function install_plugin_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_install_plugins($demo_slug);

            $this->send_step($result, $demo_slug, 'hdi_activate_plugin', esc_html__('Activating required plugins', 'hashthemes-demo-importer'));
        }

        function activate_plugin_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_activate_plugins($demo_slug);

            $this->send_step($result, $demo_slug, 'hdi_download_files', esc_html__('Downloading demo files', 'hashthemes-demo-importer'));
        }

        function download_files_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_download($demo_slug);

            $this->send_step($result, $demo_slug, 'hdi_import_xml', esc_html__('Importing posts, pages and medias. It may take a bit longer time', 'hashthemes-demo-importer'));
        }

        function import_xml_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_content($demo_slug, $this->get_exclude_images(), $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_import_customizer', esc_html__('Importing customizer settings', 'hashthemes-demo-importer'));
        }

        function import_customizer_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_customizer($demo_slug, $this->get_exclude_images(), $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_import_menu', esc_html__('Setting menus', 'hashthemes-demo-importer'));
        }

        function import_menu_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_menus($demo_slug, $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_import_theme_option', esc_html__('Importing theme option settings', 'hashthemes-demo-importer'));
        }

        function import_theme_option_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_theme_options($demo_slug, $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_import_widget', esc_html__('Importing widgets', 'hashthemes-demo-importer'));
        }

        function import_widget_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_widgets($demo_slug, $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_import_hashform', esc_html__('Importing Forms', 'hashthemes-demo-importer'));
        }

        function import_hashform_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_hash_forms($demo_slug, $this->get_selected_parts());

            // The slider step only exists when the demo shipped one.
            if ($this->has_revslider_file($demo_slug)) {
                $this->send_step($result, $demo_slug, 'hdi_import_revslider', esc_html__('Importing Revolution slider', 'hashthemes-demo-importer'));
            }

            $this->send_step($result, $demo_slug, 'hdi_custom_import_hook', esc_html__('Completing Final Settings', 'hashthemes-demo-importer'));
        }

        function import_revslider_process() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_revslider($demo_slug, $this->get_selected_parts());

            $this->send_step($result, $demo_slug, 'hdi_custom_import_hook', esc_html__('Completing Final Settings', 'hashthemes-demo-importer'));
        }

        function add_custom_import_hook() {
            $demo_slug = $this->verify_ajax_request();
            $result = $this->run_finalize($demo_slug);

            $this->ajax_response['complete_message'] = $result['message'];
            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = '';
            $this->ajax_response['next_step_message'] = '';

            // Only the last step sets this. The script needs a positive signal
            // that the import finished, otherwise a cut off or unexpected
            // response would read as "no next step" and look like success.
            $this->ajax_response['completed'] = true;
            $this->send_ajax_response();
        }

        /*
         *  Hands the collected log back so a failed import can show what went
         *  wrong. Reachable even when the failure left no usable response.
         */

        function import_log_process() {
            if (!current_user_can('manage_options')) {
                $this->send_ajax_error(esc_html__('You are not allowed to import demos on this site.', 'hashthemes-demo-importer'));
            }

            check_ajax_referer('demo-importer-ajax', 'security');

            $this->ajax_response['log'] = $this->get_log_text();
            $this->send_ajax_response();
        }

        /*
         *  Checks the server can realistically run an import, and that the demo
         *  package is actually reachable, before anything is overwritten.
         */

        function preflight_process() {
            $demo_slug = $this->verify_ajax_request();
            $demo = $this->get_demo($demo_slug);
            $external_url = isset($demo['external_url']) ? $demo['external_url'] : '';

            $this->ajax_response['checks'] = $this->get_remote_checks($external_url);
            $this->send_ajax_response();
        }

        /*
         *  Server side checks. These are cheap and need no network, so the
         *  modal can render them with the page.
         */

        public function get_server_checks() {
            $checks = array();

            $memory = $this->bytes_from_ini(ini_get('memory_limit'));
            $checks[] = array(
                'label' => esc_html__('PHP memory limit', 'hashthemes-demo-importer'),
                'value' => ini_get('memory_limit'),
                // -1 means no limit at all, which is fine.
                'status' => ($memory < 0 || $memory >= 268435456) ? 'ok' : 'warning',
                'message' => esc_html__('256M or more is recommended. A large demo can run out of memory below that.', 'hashthemes-demo-importer'),
            );

            $execution = (int) ini_get('max_execution_time');
            $checks[] = array(
                'label' => esc_html__('Max execution time', 'hashthemes-demo-importer'),
                'value' => $execution ? $execution . 's' : esc_html__('Unlimited', 'hashthemes-demo-importer'),
                'status' => (0 === $execution || $execution >= 300) ? 'ok' : 'warning',
                'message' => esc_html__('300 seconds or more is recommended. Importing media is the slow part.', 'hashthemes-demo-importer'),
            );

            $checks[] = array(
                'label' => esc_html__('PHP version', 'hashthemes-demo-importer'),
                'value' => PHP_VERSION,
                'status' => version_compare(PHP_VERSION, '7.4', '>=') ? 'ok' : 'warning',
                'message' => esc_html__('PHP 7.4 or newer is recommended.', 'hashthemes-demo-importer'),
            );

            // On a site that has never had an upload the folder does not exist
            // yet, so fall back to asking whether we could create it.
            $uploads_dir = $this->uploads_dir['basedir'];
            $uploads_writable = file_exists($uploads_dir) ? wp_is_writable($uploads_dir) : wp_is_writable(dirname($uploads_dir));
            $checks[] = array(
                'label' => esc_html__('Uploads folder', 'hashthemes-demo-importer'),
                'value' => $uploads_writable ? esc_html__('Writable', 'hashthemes-demo-importer') : esc_html__('Not writable', 'hashthemes-demo-importer'),
                'status' => $uploads_writable ? 'ok' : 'error',
                'message' => esc_html__('The demo package is unpacked here, so the import cannot run without write access.', 'hashthemes-demo-importer'),
            );

            $checks[] = array(
                'label' => esc_html__('Zip support', 'hashthemes-demo-importer'),
                'value' => class_exists('ZipArchive') ? esc_html__('ZipArchive', 'hashthemes-demo-importer') : esc_html__('Fallback', 'hashthemes-demo-importer'),
                'status' => 'ok',
                'message' => '',
            );

            return apply_filters('hdi_server_checks', $checks);
        }

        /*
         *  Confirms the demo zip is really there. Split out from the server
         *  checks because it costs a request.
         */

        public function get_remote_checks($external_url) {
            $check = array(
                'label' => esc_html__('Demo package', 'hashthemes-demo-importer'),
                'value' => esc_html__('Not reachable', 'hashthemes-demo-importer'),
                'status' => 'error',
                'message' => esc_html__('The demo files could not be reached. Check your connection and try again later.', 'hashthemes-demo-importer'),
            );

            if (!$external_url) {
                $check['message'] = esc_html__('This demo has no package url set in the config file.', 'hashthemes-demo-importer');
                return array($check);
            }

            $response = wp_remote_head($external_url, array('timeout' => 15, 'redirection' => 5));
            $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);

            // Some servers refuse HEAD. Ask for the first byte instead.
            if (200 !== $code) {
                $response = wp_remote_get($external_url, array(
                    'timeout' => 15,
                    'redirection' => 5,
                    'headers' => array('Range' => 'bytes=0-1'),
                ));
                $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
            }

            if (200 === $code || 206 === $code) {
                $size = (int) wp_remote_retrieve_header($response, 'content-length');
                $check['status'] = 'ok';
                $check['value'] = ($size > 1 && 200 === $code) ? size_format($size) : esc_html__('Reachable', 'hashthemes-demo-importer');
                $check['message'] = '';
            } else if (is_wp_error($response)) {
                $check['message'] = $response->get_error_message();
            }

            return array($check);
        }

        /*
         *  ini_get returns things like "256M". Returns bytes, or -1 for no
         *  limit.
         */

        public function bytes_from_ini($value) {
            $value = trim((string) $value);

            if ('' === $value) {
                return 0;
            }

            $bytes = (int) $value;

            if ($bytes < 0) {
                return -1;
            }

            switch (strtolower(substr($value, -1))) {
                case 'g':
                    $bytes *= 1024;
                // no break
                case 'm':
                    $bytes *= 1024;
                // no break
                case 'k':
                    $bytes *= 1024;
            }

            return $bytes;
        }

        public function download_files($external_url) {
            // Make sure we have the dependency.
            if (!function_exists('WP_Filesystem')) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
            }

            /*
             * Initialize WordPress' file system handler.
             *
             * @var WP_Filesystem_Base $wp_filesystem
             */
            WP_Filesystem();
            global $wp_filesystem;

            $result = true;

            if (!($wp_filesystem->exists($this->demo_upload_dir()))) {
                $result = $wp_filesystem->mkdir($this->demo_upload_dir());
            }

            // Abort the request if the local uploads directory couldn't be created.
            if (!$result || !$external_url) {
                return false;
            }

            $demo_pack = $this->demo_upload_dir() . 'demo-pack.zip';

            $response = wp_remote_get(
                $external_url, array(
                    'timeout' => 60,
                )
            );

            // A failed download used to be written out as an empty zip, so the
            // import only broke further down the line. Stop here instead.
            if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
                return false;
            }

            $file = wp_remote_retrieve_body($response);

            if (!$file) {
                return false;
            }

            if (!$wp_filesystem->put_contents($demo_pack, $file)) {
                return false;
            }

            $unzipped = unzip_file($demo_pack, $this->demo_upload_dir());
            $wp_filesystem->delete($demo_pack);

            return !is_wp_error($unzipped);
        }

        /*
         * Reset the database, if the case
         */

        function database_reset() {

            global $wpdb;
            $core_tables = array('commentmeta', 'comments', 'links', 'postmeta', 'posts', 'term_relationships', 'term_taxonomy', 'termmeta', 'terms');
            $exclude_core_tables = array('options', 'usermeta', 'users');
            $core_tables = array_map(function ($tbl) {
                global $wpdb;
                return $wpdb->prefix . $tbl;
            }, $core_tables);
            $exclude_core_tables = array_map(function ($tbl) {
                global $wpdb;
                return $wpdb->prefix . $tbl;
            }, $exclude_core_tables);
            $custom_tables = array();

            $table_status = $wpdb->get_results('SHOW TABLE STATUS');
            if (is_array($table_status)) {
                foreach ($table_status as $index => $table) {
                    if (0 !== stripos($table->Name, $wpdb->prefix)) {
                        continue;
                    }
                    if (empty($table->Engine)) {
                        continue;
                    }

                    if (false === in_array($table->Name, $core_tables) && false === in_array($table->Name, $exclude_core_tables)) {
                        $custom_tables[] = $table->Name;
                    }
                }
            }
            $custom_tables = array_merge($core_tables, $custom_tables);

            $wpdb->query('SET foreign_key_checks = 0');

            foreach ($custom_tables as $tbl) {
                $wpdb->query('TRUNCATE TABLE `' . str_replace('`', '', $tbl) . '`');
            }

            // Put the check back so the rest of the import runs normally.
            $wpdb->query('SET foreign_key_checks = 1');

            // Delete Widgets
            global $wp_registered_widget_controls;

            $widget_controls = is_array($wp_registered_widget_controls) ? $wp_registered_widget_controls : array();

            $available_widgets = array();

            foreach ($widget_controls as $widget) {
                if (!empty($widget['id_base']) && !isset($available_widgets[$widget['id_base']])) {
                    $available_widgets[] = $widget['id_base'];
                }
            }

            update_option('sidebars_widgets', array('wp_inactive_widgets' => array()));
            foreach ($available_widgets as $widget_data) {
                update_option('widget_' . $widget_data, array());
            }

            // Delete Thememods
            $theme_slug = get_option('stylesheet');
            $mods = get_option("theme_mods_$theme_slug");
            if (false !== $mods) {
                delete_option("theme_mods_$theme_slug");
            }

            //Clear "uploads" folder
            $this->clear_uploads($this->uploads_dir['basedir']);

            if (did_action('elementor/loaded')) {
                $created_default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
                update_option('elementor_active_kit', $created_default_kit);
            }
        }

        /*
         * Clear "uploads" folder
         * @param string $dir
         * @return bool
         */

        private function clear_uploads($dir) {
            if (!is_dir($dir)) {
                return false;
            }

            $files = scandir($dir);

            if (false === $files) {
                return false;
            }

            foreach (array_diff($files, array('.', '..')) as $file) {
                if (is_dir("$dir/$file")) {
                    $this->clear_uploads("$dir/$file");
                } else if (is_writable("$dir/$file")) {
                    unlink("$dir/$file");
                }
            }

            return ($dir != $this->uploads_dir['basedir']) ? rmdir($dir) : true;
        }

        /*
         * Set the menu on theme location
         */

        function setMenu($menu_array) {

            if (!$menu_array) {
                return;
            }

            $locations = get_theme_mod('nav_menu_locations');

            foreach ($menu_array as $menuId => $menuname) {
                $menu_exists = wp_get_nav_menu_object($menuname);

                if (!$menu_exists) {
                    $term_id_of_menu = wp_create_nav_menu($menuname);
                } else {
                    $term_id_of_menu = $menu_exists->term_id;
                }

                $locations[$menuId] = $term_id_of_menu;
            }

            set_theme_mod('nav_menu_locations', $locations);
        }

        /*
         * Import demo XML content
         */

        function importDemoContent($xml_filepath, $excludeImages, $demo_slug = '') {

            if (!defined('WP_LOAD_IMPORTERS'))
                define('WP_LOAD_IMPORTERS', true);

            if (!class_exists('HDI_Import')) {
                $class_wp_importer = HDI_PATH . "wordpress-importer/wordpress-importer.php";
                if (file_exists($class_wp_importer)) {
                    require_once $class_wp_importer;
                }
            }

            // Import demo content from XML
            if (class_exists('HDI_Import')) {
                // The slug is passed in by the caller; the request fallback is
                // only there for anything still calling this the old way.
                if (!$demo_slug) {
                    $demo_slug = isset($_POST['demo']) ? sanitize_text_field(wp_unslash($_POST['demo'])) : '';
                }

                $excludeImages = $excludeImages == 'true' ? false : true;
                $demo = $this->get_demo($demo_slug);
                $home_slug = isset($demo['home_slug']) ? $demo['home_slug'] : '';
                $blog_slug = isset($demo['blog_slug']) ? $demo['blog_slug'] : '';
                $element_kit_slug = isset($demo['element_kit_slug']) ? $demo['element_kit_slug'] : '';

                if (file_exists($xml_filepath)) {
                    $wp_import = new HDI_Import();
                    $wp_import->fetch_attachments = $excludeImages;

                    // Whatever the importer prints goes to the log, so a failed
                    // import can be explained afterwards.
                    $this->capture(function () use ($wp_import, $xml_filepath) {
                        $wp_import->import($xml_filepath);
                    });
                    // Import DONE
                    // set homepage as front page
                    if ($home_slug) {
                        $page = get_page_by_path($home_slug);
                        if ($page) {
                            update_option('show_on_front', 'page');
                            update_option('page_on_front', $page->ID);
                        }
                    }

                    if ($blog_slug) {
                        $blog = get_page_by_path($blog_slug);
                        if ($blog) {
                            update_option('show_on_front', 'page');
                            update_option('page_for_posts', $blog->ID);
                        }
                    }

                    if (!$home_slug && !$blog_slug) {
                        update_option('show_on_front', 'posts');
                    }

                    if ($element_kit_slug) {
                        $elementor_kit = get_page_by_path($element_kit_slug, OBJECT, 'elementor_library');
                        if ($elementor_kit) {
                            update_option('elementor_active_kit', $elementor_kit->ID);
                        }
                    }
                }
            }
        }

        function demo_upload_dir($path = '') {
            $upload_dir = $this->uploads_dir['basedir'] . '/demo-pack/' . $path;
            return $upload_dir;
        }

        function install_plugins($slug) {
            $demo = $this->get_demo($slug);

            $plugins = isset($demo['plugins']) && is_array($demo['plugins']) ? $demo['plugins'] : array();

            foreach ($plugins as $plugin_slug => $plugin) {
                $source = isset($plugin['source']) ? $plugin['source'] : '';
                $file_path = isset($plugin['file_path']) ? $plugin['file_path'] : '';
                $location = isset($plugin['location']) ? $plugin['location'] : '';

                if ($source == 'wordpress') {
                    $this->plugin_installer_callback($file_path, $plugin_slug);
                } else {
                    $this->plugin_offline_installer_callback($file_path, $location);
                }
            }
        }

        function activate_plugins($slug) {
            $demo = $this->get_demo($slug);

            $plugins = isset($demo['plugins']) && is_array($demo['plugins']) ? $demo['plugins'] : array();

            foreach ($plugins as $plugin_slug => $plugin) {
                $file_path = isset($plugin['file_path']) ? $plugin['file_path'] : '';
                $plugin_status = $this->plugin_status($file_path);

                if ($plugin_status == 'inactive') {
                    $this->activate_plugin($file_path);
                    $this->plugin_active_count++;
                }
            }
        }

        public function plugin_installer_callback($file_path, $slug) {
            $plugin_status = $this->plugin_status($file_path);

            if ($plugin_status == 'install') {
                // Include required libs for installation
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
                require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

                // Get Plugin Info
                $api = $this->call_plugin_api($slug);

                // wordpress.org can be unreachable or the slug can be wrong.
                if (is_wp_error($api) || empty($api->download_link)) {
                    return;
                }

                $skin = new WP_Ajax_Upgrader_Skin();
                $upgrader = new Plugin_Upgrader($skin);
                $upgrader->install($api->download_link);

                $this->activate_plugin($file_path);

                $this->plugin_install_count++;
            }
        }

        public function plugin_offline_installer_callback($file_path, $external_url) {

            $plugin_status = $this->plugin_status($file_path);

            if ($plugin_status == 'install') {
                // Make sure we have the dependency.
                if (!function_exists('WP_Filesystem')) {
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                }

                /*
                 * Initialize WordPress' file system handler.
                 *
                 * @var WP_Filesystem_Base $wp_filesystem
                 */
                WP_Filesystem();
                global $wp_filesystem;

                if (!$external_url) {
                    return;
                }

                $plugin = $this->demo_upload_dir() . 'plugin.zip';

                $response = wp_remote_get(
                    $external_url, array(
                        'timeout' => 60,
                    )
                );

                if (is_wp_error($response) || 200 !== (int) wp_remote_retrieve_response_code($response)) {
                    return;
                }

                $file = wp_remote_retrieve_body($response);

                if (!$file) {
                    return;
                }

                $wp_filesystem->mkdir($this->demo_upload_dir());

                if (!$wp_filesystem->put_contents($plugin, $file)) {
                    return;
                }

                $unzipped = unzip_file($plugin, WP_PLUGIN_DIR);

                $wp_filesystem->delete($plugin);

                if (is_wp_error($unzipped)) {
                    return;
                }

                $this->activate_plugin($file_path);

                $this->plugin_install_count++;
            }
        }

        /* Plugin API */

        public function call_plugin_api($slug) {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

            $call_api = plugins_api(
                'plugin_information', array(
                    'slug' => $slug,
                    'fields' => array(
                        'downloaded' => false,
                        'rating' => false,
                        'description' => false,
                        'short_description' => false,
                        'donate_link' => false,
                        'tags' => false,
                        'sections' => false,
                        'homepage' => false,
                        'added' => false,
                        'last_updated' => false,
                        'compatibility' => false,
                        'tested' => false,
                        'requires' => false,
                        'downloadlink' => true,
                        'icons' => false
                    )
                )
            );

            return $call_api;
        }

        public function activate_plugin($file_path) {
            if ($file_path) {
                activate_plugin($file_path, '', is_multisite() && is_network_admin(), true);

                if ($file_path == 'hash-form/hash-form.php' && function_exists('hashform_network_create_table')) {
                    hashform_network_create_table(is_multisite() && is_network_admin());
                }
            }
        }

        /* Check if plugin is active or not */

        public function plugin_status($file_path) {
            $status = 'install';

            $plugin_path = WP_PLUGIN_DIR . '/' . $file_path;

            if (file_exists($plugin_path)) {
                $status = is_plugin_active($file_path) ? 'active' : 'inactive';
            }
            return $status;
        }

        public function get_plugin_status($status) {
            switch ($status) {
                case 'active':
                    $plugin_status = esc_html__('Installed and Active', 'hashthemes-demo-importer');
                    break;

                case 'inactive':
                    $plugin_status = esc_html__('Installed but Not Active', 'hashthemes-demo-importer');
                    break;

                case 'install':
                default:
                    $plugin_status = esc_html__('Not Installed', 'hashthemes-demo-importer');
                    break;
            }
            return $plugin_status;
        }

        public function send_ajax_response() {
            $json = wp_json_encode($this->ajax_response);
            echo $json;
            die();
        }

        /*
         *  Ends the step with an error the progress screen can render.
         */

        public function send_ajax_error($message) {
            $this->ajax_response['error'] = true;
            $this->ajax_response['error_message'] = $message;
            $this->send_ajax_response();
        }

        public function add_plugin_action_link($links) {
            $custom['settings'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>', esc_url(add_query_arg('page', 'hdi-demo-importer', admin_url('themes.php'))), esc_attr__('HashThemes Demo Importer', 'hashthemes-demo-importer'), esc_html__('Import Demo', 'hashthemes-demo-importer')
            );

            return array_merge($custom, (array) $links);
        }

        /*
          Register necessary backend js
         */

        function load_backends($hook = '') {
            // The importer only lives on its own screen, so nothing here has
            // any business loading on the rest of wp-admin.
            if (!$this->page_hook || $hook !== $this->page_hook) {
                return;
            }

            $data = array(
                'nonce' => wp_create_nonce('demo-importer-ajax'),
                'prepare_importing' => esc_html__('Preparing to import demo', 'hashthemes-demo-importer'),
                'reset_database' => esc_html__('Reseting database', 'hashthemes-demo-importer'),
                'no_reset_database' => esc_html__('Database was not reset', 'hashthemes-demo-importer'),
                'confirm_import' => esc_html__('Are you sure to proceed?', 'hashthemes-demo-importer'),
                'confirm_reset_import' => esc_html__('Are you sure to proceed? Resetting the database will delete all your contents.', 'hashthemes-demo-importer'),
                'no_parts_selected' => esc_html__('Choose at least one thing to import.', 'hashthemes-demo-importer'),
                'import_steps' => $this->get_import_steps(),
                /* translators: 1: current step number, 2: total number of steps. */
                'step_counter' => esc_html__('Step %1$s of %2$s', 'hashthemes-demo-importer'),
                'show_details' => esc_html__('Show error details', 'hashthemes-demo-importer'),
                'hide_details' => esc_html__('Hide error details', 'hashthemes-demo-importer'),
                'no_details' => esc_html__('No further details were recorded.', 'hashthemes-demo-importer'),
                'preflight_blocked' => esc_html__('This demo cannot be imported right now. See the checks above.', 'hashthemes-demo-importer'),
                'import_error' => sprintf(esc_html__('There was an error in importing demo. Please reload the page and try again. If it still did not work then please click %s for more detail.', 'hashthemes-demo-importer'), '<a href="https://hashthemes.com/demo-import-process-failed-why-does-demo-import-fail/" target="_blank">' . esc_html__('here', 'hashthemes-demo-importer') . '</a>'),
                'import_success' => '<h2>' . esc_html__('All done. Have fun!', 'hashthemes-demo-importer') . '</h2><p>' . esc_html__('Your website has been successfully setup.', 'hashthemes-demo-importer') . '</p><a class="button" target="_blank" href="' . esc_url(home_url('/')) . '">' . esc_html__('View your Website', 'hashthemes-demo-importer') . '</a><a class="button" href="' . esc_url(admin_url('themes.php?page=hdi-demo-importer')) . '">' . esc_html__('Go Back', 'hashthemes-demo-importer') . '</a>'
            );

            wp_enqueue_script('isotope-pkgd', HDI_ASSETS_URL . 'isotope.pkgd.js', array('jquery'), HDI_VERSION, true);
            wp_enqueue_script('hdi-demo-ajax', HDI_ASSETS_URL . 'demo-importer-ajax.js', array('jquery', 'imagesloaded'), HDI_VERSION, true);
            wp_localize_script('hdi-demo-ajax', 'hdi_ajax_data', $data);
            if (is_rtl()) {
                wp_enqueue_style('hdi-demo-style', HDI_ASSETS_URL . 'demo-importer-style.rtl.css', array(), HDI_VERSION);
            } else {
                wp_enqueue_style('hdi-demo-style', HDI_ASSETS_URL . 'demo-importer-style.css', array(), HDI_VERSION);
            }
        }

    }

}

/**
 * Returns the shared importer instance, so themes and add-ons have something to
 * hook onto and a second call cannot register every hook twice.
 */
function hdi_importer() {
    static $instance = null;

    if (is_null($instance)) {
        $instance = new HDI_Importer();
    }

    return $instance;
}

add_action('after_setup_theme', 'hdi_importer');
