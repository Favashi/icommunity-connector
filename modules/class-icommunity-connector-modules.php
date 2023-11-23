<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/admin
 */

/**
 * The external dependant functionality of the plugin.
 *
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/modules
 * @author     Toni Ruiz <info@toniruiz.es>
 */
class ICommunity_Connector_Modules {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $icommunity_connector    The ID of this plugin.
	 */
	private $icommunity_connector;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $icommunity_connector       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $icommunity_connector, $version ) {

		$this->icommunity_connector = $icommunity_connector;
		$this->version = $version;
	}

    private function load_dependencies()
    {
        include_once plugin_dir_path(dirname(__FILE__)) . 'modules/form-action/class-icommunity-connection-after-submit.php';
    }

    function ibs_add_evidence_post_type() {
        $args_evidence = array(
            'labels' => array(
                'name'                => _x( 'Evidences', 'Post Type General Name', 'ibs-connect-elementor' ),
                'singular_name'       => _x( 'Evidence', 'Post Type Singular Name', 'ibs-connect-elementor' ),
                'all_items'           => __( 'All evidence', 'ibs-connect-elementor' ),
                'view_item'           => __( 'View evidence', 'ibs-connect-elementor' ),
                'add_new_item'        => __( 'Add new post', 'ibs-connect-elementor' ),
                'search_items'        => __( 'Search evidence', 'ibs-connect-elementor' ),
                'not_found'           => __( 'Not Found', 'ibs-connect-elementor' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'ibs-connect-elementor' ),
            ),
            'description' => 'A custom post type to hold the blockchain registration data',
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'menu_position' => 20,
            'supports' => array ('title', 'editor', 'excerpt', 'author', 'custom-fields', 'thumbnail'),
            'taxonomies' => array( 'Owner' ),
            'delete_with_user' => false,
        );

        register_post_type( 'ibs_evidence', $args_evidence);
    }

    function ibs_evidence_meta_register() {
        $args_evidence_id = array (
            'object_subtype' => 'ibs_evidence',
            'type' => 'string',
            'description' => 'The evidence id',
            'single' => true,
            'show_in_rest' => true
        );

        register_meta('post', 'ibs_evidence_id', $args_evidence_id);

    }

    /**
     * Add new form action after form submission.
     *
     * @since 1.0.0
     * @param ElementorPro\Modules\Forms\Registrars\Form_Actions_Registrar $form_actions_registrar
     * @return void
     */
    function ibs_add_connection_action( $form_actions_registrar ) {

        include_once plugin_dir_path(dirname(__FILE__)) . 'modules/form-action/class-icommunity-connection-after-submit.php';

        $form_actions_registrar->register( new Icommunity_Connection_Action_After_Submit());

    }
}
