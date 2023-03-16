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
if ( ! class_exists('Post_Updated_Date_For_Divi') ) {
    final class Post_Updated_Date_For_Divi {
        /**
         * The loader that's responsible for maintaining and registering all hooks that power
         * the plugin.
         *
         * @since    1.0.0
         *
         * @var Post_Updated_Date_For_Divi_Loader maintains and registers all hooks for the plugin
         */
        private $loader;

        /**
         * The unique identifier of this plugin.
         *
         * @since    1.0.0
         *
         * @var string the string used to uniquely identify this plugin
         */
        private $plugin_name;

        /**
         * The current version of the plugin.
         *
         * @since    1.0.0
         *
         * @var string the current version of the plugin
         */
        private $version;
        private static $instance = false;

        /**
         * Define the core functionality of the plugin.
         *
         * Set the plugin name and the plugin version that can be used throughout the plugin.
         * Load the dependencies, define the locale, and set the hooks for the admin area and
         * the public-facing side of the site.
         *
         * @since    1.0.0
         */
        public function __construct() {
            if ( defined( 'LKN_DPMD_VERSION' ) ) {
                $this->version = LKN_DPMD_VERSION;
            } else {
                $this->version = '1.0.2';
            }
            $this->plugin_name = 'post-updated-date-for-divi';

            $this->load_dependencies();
            $this->set_locale();
            $this->define_admin_hooks();
            $this->define_public_hooks();

            add_action('init', array($this, 'lkn_dpmd_init'));
        }

        public function lkn_dpmd_register_settings(): void {
            register_setting('lkn_dpmd_settings', 'lkn_dpmd_text');
        }

        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        public function lkn_dpmd_init(): void {
            // add_filter( 'register_post_type_args', array($this, 'wpse247328_register_post_type_args'));
            // add_action( 'init', array($this, 'acelerar_blog_link'));

            add_action( 'get_the_date', array($this, 'et_last_modified_date_blog'));
            add_action( 'get_the_time', array($this, 'et_last_modified_date_blog'));
        }

        public function et_last_modified_date_blog() {
            if ( 'post' === get_post_type() ) {
                $the_time = get_post_time( 'H:i:s' );
                $the_modified = get_post_modified_time( 'H:i:s' );
                $the_modified2 = get_post_modified_time( 'd/m/y, H:i', false, null, true);

                $last_modified = __( 'Updated', 'post-updated-date-for-divi' ) . ' ' . $the_modified2;

                return $the_modified !== $the_time ? $last_modified : get_post_time( 'M j, Y' );
            }
        }

        public function acelerar_blog_link(): void {
            remove_action('wp_head', 'print_emoji_detection_script', 7);
            remove_action('wp_print_styles', 'print_emoji_styles');
            remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
            remove_action( 'admin_print_styles', 'print_emoji_styles' );

            remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

            add_action( 'wp_footer', wp_dequeue_script( 'wp-embed' ));

            remove_action( 'wp_head', 'wlwmanifest_link' );

            add_action('wp_enqueue_scripts', 'deregister_qjuery');

            if ( ! is_admin() ) {
                add_action('wp_enqueue_scripts', wp_deregister_script('jquery'));
            }

            add_action( 'init', wp_deregister_script('heartbeat'), 1 );

            // add_action( 'wp_print_styles',wp_deregister_style( 'dashicons' ), 100 );
            wp_deregister_style( 'dashicons' );

            /*if (!current_user_can( 'update_core' )) {
                add_action( 'wp_enqueue_scripts', wp_deregister_style('dashicons'));
            }*/
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
         * @return Post_Updated_Date_For_Divi_Loader orchestrates the hooks of the plugin
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
         * - Post_Updated_Date_For_Divi_Loader. Orchestrates the hooks of the plugin.
         * - Post_Updated_Date_For_Divi_i18n. Defines internationalization functionality.
         * - Post_Updated_Date_For_Divi_Admin. Defines all hooks for the admin area.
         * - Post_Updated_Date_For_Divi_Public. Defines all hooks for the public side of the site.
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

            /**
             * The class responsible for defining all actions that occur in the admin area.
             */
            require_once plugin_dir_path( __DIR__ ) . 'admin/class-post-updated-date-for-divi-admin.php';

            /**
             * The class responsible for defining all actions that occur in the public-facing
             * side of the site.
             */
            require_once plugin_dir_path( __DIR__ ) . 'public/class-post-updated-date-for-divi-public.php';

            $this->loader = new Post_Updated_Date_For_Divi_Loader();
        }

        /**
         * Define the locale for this plugin for internationalization.
         *
         * Uses the Post_Updated_Date_For_Divi_i18n class in order to set the domain and to register the hook
         * with WordPress.
         *
         * @since    1.0.0
         */
        private function set_locale(): void {
            $plugin_i18n = new Post_Updated_Date_For_Divi_i18n();

            $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
        }

        /**
         * Register all of the hooks related to the admin area functionality
         * of the plugin.
         *
         * @since    1.0.0
         */
        private function define_admin_hooks(): void {
            $plugin_admin = new Post_Updated_Date_For_Divi_Admin( $this->get_plugin_name(), $this->get_version() );

            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
            $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        }

        /**
         * Register all of the hooks related to the public-facing functionality
         * of the plugin.
         *
         * @since    1.0.0
         */
        private function define_public_hooks(): void {
            $plugin_public = new Post_Updated_Date_For_Divi_Public( $this->get_plugin_name(), $this->get_version() );

            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        }
    }

    Post_Updated_Date_For_Divi::get_instance();
}
