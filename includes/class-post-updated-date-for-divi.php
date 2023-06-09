<?php

/**
 * The file that defines the core plugin class.
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @see       https://www.linknacional.com/
 * @since      1.0.0
 */

/*
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 *
 * @author     Link Nacional
 */
if ( ! class_exists('Lkn_Post_Updated_Date_For_Divi') ) {
    final class Lkn_Post_Updated_Date_For_Divi {
        private $loader;
        private $plugin_name;
        private $version;
        private static $instance = false;

        public function __construct() {
            if ( defined( 'LKN_PUDD_VERSION' ) ) {
                $this->version = LKN_PUDD_VERSION;
            } else {
                $this->version = '1.0.0';
            }
            $this->plugin_name = 'post-updated-date-for-divi';

            $this->load_dependencies();
            $this->set_locale();

            add_action('init', array($this, 'init'));
        }

        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function init(): void {
            add_action( 'get_the_date', array($this, 'et_last_modified_date_blog'));
            add_filter( 'get_the_time', array($this, 'et_last_modified_date_blog'));
            add_filter('post_date_column_status', array($this, 'change_published_date_text'));
            add_filter('post_date_column_time', array($this, 'change_published_time_text'));
        }

        public function et_last_modified_date_blog() {
            $the_time = get_post_time( 'd/m/y H:i:s' );
            $the_modified = get_post_modified_time( 'd/m/y H:i:s' );

            if ('post' === get_post_type()) {
                $the_published = get_post_time( 'd/m/y, H:i' );
                $the_updated = get_post_modified_time( 'd/m/y, H:i' );

                return $the_modified !== $the_time ? $the_updated : $the_published;
            }
        }

        public function change_published_time_text() {
            $date_format = get_option('date_format');
            $time_format = get_option('time_format');

            $the_time = get_post_time( 'd/m/y H:i:s' );
            $the_modified = get_post_modified_time( 'd/m/y H:i:s' );

            if ('post' === get_post_type()) {
                $text_time_updated = get_post_modified_time($date_format . ' ', false, null, true)
                . __( 'at', 'post-updated-date-for-divi' ) . get_post_modified_time(' ' . $time_format, false, null, true);
                $text_time_published = get_post_time($date_format . ' ', false, null, true)
                . __( 'at', 'post-updated-date-for-divi' ) . get_post_time(' ' . $time_format, false, null, true);

                return $the_modified !== $the_time ? $text_time_updated : $text_time_published;
            }
        }

        public function change_published_date_text() {
            if ('post' === get_post_type()) {
                $the_time = get_post_time( 'd/m/y H:i:s' );
                $the_modified = get_post_modified_time( 'd/m/y H:i:s' );

                $text_updated = __( 'Updated:', 'post-updated-date-for-divi' );
                $text_published = __( 'Published:', 'post-updated-date-for-divi' );

                return $the_time !== $the_modified ? $text_updated : $text_published;
            }
        }

        /**
         * Run the loader to execute all of the hooks with WordPress.
         *
         * @since    1.0.0
         */
        public function run(): void {
            $this->loader->run();
        }

        /**
         * The name of the plugin used to uniquely identify it within the context of
         * WordPress and to define internationalization functionality.
         *
         * @since     1.0.0
         *
         * @return string the name of the plugin
         */
        public function get_plugin_name() {
            return $this->plugin_name;
        }

        /**
         * The reference to the class that orchestrates the hooks with the plugin.
         *
         * @since     1.0.0
         *
         * @return Lkn_Post_Updated_Date_For_Divi_Loader orchestrates the hooks of the plugin
         */
        public function get_loader() {
            return $this->loader;
        }

        /**
         * Retrieve the version number of the plugin.
         *
         * @since     1.0.0
         *
         * @return string the version number of the plugin
         */
        public function get_version() {
            return $this->version;
        }

        /**
         * Load the required dependencies for this plugin.
         *
         * Include the following files that make up the plugin:
         *
         * - Lkn_Post_Updated_Date_For_Divi_Loader. Orchestrates the hooks of the plugin.
         * - Lkn_Post_Updated_Date_For_Divi_i18n. Defines internationalization functionality.
         *
         * Create an instance of the loader which will be used to register the hooks
         * with WordPress.
         *
         * @since    1.0.0
         */
        private function load_dependencies(): void {
            /**
             * The class responsible for orchestrating the actions and filters of the
             * core plugin.
             */
            require_once plugin_dir_path( __DIR__ ) . 'includes/class-post-updated-date-for-divi-loader.php';

            /**
             * The class responsible for defining internationalization functionality
             * of the plugin.
             */
            require_once plugin_dir_path( __DIR__ ) . 'includes/class-post-updated-date-for-divi-i18n.php';

            $this->loader = new Lkn_Post_Updated_Date_For_Divi_Loader();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Lkn_Post_Updated_Date_For_Divi_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since    1.0.0
         */
        private function set_locale(): void {
            $plugin_i18n = new Lkn_Post_Updated_Date_For_Divi_i18n();

            $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
        }
    }

    Lkn_Post_Updated_Date_For_Divi::get_instance();
}
