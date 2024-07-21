<?php
/**
 * Plugin Name: HashThemes Demo Importer
 * Plugin URI: https://github.com/pzstar/hashthemes-demo-importer
 * Description: Easily imports demo with just one click.
 * Version: 1.2.8
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


define('HDI_VERSION', '1.2.8');

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

        /*
         * Constructor
         */

        public function __construct() {

            $this->uploads_dir = wp_get_upload_dir();

            $this->plugin_install_count = 0;
            $this->plugin_active_count = 0;

            // Include necesarry files
            $this->configFile = include HDI_PATH . 'import_config.php';

            require_once HDI_PATH . 'classes/class-demo-importer.php';
            require_once HDI_PATH . 'classes/class-customizer-importer.php';
            require_once HDI_PATH . 'classes/class-widget-importer.php';

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
            add_submenu_page('themes.php', esc_html__('OneClick Demo Install', 'hashthemes-demo-importer'), esc_html__('HashThemes Demo Importer', 'hashthemes-demo-importer'), 'manage_options', 'hdi-demo-importer', array($this, 'display_demos'));
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
            }
            update_option('hdi_elementor_params_overwrite', 'yes');
        }

        /*
         *  Allow SVG uploads
         */

        function file_types_to_uploads($file_types) {
            $new_filetypes = array();
            $new_filetypes['svg'] = 'image/svg+xml';
            $file_types = array_merge($file_types, $new_filetypes);
            return $file_types;
        }

        /*
         *  Display the available demos
         */

        function display_demos() {
            ?>
            <div class="wrap hdi-demo-importer-wrap">
                <h2><?php echo esc_html__('HashThemes OneClick Demo Importer', 'hashthemes-demo-importer'); ?></h2>

                <?php
                if (is_array($this->configFile) && !is_null($this->configFile) && !empty($this->configFile)) {
                    $tags = $pagebuilders = array();
                    foreach ($this->configFile as $demo_slug => $demo_pack) {
                        if (isset($demo_pack['tags']) && is_array($demo_pack['tags'])) {
                            foreach ($demo_pack['tags'] as $key => $tag) {
                                $tags[$key] = $tag;
                            }
                        }
                    }

                    foreach ($this->configFile as $demo_slug => $demo_pack) {
                        if (isset($demo_pack['pagebuilder']) && is_array($demo_pack['pagebuilder'])) {
                            foreach ($demo_pack['pagebuilder'] as $key => $pagebuilder) {
                                $pagebuilders[$key] = $pagebuilder;
                            }
                        }
                    }
                    asort($tags);
                    asort($pagebuilders);

                    if (!empty($tags) || !empty($pagebuilders)) {
                        ?>
                        <div class="hdi-tab-filter hdi-clearfix">
                            <?php
                            if (!empty($tags)) {
                                ?>
                                <div class="hdi-tab-group hdi-tag-group" data-filter-group="tag">
                                    <div class="hdi-tab" data-filter="*">
                                        <?php esc_html_e('All', 'hashthemes-demo-importer'); ?>
                                    </div>
                                    <?php
                                    foreach ($tags as $key => $value) {
                                        ?>
                                        <div class="hdi-tab" data-filter=".<?php echo esc_attr($key); ?>">
                                            <?php echo esc_html($value); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <?php
                            }

                            if (!empty($pagebuilders)) {
                                ?>
                                <div class="hdi-tab-group hdi-pagebuilder-group" data-filter-group="pagebuilder">
                                    <div class="hdi-tab" data-filter="*">
                                        <?php esc_html_e('All', 'hashthemes-demo-importer'); ?>
                                    </div>
                                    <?php
                                    foreach ($pagebuilders as $key => $value) {
                                        ?>
                                        <div class="hdi-tab" data-filter=".<?php echo esc_attr($key); ?>">
                                            <?php echo esc_html($value); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="hdi-demo-box-wrap wp-clearfix">
                        <?php
                        // Loop through Demos
                        foreach ($this->configFile as $demo_slug => $demo_pack) {
                            $tags = $pagebuilders = $class = '';
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

                                    <img src="<?php echo esc_url($demo_pack['image']); ?> ">

                                    <div class="hdi-demo-actions">

                                        <h4><?php echo esc_html($demo_pack['name']); ?></h4>

                                        <div class="hdi-demo-buttons">
                                            <a href="<?php echo esc_url($demo_pack['preview_url']); ?>" target="_blank" class="button">
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
                        <?php esc_html_e("It looks like the config file for the demos is missing or conatins errors!. Demo install can't go futher!", 'hashthemes-demo-importer'); ?>
                    </div>
                <?php }
                ?>

                <?php
                /* Demo Modals */
                if (is_array($this->configFile) && !is_null($this->configFile)) {
                    foreach ($this->configFile as $demo_slug => $demo_pack) {
                        ?>
                        <div id="hdi-modal-<?php echo esc_attr($demo_slug) ?>" class="hdi-modal" style="display: none;">

                            <div class="hdi-modal-header">
                                <h2><?php printf(esc_html('Import %s Demo', 'hashthemes-demo-importer'), esc_html($demo_pack['name'])); ?></h2>
                                <div class="hdi-modal-back"><span class="dashicons dashicons-no-alt"></span></div>
                            </div>

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

                                <div class="hdi-exclude-image-checkbox">
                                    <h4><?php esc_html_e('Exclude Images', 'hashthemes-demo-importer') ?></h4>
                                    <p><?php esc_html_e('Check this option if importing demo fails multiple times. Excluding image will make the demo import process super quick.', 'hashthemes-demo-importer') ?></p>
                                    <label>
                                        <input id="checkbox-exclude-image-<?php echo esc_attr($demo_slug); ?>" type="checkbox" value='1' />
                                        <?php echo esc_html('Yes, Exclude Images', 'hashthemes-demo-importer'); ?>
                                    </label>
                                </div>

                                <div class="hdi-reset-checkbox">
                                    <h4><?php esc_html_e('Reset Website', 'hashthemes-demo-importer') ?></h4>
                                    <p><?php esc_html_e('Reseting the website will delete all your post, pages, custom post types, categories, taxonomies, images and all other customizer and theme option settings.', 'hashthemes-demo-importer') ?></p>
                                    <p><?php esc_html_e('It is always recommended to reset the database for a complete demo import.', 'hashthemes-demo-importer') ?></p>
                                    <label class="hdi-reset-website-checkbox">
                                        <input id="checkbox-reset-<?php echo esc_attr($demo_slug); ?>" type="checkbox" value='1' checked="checked" />
                                        <?php echo esc_html('Reset Website - Check this box only if you are sure to reset the website.', 'hashthemes-demo-importer'); ?>
                                    </label>
                                </div>

                                <a href="javascript:void(0)" data-demo-slug="<?php echo esc_attr($demo_slug) ?>" class="button button-primary hdi-import-demo"><?php esc_html_e('Import Demo', 'hashthemes-demo-importer'); ?></a>
                                <a href="javascript:void(0)" class="button hdi-modal-cancel"><?php esc_html_e('Cancel', 'hashthemes-demo-importer'); ?></a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <div id="hdi-import-progress" style="display: none">
                    <h2 class="hdi-import-progress-header"><?php echo esc_html__('Demo Import Progress', 'hashthemes-demo-importer'); ?></h2>

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
                    </div>
                </div>
            </div>
            <?php
        }

        /*
         *  Do the install on ajax call
         */

        function install_demo_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            // Get the demo content from the right file
            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';

            if (isset($_POST['reset']) && $_POST['reset'] == 'true') {
                $this->database_reset();
                $this->ajax_response['complete_message'] = esc_html__('Database reset complete', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['excludeImages'] = $excludeImages;
            $this->ajax_response['next_step'] = 'hdi_install_plugin';
            $this->ajax_response['next_step_message'] = esc_html__('Installing required plugins', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function install_plugin_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';

            // Install Required Plugins
            $this->install_plugins($demo_slug);

            $plugin_install_count = $this->plugin_install_count;

            if ($plugin_install_count > 0) {
                $this->ajax_response['complete_message'] = esc_html__('All the required plugins installed', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No plugin required to install', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'hdi_activate_plugin';
            $this->ajax_response['excludeImages'] = $excludeImages;
            $this->ajax_response['next_step_message'] = esc_html__('Activating required plugins', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function activate_plugin_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';

            // Activate Required Plugins
            $this->activate_plugins($demo_slug);

            $plugin_active_count = $this->plugin_active_count;

            if ($plugin_active_count > 0) {
                $this->ajax_response['complete_message'] = esc_html__('All the required plugins activated', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No plugin required to activate', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['excludeImages'] = $excludeImages;
            $this->ajax_response['next_step'] = 'hdi_download_files';
            $this->ajax_response['next_step_message'] = esc_html__('Downloading demo files', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function download_files_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';

            $downloads = $this->download_files($this->configFile[$demo_slug]['external_url']);
            if ($downloads) {
                $this->ajax_response['complete_message'] = esc_html__('All demo files downloaded', 'hashthemes-demo-importer');
                $this->ajax_response['next_step'] = 'hdi_import_xml';
                $this->ajax_response['next_step_message'] = esc_html__('Importing posts, pages and medias. It may take a bit longer time', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['error'] = true;
                $this->ajax_response['error_message'] = esc_html__('Demo import process failed. Demo files can not be downloaded', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['excludeImages'] = $excludeImages;
            $this->send_ajax_response();
        }

        function import_xml_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            if ('1' !== get_option('elementor_unfiltered_files_upload')) {
                update_option('elementor_unfiltered_files_upload', '1');
            }

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';
            // Import XML content
            $xml_filepath = $this->demo_upload_dir($demo_slug) . '/content.xml';

            if (file_exists($xml_filepath)) {
                $this->importDemoContent($xml_filepath, $excludeImages);
                $this->ajax_response['complete_message'] = esc_html__('All content imported', 'hashthemes-demo-importer');
                $this->ajax_response['next_step'] = 'hdi_import_customizer';
                $this->ajax_response['next_step_message'] = esc_html__('Importing customizer settings', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['error'] = true;
                $this->ajax_response['error_message'] = esc_html__('Demo import process failed. No content file found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['excludeImages'] = $excludeImages;
            $this->send_ajax_response();
        }

        function import_customizer_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
            $excludeImages = isset($_POST['excludeImages']) ? sanitize_text_field($_POST['excludeImages']) : '';

            $customizer_filepath = $this->demo_upload_dir($demo_slug) . '/customizer.dat';

            if (file_exists($customizer_filepath)) {
                ob_start();
                HDI_Customizer_Importer::import($customizer_filepath, $excludeImages);
                ob_end_clean();
                $this->ajax_response['complete_message'] = esc_html__('Customizer settings imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No customizer settings found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'hdi_import_menu';
            $this->ajax_response['next_step_message'] = esc_html__('Setting menus', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function import_menu_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';

            $menu_array = isset($this->configFile[$demo_slug]['menu_array']) ? $this->configFile[$demo_slug]['menu_array'] : '';
            // Set menu
            if ($menu_array) {
                $this->setMenu($menu_array);
                $this->ajax_response['complete_message'] = esc_html__('Menus saved', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No menus saved', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'hdi_import_theme_option';
            $this->ajax_response['next_step_message'] = esc_html__('Importing theme option settings', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function import_theme_option_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';

            $options_array = isset($this->configFile[$demo_slug]['options_array']) ? $this->configFile[$demo_slug]['options_array'] : '';

            if (isset($options_array) && is_array($options_array)) {
                foreach ($options_array as $theme_option) {
                    $option_filepath = $this->demo_upload_dir($demo_slug) . '/' . $theme_option . '.json';

                    if (file_exists($option_filepath)) {
                        $data = file_get_contents($option_filepath);

                        if ($data) {
                            update_option($theme_option, json_decode($data, true));
                        }
                    }
                }
                $this->ajax_response['complete_message'] = esc_html__('Theme options settings imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No theme options found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'hdi_import_widget';
            $this->ajax_response['next_step_message'] = esc_html__('Importing widgets', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function import_widget_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';

            $widget_filepath = $this->demo_upload_dir($demo_slug) . '/widget.wie';

            if (file_exists($widget_filepath)) {
                ob_start();
                HDI_Widget_Importer::import($widget_filepath);
                ob_end_clean();
                $this->ajax_response['complete_message'] = esc_html__('Widgets imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No widgets found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'hdi_import_hashform';
            $this->ajax_response['next_step_message'] = esc_html__('Importing Forms', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function import_hashform_process() {
            if (!current_user_can('manage_options')) {
                return;
            }

            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';

            $hash_forms = isset($this->configFile[$demo_slug]['hash_forms']) ? $this->configFile[$demo_slug]['hash_forms'] : '';

            if (isset($hash_forms) && is_array($hash_forms)) {
                foreach ($hash_forms as $hash_form) {
                    $filepath = $this->demo_upload_dir($demo_slug) . '/' . $hash_form . '.json';

                    if (file_exists($filepath)) {
                        if (class_exists('HashFormBuilder')) {
                            hashform_create_table();

                            $imdat = json_decode(file_get_contents($filepath), true);
                            $options = $imdat['options'];

                            $form = array(
                                'name' => esc_html($options['title']),
                                'description' => esc_html($options['description']),
                                'options' => $options,
                                'status' => $imdat['status'],
                                'settings' => $imdat['settings'],
                                'styles' => $imdat['styles'],
                                'created_at' => current_time('mysql'),
                            );

                            $form_id = HashFormBuilder::create($form);

                            foreach ($imdat['field'] as $field) {
                                HashFormFields::create_row(
                                        array(
                                            'name' => $field['name'],
                                            'description' => $field['description'],
                                            'type' => $field['type'],
                                            'default_value' => $field['default_value'],
                                            'options' => $field['options'],
                                            'field_order' => $field['field_order'],
                                            'form_id' => absint($form_id),
                                            'required' => $field['required'],
                                            'field_options' => $field['field_options']
                                        )
                                );
                            }
                        } else {
                            $this->ajax_response['complete_message'] = esc_html__('Hash Form plugin not installed', 'hashthemes-demo-importer');
                        }
                    }
                }
                $this->ajax_response['complete_message'] = esc_html__('Forms imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No Form files found', 'hashthemes-demo-importer');
            }

            $sliderFile = $this->demo_upload_dir($demo_slug) . '/revslider.zip';

            if (file_exists($sliderFile)) {
                $this->ajax_response['next_step'] = 'hdi_import_revslider';
                $this->ajax_response['next_step_message'] = esc_html__('Importing Revolution slider', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['next_step'] = '';
                $this->ajax_response['next_step_message'] = '';
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->send_ajax_response();
        }

        function import_revslider_process() {
            if (!current_user_can('manage_options')) {
                return;
            }
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';

            // Get the zip file path
            $sliderFile = $this->demo_upload_dir($demo_slug) . '/revslider.zip';

            if (file_exists($sliderFile)) {
                if (class_exists('RevSlider')) {
                    $slider = new RevSlider();
                    $slider->importSliderFromPost(true, true, $sliderFile);
                    $this->ajax_response['complete_message'] = esc_html__('Revolution slider installed', 'hashthemes-demo-importer');
                } else {
                    $this->ajax_response['complete_message'] = esc_html__('Revolution slider plugin not installed', 'hashthemes-demo-importer');
                }
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No Revolution slider found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = '';
            $this->ajax_response['next_step_message'] = '';
            $this->send_ajax_response();
        }

        public function download_files($external_url) {
            // Make sure we have the dependency.
            if (!function_exists('WP_Filesystem')) {
                require_once (ABSPATH . 'wp-admin/includes/file.php');
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
            if (!$result) {
                return false;
            } else {
                $demo_pack = $this->demo_upload_dir() . 'demo-pack.zip';

                $file = wp_remote_retrieve_body(
                        wp_remote_get(
                                $external_url, array(
                    'timeout' => 60,
                                )
                        )
                );

                $wp_filesystem->put_contents($demo_pack, $file);
                unzip_file($demo_pack, $this->demo_upload_dir());
                $wp_filesystem->delete($demo_pack);
                return true;
            }
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

            foreach ($custom_tables as $tbl) {
                $wpdb->query('SET foreign_key_checks = 0');
                $wpdb->query('TRUNCATE TABLE ' . $tbl);
            }

            // Delete Widgets
            global $wp_registered_widget_controls;

            $widget_controls = $wp_registered_widget_controls;

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
        }

        /*
         * Clear "uploads" folder
         * @param string $dir
         * @return bool
         */

        private function clear_uploads($dir) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->clear_uploads("$dir/$file") : unlink("$dir/$file");
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

        function importDemoContent($xml_filepath, $excludeImages) {

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
                $demo_slug = isset($_POST['demo']) ? sanitize_text_field($_POST['demo']) : '';
                $excludeImages = $excludeImages == 'true' ? false : true;
                $home_slug = isset($this->configFile[$demo_slug]['home_slug']) ? $this->configFile[$demo_slug]['home_slug'] : '';
                $blog_slug = isset($this->configFile[$demo_slug]['blog_slug']) ? $this->configFile[$demo_slug]['blog_slug'] : '';
                $element_kit_slug = isset($this->configFile[$demo_slug]['element_kit_slug']) ? $this->configFile[$demo_slug]['element_kit_slug'] : '';

                if (file_exists($xml_filepath)) {
                    $wp_import = new HDI_Import();
                    $wp_import->fetch_attachments = $excludeImages;
                    // Capture the output.
                    ob_start();
                    $wp_import->import($xml_filepath);
                    // Clean the output.
                    ob_end_clean();
                    // Import DONE
                    // set homepage as front page
                    if ($home_slug) {
                        $page = get_page_by_path($home_slug);
                        if ($page) {
                            update_option('show_on_front', 'page');
                            update_option('page_on_front', $page->ID);
                        } else {
                            $page = get_page_by_title('Home');
                            if ($page) {
                                update_option('show_on_front', 'page');
                                update_option('page_on_front', $page->ID);
                            }
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
            $demo = $this->configFile[$slug];

            $plugins = $demo['plugins'];

            foreach ($plugins as $plugin_slug => $plugin) {
                $name = isset($plugin['name']) ? $plugin['name'] : '';
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
            $demo = $this->configFile[$slug];

            $plugins = $demo['plugins'];

            foreach ($plugins as $plugin_slug => $plugin) {
                $name = isset($plugin['name']) ? $plugin['name'] : '';
                $file_path = isset($plugin['file_path']) ? $plugin['file_path'] : '';
                $plugin_status = $this->plugin_status($file_path);

                if ($plugin_status == 'inactive') {
                    $this->activate_plugin($file_path);
                    $this->plugin_active_count++;
                }
            }
        }

        public function plugin_installer_callback($path, $slug) {
            $plugin_status = $this->plugin_status($path);

            if ($plugin_status == 'install') {
                // Include required libs for installation
                require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
                require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

                // Get Plugin Info
                $api = $this->call_plugin_api($slug);

                $skin = new WP_Ajax_Upgrader_Skin();
                $upgrader = new Plugin_Upgrader($skin);
                $upgrader->install($api->download_link);

                $this->activate_plugin($file_path);

                $this->plugin_install_count++;
            }
        }

        public function plugin_offline_installer_callback($path, $external_url) {

            $plugin_status = $this->plugin_status($path);

            if ($plugin_status == 'install') {
                // Make sure we have the dependency.
                if (!function_exists('WP_Filesystem')) {
                    require_once (ABSPATH . 'wp-admin/includes/file.php');
                }

                /*
                 * Initialize WordPress' file system handler.
                 *
                 * @var WP_Filesystem_Base $wp_filesystem
                 */
                WP_Filesystem();
                global $wp_filesystem;

                $plugin = $this->demo_upload_dir() . 'plugin.zip';

                $file = wp_remote_retrieve_body(
                        wp_remote_get(
                                $external_url, array(
                    'timeout' => 60,
                                )
                        )
                );

                $wp_filesystem->mkdir($this->demo_upload_dir());

                $wp_filesystem->put_contents($plugin, $file);

                unzip_file($plugin, WP_PLUGIN_DIR);

                $plugin_file = WP_PLUGIN_DIR . '/' . esc_html($path);

                $wp_filesystem->delete($plugin);

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
                $activate = activate_plugin($file_path, '', false, true);
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
                case 'install':
                    $plugin_status = esc_html__('Not Installed', 'hashthemes-demo-importer');
                    break;

                case 'active':
                    $plugin_status = esc_html__('Installed and Active', 'hashthemes-demo-importer');
                    break;

                case 'inactive':
                    $plugin_status = esc_html__('Installed but Not Active', 'hashthemes-demo-importer');
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
          Register necessary backend js
         */

        function load_backends() {
            $data = array(
                'nonce' => wp_create_nonce('demo-importer-ajax'),
                'prepare_importing' => esc_html__('Preparing to import demo', 'hashthemes-demo-importer'),
                'reset_database' => esc_html__('Reseting database', 'hashthemes-demo-importer'),
                'no_reset_database' => esc_html__('Database was not reset', 'hashthemes-demo-importer'),
                'import_error' => sprintf(esc_html__('There was an error in importing demo. Please reload the page and try again. If it still did not work then please click %s for more detail.', 'hashthemes-demo-importer'), '<a href="https://hashthemes.com/demo-import-process-failed-why-does-demo-import-fail/" target="_blank">' . esc_html('here', 'hashthemes-demo-importer') . '</a>'),
                'import_success' => '<h2>' . esc_html__('All done. Have fun!', 'hashthemes-demo-importer') . '</h2><p>' . esc_html__('Your website has been successfully setup.', 'hashthemes-demo-importer') . '</p><a class="button" target="_blank" href="' . esc_url(home_url('/')) . '">View your Website</a><a class="button" href="' . esc_url(admin_url('/admin.php?page=hdi-demo-importer')) . '">' . esc_html__('Go Back', 'hashthemes-demo-importer') . '</a>'
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

function hdi_importer() {
    new HDI_Importer;
}

add_action('after_setup_theme', 'hdi_importer');
