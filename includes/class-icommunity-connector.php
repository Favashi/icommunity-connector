<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/includes
 * @author     Toni Ruiz <info@toniruiz.es>
 */
class Icommunity_Connector {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Icommunity_Connector_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $icommunity_connector    The string used to uniquely identify this plugin.
	 */
	protected $icommunity_connector;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

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
		if ( defined( 'ICOMMUNITY_CONNECTOR_VERSION' ) ) {
			$this->version = ICOMMUNITY_CONNECTOR_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->icommunity_connector = 'icommunity-connector';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Icommunity_Connector_Loader. Orchestrates the hooks of the plugin.
	 * - Icommunity_Connector_i18n. Defines internationalization functionality.
	 * - Icommunity_Connector_Admin. Defines all hooks for the admin area.
	 * - Icommunity_Connector_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-icommunity-connector-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-icommunity-connector-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-icommunity-connector-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-icommunity-connector-public.php';

        /**
         * Enumerator for KYC status
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/enum-kyc-status.php';

		$this->loader = new Icommunity_Connector_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Icommunity_Connector_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Icommunity_Connector_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Icommunity_Connector_Admin( $this->get_icommunity_connector(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'register_settings' );
        $this->loader->add_filter( 'manage_users_columns', $plugin_admin, 'custom_add_signature_status_column'); //admin
        $this->loader->add_filter( 'manage_users_custom_column', $plugin_admin, 'custom_show_signature_status_column_content', 10, 3); //admin
        $this->loader->add_action( 'show_user_profile', $plugin_admin, 'display_signature_user_profile_field' );
        $this->loader->add_action( 'edit_user_profile', $plugin_admin, 'display_signature_user_profile_field' );

        $this->loader->add_action( 'rest_api_init', $plugin_admin, 'register_signature_rest_endpoint');
        $this->loader->add_action( 'user_register', $plugin_admin, 'verify_signature_status_register_user');
        $this->loader->add_action('wp_login', $plugin_admin, 'verify_signature_status',10, 2);
    }

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Icommunity_Connector_Public( $this->get_icommunity_connector(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_shortcode('validate_signature', $plugin_public, 'validate_signature_shortcode');

    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_icommunity_connector() {
		return $this->icommunity_connector;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Icommunity_Connector_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
