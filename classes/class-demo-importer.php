<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    die();
}

if (!class_exists('HDI_Demo_Importer')) {

    final class HDI_Demo_Importer {

        /**
         * A reference to an instance of this class.
         *
         * @since  1.0.0
         * @access private
         * @var    object
         */
        private static $instance = NULL;

        /**
         * Initiator
         *
         * @since 1.0.0
         * @return object
         */
        public static function get_instance() {
            if (!isset(self::$instance)) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public function __construct() {
            /** WordPress Plugin Installation Ajax * */
            add_action('wp_ajax_plugin_installer', array($this, 'plugin_installer_callback'));

            /** Bundled & Remote Plugin Installation Ajax * */
            add_action('wp_ajax_plugin_offline_installer', array($this, 'plugin_offline_installer_callback'));

            /** Plugin Activation Ajax * */
            add_action('wp_ajax_plugin_activation', array($this, 'plugin_activation_callback'));

            /** Plugin Deactivation Ajax * */
            add_action('wp_ajax_plugin_deactivation', array($this, 'plugin_deactivation_callback'));
        }

        /** Plugin API * */
        public static function call_plugin_api($plugin) {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

            $call_api = plugins_api(
                    'plugin_information', array(
                'slug' => $plugin,
                'fields' => array(
                    'downloaded' => false,
                    'rating' => false,
                    'description' => false,
                    'short_description' => false,
                    'donate_link' => false,
                    'tags' => false,
                    'sections' => true,
                    'homepage' => true,
                    'added' => false,
                    'last_updated' => false,
                    'compatibility' => false,
                    'tested' => false,
                    'requires' => false,
                    'downloadlink' => false,
                    'icons' => true
                )
                    )
            );

            return $call_api;
        }

        /** Check if Plugin is active or not * */
        public static function plugin_active_status($file_path) {
            $status = 'install';
            $plugin_path = WP_PLUGIN_DIR . '/' . esc_attr($file_path);

            if (file_exists($plugin_path)) {
                $status = is_plugin_active($file_path) ? 'active' : 'inactive';
            }

            return $status;
        }

        /** Generate Url for the Plugin Button * */
        public static function generate_plugin_url($plugin) {
            $status = self::plugin_active_status($plugin);
            $url = 'javascript:void()';
            if ($status == 'install' && $source == 'remote') {
                $url = $plugin['location'];
            }
            return $url;
        }

        /** Generate Class for the Plugin Button * */
        public static function generate_plugin_class($plugin) {
            $status = self::plugin_active_status($plugin);
            switch ($status) {
                case 'install':
                    $btn_class = 'install button button-primary';
                    break;

                case 'inactive':
                    $btn_class = 'activate button button-primary';
                    break;

                case 'active':
                    $btn_class = 'installed button';
                    break;
            }

            return $btn_class;
        }

        /** Generate Label for the Plugin Button * */
        public static function generate_plugin_label($plugin) {
            $status = self::plugin_active_status($plugin);
            switch ($status) {
                case 'install':
                    $btn_label = esc_html__('Install', 'hashthemes-demo-importer');
                    break;

                case 'inactive':
                    $btn_label = esc_html__('Activate', 'hashthemes-demo-importer');
                    break;

                case 'active':
                    $btn_label = esc_html__('Installed', 'hashthemes-demo-importer');
                    break;
            }
            return $btn_label;
        }

        /* ========== Plugin Installation Ajax =========== */

        public function plugin_installer_callback() {

            if (!current_user_can('install_plugins')) {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => esc_html__('Sorry, you are not allowed to install plugins on this site.', 'hashthemes-demo-importer')
                        )
                );
                die();
            }

            $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
            $plugin = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';
            $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field(wp_unslash($_POST['plugin_file'])) : '';

            // Check our nonce, if they don't match then bounce!
            if (!wp_verify_nonce($nonce, 'plugin_installer_nonce')) {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => esc_html__('Error - unable to verify nonce, please try again.', 'hashthemes-demo-importer')
                        )
                );
                die();
            }

            // Include required libs for installation
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
            require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

            // Get Plugin Info
            $api = self::call_plugin_api($plugin);

            $skin = new WP_Ajax_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader($skin);
            $upgrader->install($api->download_link);

            $plugin_file = esc_html($plugin) . '/' . esc_html($plugin_file);

            if ($plugin_file) {
                $activate = activate_plugin($plugin_file, '', false, true);
                wp_send_json_success();
            }

            die();
        }

        /** Plugin Offline Installation Ajax * */
        public function plugin_offline_installer_callback() {
            if (!current_user_can('install_plugins')) {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => esc_html__('Sorry, you are not allowed to install plugins on this site.', 'hashthemes-demo-importer')
                        )
                );
                die();
            }

            $file_location = isset($_POST['file_location']) ? sanitize_text_field(wp_unslash($_POST['file_location'])) : '';
            $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
            $plugin = isset($_POST['slug']) ? sanitize_text_field(wp_unslash($_POST['slug'])) : '';
            $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field(wp_unslash($_POST['plugin_file'])) : '';

            // Check our nonce, if they don't match then bounce!
            if (!wp_verify_nonce($nonce, 'plugin_installer_nonce')) {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => esc_html__('Error - unable to verify nonce, please try again.', 'hashthemes-demo-importer')
                        )
                );
                die();
            }

            if ($file_location) {
                $upload_path = $this->get_local_dir_path($file_location, $plugin);
                $plugin_file = WP_PLUGIN_DIR . '/' . esc_html($plugin) . '/' . esc_html($plugin_file);

                $zip = new ZipArchive();
                if ($zip->open($upload_path) === TRUE) {
                    $zip->extractTo(WP_PLUGIN_DIR);
                    $zip->close();

                    if (file_exists($plugin_file)) {
                        activate_plugin($plugin_file);
                    }

                    unlink($upload_path);

                    wp_send_json_success();
                }
            } else {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => __('Missing File Location.', 'hashthemes-demo-importer')
                        )
                );
            }

            die();
        }

        /** Plugin Offline Activation Ajax * */
        public function plugin_activation_callback() {

            $plugin = isset($_POST['plugin']) ? sanitize_text_field(wp_unslash($_POST['plugin'])) : '';
            $plugin_file = isset($_POST['plugin_file']) ? sanitize_text_field(wp_unslash($_POST['plugin_file'])) : '';
            $plugin_file = WP_PLUGIN_DIR . '/' . esc_html($plugin) . '/' . esc_html($plugin_file);
            $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';

            // Check our nonce, if they don't match then bounce!
            if (!wp_verify_nonce($nonce, 'plugin_activate_nonce')) {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => esc_html__('Error - unable to verify nonce, please try again.', 'hashthemes-demo-importer')
                        )
                );
                die();
            }

            if (file_exists($plugin_file)) {
                activate_plugin($plugin_file);
                wp_send_json_success();
            } else {
                wp_send_json(
                        array(
                            'success' => false,
                            'message' => __('Error!! Plugin not activated.', 'hashthemes-demo-importer')
                        )
                );
            }

            die();
        }

        public function get_local_dir_path($file_location, $plugin) {

            if (isset($file_location)) {
                $upload_dir = wp_upload_dir();

                $upload_path = $upload_dir['path'] . '/' . $plugin . '.zip';

                $url = wp_nonce_url(admin_url('themes.php?page=hdi-recommended-plugins'), 'remote-file-installation');
                if (false === ($creds = request_filesystem_credentials($url, '', false, false, NULL))) {
                    return; // stop processing here
                }

                if (!WP_Filesystem($creds)) {
                    request_filesystem_credentials($url, '', true, false, NULL);
                    return;
                }

                global $wp_filesystem;
                $file = $wp_filesystem->get_contents($file_location);

                $wp_filesystem->put_contents($upload_path, $file, FS_CHMOD_FILE);

                return $upload_path;
            }
        }

    }

}

HDI_Demo_Importer::get_instance();
