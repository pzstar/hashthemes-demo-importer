<?php
/**
 * Plugin Name: HashThemes Demo Importer
 * Plugin URI: 
 * Description: A one click demo importer to import demo contents developed by HashThemes.
 * Version: 1.0.
 * Author: HashThemes
 * Author URI:  https://hashthemes.com
 * Text Domain: hashthemes-demo-importer
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 */
if (!defined('ABSPATH'))
    exit;


define( 'HDI_VERSION', '1.0.0' );

define( 'HDI_FILE', __FILE__ );
define( 'HDI_PLUGIN_BASENAME', plugin_basename( HDI_FILE ) );
define( 'HDI_PATH', plugin_dir_path( HDI_FILE ) );
define( 'HDI_URL', plugins_url( '/', HDI_FILE ) );

define( 'HDI_ASSETS_URL', HDI_URL . 'assets/' );

if (!class_exists('Viral_Pro_Importer')) {

    class Viral_Pro_Importer {

        public $configFile;
        public $uploads_dir;
        public $plugin_install_count;
        public $ajax_response = array();

        /*
         * Constructor
         */

        public function __construct() {

            $this->uploads_dir = wp_get_upload_dir();

            $this->plugin_install_count = 0;

            // Include necesarry files
            $this->configFile = include HDI_PATH . 'import_config.php';

            require_once HDI_PATH . 'classes/class-demo-importer.php';
            require_once HDI_PATH . 'classes/class-customizer-importer.php';
            require_once HDI_PATH . 'classes/class-widget-importer.php';
            
            // Load translation files
            add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

            // WP-Admin Menu
            add_action('admin_menu', array($this, 'viral_pro_menu'));

            // Add necesary backend JS
            add_action('admin_enqueue_scripts', array($this, 'load_backends'));

            // Actions for the ajax call
            add_action('wp_ajax_viral_pro_install_demo', array($this, 'viral_pro_install_demo'));
            add_action('wp_ajax_viral_pro_install_plugin', array($this, 'viral_pro_install_plugin'));
            add_action('wp_ajax_viral_pro_download_files', array($this, 'viral_pro_download_files'));
            add_action('wp_ajax_viral_pro_import_xml', array($this, 'viral_pro_import_xml'));
            add_action('wp_ajax_viral_pro_customizer_import', array($this, 'viral_pro_customizer_import'));
            add_action('wp_ajax_viral_pro_menu_import', array($this, 'viral_pro_menu_import'));
            add_action('wp_ajax_viral_pro_theme_option', array($this, 'viral_pro_theme_option'));
            add_action('wp_ajax_viral_pro_importing_widget', array($this, 'viral_pro_importing_widget'));
            add_action('wp_ajax_viral_pro_importing_revslider', array($this, 'viral_pro_importing_revslider'));
        }
        
        /**
         * Loads the translation files.
         *
         * @since 1.0.0
         * @access public
         * @return void
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'hashthemes-demo-importer', false, HDI_PATH . '/languages' );
        }

        /*
         * WP-ADMIN Menu for importer
         */

        function viral_pro_menu() {
            add_submenu_page('themes.php', 'OneClick Demo Install', 'HashThemes Demo Importer', 'manage_options', 'viral-pro-demo-importer', array($this, 'viral_pro_display_demos'));
        }

        /*
         *  Display the available demos
         */

        function viral_pro_display_demos() {
            ?>
            <div class="wrap viral-pro-demo-importer-wrap">
                <h2><?php echo esc_html__('Viral Pro OneClick Demo Importer', 'hashthemes-demo-importer'); ?></h2>

                <?php if (is_array($this->configFile) && !is_null($this->configFile)) { ?>
                    <div class="viral-pro-demo-box-wrap wp-clearfix">
                        <?php
                        // Loop through Demos
                        foreach ($this->configFile as $demo_slug => $demo_pack) {
                            $tags = implode(' ', array_keys($demo_pack['tags']));
                            ?>
                            <div id="<?php echo esc_attr($demo_slug); ?>" class="viral-pro-demo-box <?php echo esc_attr($tags); ?>">
                                <img src="<?php echo esc_url($demo_pack['image']); ?> ">

                                <div class="viral-pro-demo-actions">
                                    <h4><?php echo esc_html($demo_pack['name']); ?></h4>

                                    <div class="viral-pro-demo-buttons">
                                        <a href="<?php echo esc_url($demo_pack['preview_url']); ?>" target="_blank" class="button">
                                            <?php echo esc_html__('Preview', 'hashthemes-demo-importer'); ?>
                                        </a> 

                                        <a href="#viral-pro-modal-<?php echo esc_attr($demo_slug) ?>" class="viral-pro-modal-button button button-primary">
                                            <?php echo esc_html__('Install', 'hashthemes-demo-importer') ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php }
                        ?>
                    </div>
                <?php } else {
                    ?>
                    <div class="viral-pro-demo-wrap">
                        <?php esc_html_e("It looks like the config file for the demos is missing or conatins errors!. Demo install can\'t go futher!", 'hashthemes-demo-importer'); ?>  
                    </div>
                <?php }
                ?>

                <?php
                /* Demo Modals */
                if (is_array($this->configFile) && !is_null($this->configFile)) {
                    foreach ($this->configFile as $demo_slug => $demo_pack) {
                        ?>
                        <div id="viral-pro-modal-<?php echo esc_attr($demo_slug) ?>" class="viral-pro-modal" style="display: none;">

                            <div class="viral-pro-modal-header">
                                <h2><?php printf(esc_html('Import %s Demo', 'hashthemes-demo-importer'), esc_html($demo_pack['name'])); ?></h2>
                                <div class="viral-pro-modal-back"><span class="dashicons dashicons-no-alt"></span></div>
                            </div>

                            <div class="viral-pro-modal-wrap">
                                <p><?php echo sprintf(esc_html__('We recommend you backup your website content before attempting to import the demo so that you can recover your website if something goes wrong. You can use %s plugin for it.', 'hashthemes-demo-importer'), '<a href="https://wordpress.org/plugins/all-in-one-wp-migration/" target="_blank">' . esc_html__('All in one migration', 'hashthemes-demo-importer') . '</a>'); ?></p>

                                <p><?php echo esc_html__('This process will install all the required plugins, import contents and setup customizer and theme options.', 'hashthemes-demo-importer'); ?></p>

                                <div class="viral-pro-modal-recommended-plugins">
                                    <h4><?php esc_html_e('Required Plugins', 'hashthemes-demo-importer') ?></h4>
                                    <p><?php esc_html_e('For your website to look exactly like the demo,the import process will install and activate the following plugin if they are not installed or activated.', 'hashthemes-demo-importer') ?></p>
                                    <?php
                                    $plugins = isset($demo_pack['plugins']) ? $demo_pack['plugins'] : '';

                                    if (is_array($plugins)) {
                                        ?>
                                        <ul>
                                            <?php
                                            foreach ($plugins as $plugin) {
                                                $name = isset($plugin['name']) ? $plugin['name'] : '';
                                                $status = Viral_Pro_Demo_Importer::plugin_active_status($plugin['file_path']);
                                                ?>
                                                <li>
                                                    <?php
                                                    echo esc_html($name) . ' - ' . $this->get_plugin_status($status);
                                                    ?>
                                                </li>
                                            <?php }
                                            ?>
                                        </ul>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div class="viral-pro-reset-checkbox">
                                    <h4><?php esc_html_e('Reset Website', 'hashthemes-demo-importer') ?></h4>
                                    <p><?php esc_html_e('Reseting the website will delete all your post, pages, custom post types, categories, taxonomies, images and all other customizer and theme option settings.', 'hashthemes-demo-importer') ?></p>
                                    <p><?php esc_html_e('It is always recommended to reset the database for a complete demo import.', 'hashthemes-demo-importer') ?></p>
                                    <label>
                                        <input id="checkbox-reset-<?php echo esc_attr($demo_slug); ?>" type="checkbox" value='1' checked="checked"/>
                                        <?php echo esc_html('Reset Website', 'hashthemes-demo-importer'); ?>
                                    </label>
                                </div>

                                <a href="javascript:void(0)" data-demo-slug="<?php echo esc_attr($demo_slug) ?>" class="button button-primary viral-pro-import-demo"><?php esc_html_e('Import Demo', 'hashthemes-demo-importer'); ?></a>
                                <a href="javascript:void(0)" class="button viral-pro-modal-cancel"><?php esc_html_e('Cancel', 'hashthemes-demo-importer'); ?></a>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <div id="viral-pro-import-progress" style="display: none">
                    <h2 class="viral-pro-import-progress-header"><?php echo esc_html__('Demo Import Progress', 'hashthemes-demo-importer'); ?></h2>

                    <div class="viral-pro-import-progress-wrap">
                        <div class="viral-pro-import-loader">
                            <div class="viral-pro-loader-content">
                                <div class="viral-pro-loader-content-inside">
                                    <div class="viral-pro-loader-rotater"></div>
                                    <div class="viral-pro-loader-line-point"></div>
                                </div>
                            </div>
                        </div>
                        <div class="viral-pro-import-progress-message"></div>
                    </div>
                </div>
            </div>
            <?php
        }

        /*
         *  Do the install on ajax call
         */

        function viral_pro_install_demo() {
            check_ajax_referer('demo-importer-ajax', 'security');

            // Get the demo content from the right file
            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $this->ajax_response['demo'] = $demo_slug;

            if (isset($_POST['reset']) && $_POST['reset'] == 'true') {
                $this->database_reset();
                $this->ajax_response['complete_message'] = esc_html__('Database reset complete', 'hashthemes-demo-importer');
            }

            $this->ajax_response['next_step'] = 'viral_pro_install_plugin';
            $this->ajax_response['next_step_message'] = esc_html__('Installing required plugins', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_install_plugin() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            // Install Required Plugins
            $this->install_plugins($demo_slug);

            $plugin_install_count = $this->plugin_install_count;

            $this->ajax_response['demo'] = $demo_slug;

            if ($plugin_install_count > 0) {
                $this->ajax_response['complete_message'] = esc_html__('All the required plugins installed and activated successfully', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No plugin required to install', 'hashthemes-demo-importer');
            }
            $this->ajax_response['next_step'] = 'viral_pro_download_files';
            $this->ajax_response['next_step_message'] = esc_html__('Downloading demo files', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_download_files() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $this->download_files($this->configFile[$demo_slug]['external_url']);

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['complete_message'] = esc_html__('All demo files downloaded', 'hashthemes-demo-importer');
            $this->ajax_response['next_step'] = 'viral_pro_import_xml';
            $this->ajax_response['next_step_message'] = esc_html__('Importing posts, pages and medias. It may take a bit longer time', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_import_xml() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            // Import XML content
            $this->importDemoContent($demo_slug);

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['complete_message'] = esc_html__('All content imported', 'hashthemes-demo-importer');
            $this->ajax_response['next_step'] = 'viral_pro_customizer_import';
            $this->ajax_response['next_step_message'] = esc_html__('Importing customizer settings', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_customizer_import() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $customizer_filepath = $this->demo_upload_dir($demo_slug) . '/customizer.dat';

            if (file_exists($customizer_filepath)) {
                ob_start();
                Viral_Pro_Customizer_Importer::import($customizer_filepath);
                ob_end_clean();
                $this->ajax_response['complete_message'] = esc_html__('Customizer settings imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No Customizer settings found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'viral_pro_menu_import';
            $this->ajax_response['next_step_message'] = esc_html__('Setting primary menu', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_menu_import() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $menu_array = isset($this->configFile[$demo_slug]['menu_array']) ? $this->configFile[$demo_slug]['menu_array'] : '';
            // Set menu
            if ($menu_array) {
                $this->setMenu($menu_array);
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['complete_message'] = esc_html__('Primary menu saved', 'hashthemes-demo-importer');
            $this->ajax_response['next_step'] = 'viral_pro_theme_option';
            $this->ajax_response['next_step_message'] = esc_html__('Importing theme option settings', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_theme_option() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $themeoption_filepath = $this->demo_upload_dir($demo_slug) . '/theme-option.json';

            if (file_exists($themeoption_filepath)) {
                $data = file_get_contents($themeoption_filepath);

                if ($data) {
                    if (update_option('viral-pro-options', json_decode($data, true), '', 'yes')) {
                        $this->ajax_response['complete_message'] = esc_html__('Theme options settings imported', 'hashthemes-demo-importer');
                    }
                }
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No theme options found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'viral_pro_importing_widget';
            $this->ajax_response['next_step_message'] = esc_html__('Importing Widgets', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_importing_widget() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

            $widget_filepath = $this->demo_upload_dir($demo_slug) . '/widget.wie';

            if (file_exists($widget_filepath)) {
                ob_start();
                Viral_Pro_Widget_Importer::import($widget_filepath);
                ob_end_clean();
                $this->ajax_response['complete_message'] = esc_html__('Widgets Imported', 'hashthemes-demo-importer');
            } else {
                $this->ajax_response['complete_message'] = esc_html__('No Widgets found', 'hashthemes-demo-importer');
            }

            $this->ajax_response['demo'] = $demo_slug;
            $this->ajax_response['next_step'] = 'viral_pro_importing_revslider';
            $this->ajax_response['next_step_message'] = esc_html__('Importing Revolution slider', 'hashthemes-demo-importer');
            $this->send_ajax_response();
        }

        function viral_pro_importing_revslider() {
            check_ajax_referer('demo-importer-ajax', 'security');

            $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';

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
                require_once( ABSPATH . 'wp-admin/includes/file.php' );
            }

            /**
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
                $this->add_ajax_message['message'] = esc_html__('The directory for the demo packs couldn\'t be created.', 'hashthemes-demo-importer');
                $this->ajax_response['error'] = true;
                $this->send_ajax_response();
            }

            $demo_pack = $this->demo_upload_dir() . 'demo-pack.zip';

            $file = wp_remote_retrieve_body(wp_remote_get($external_url, array(
                'timeout' => 60,
            )));

            $wp_filesystem->put_contents($demo_pack, $file);
            unzip_file($demo_pack, $this->demo_upload_dir());
            $wp_filesystem->delete($demo_pack);
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

        /**
         * Clear "uploads" folder
         * @param string $dir
         * @return bool
         */
        private function clear_uploads($dir) {
            $files = array_diff(scandir($dir), array('.', '..'));
            foreach ($files as $file) {
                ( is_dir("$dir/$file") ) ? $this->clear_uploads("$dir/$file") : unlink("$dir/$file");
            }

            return ( $dir != $this->uploads_dir['basedir'] ) ? rmdir($dir) : true;
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

        function importDemoContent($slug) {

            if (!defined('WP_LOAD_IMPORTERS'))
                define('WP_LOAD_IMPORTERS', true);

            if (!class_exists('WP_Import')) {
                $class_wp_importer = HDI_PATH . "wordpress-importer/wordpress-importer.php";
                if (file_exists($class_wp_importer)) {
                    require_once $class_wp_importer;
                }
            }

            // Import demo content from XML
            if (class_exists('WP_Import')) {
                $import_filepath = $this->demo_upload_dir($slug) . '/content.xml'; // Get the xml file from directory 
                $demo_slug = isset($_POST['demo']) ? $_POST['demo'] : '';
                $home_slug = isset($this->configFile[$demo_slug]['home_slug']) ? $this->configFile[$demo_slug]['home_slug'] : '';
                $blog_slug = isset($this->configFile[$demo_slug]['blog_slug']) ? $this->configFile[$demo_slug]['blog_slug'] : '';

                if (file_exists($import_filepath)) {
                    $wp_import = new WP_Import();
                    $wp_import->fetch_attachments = true;
                    // Capture the output.
                    ob_start();
                    $wp_import->import($import_filepath);
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

                $this->activate_plugin($path);
                $this->plugin_install_count++;
            } else if ($plugin_status == 'inactive') {
                $this->activate_plugin($path);
                $this->plugin_install_count++;
            }
        }

        public function plugin_offline_installer_callback($path, $external_url) {

            $plugin_status = $this->plugin_status($path);

            if ($plugin_status == 'install') {
                // Make sure we have the dependency.
                if (!function_exists('WP_Filesystem')) {
                    require_once( ABSPATH . 'wp-admin/includes/file.php' );
                }

                /**
                 * Initialize WordPress' file system handler.
                 *
                 * @var WP_Filesystem_Base $wp_filesystem
                 */
                WP_Filesystem();
                global $wp_filesystem;

                $plugin = $this->demo_upload_dir() . 'plugin.zip';

                $file = wp_remote_retrieve_body(wp_remote_get($external_url, array(
                    'timeout' => 60,
                )));

                $wp_filesystem->mkdir($this->demo_upload_dir());

                $wp_filesystem->put_contents($plugin, $file);

                unzip_file($plugin, WP_PLUGIN_DIR);

                $plugin_file = WP_PLUGIN_DIR . '/' . esc_html($path);

                if (file_exists($plugin_file)) {
                    $this->activate_plugin($path);
                    $this->plugin_install_count++;
                }

                $wp_filesystem->delete($plugin);
            } else if ($plugin_status == 'inactive') {
                $this->activate_plugin($path);
                $this->plugin_install_count++;
            }
        }

        /* Plugin API */

        public function call_plugin_api($slug) {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

            $call_api = plugins_api('plugin_information', array(
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
            )));

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
                'import_error' => esc_html__('There was an error in importing demo. Please reload the page and try again.', 'hashthemes-demo-importer'),
                'import_success' => '<h2>' . esc_html__('All done. Have fun!', 'hashthemes-demo-importer') . '</h2><p>' . esc_html__('Your website has been successfully setup.', 'hashthemes-demo-importer') . '</p><a class="button" target="_blank" href="' . esc_url(home_url('/')) . '">View your Website</a><a class="button" href="' . esc_url(admin_url('/admin.php?page=viral-pro-demo-importer')) . '">Go Back</a>'
            );

            wp_enqueue_script('viral-pro-demo-ajax', HDI_ASSETS_URL . 'demo-importer-ajax.js', array('jquery'), '2.0.0', true);
            wp_localize_script('viral-pro-demo-ajax', 'viral_pro_ajax_data', $data);
            wp_enqueue_style('viral-pro-demo-style', HDI_ASSETS_URL . 'demo-importer-style.css', array(), '2.0.0');
        }

    }

}
new Viral_Pro_Importer;
