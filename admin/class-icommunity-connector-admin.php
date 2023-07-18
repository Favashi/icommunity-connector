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
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/admin
 * @author     Toni Ruiz <info@toniruiz.es>
 */
class Icommunity_Connector_Admin {

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

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Icommunity_Connector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Icommunity_Connector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->icommunity_connector, plugin_dir_url(__FILE__) . 'css/icommunity-connector-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Icommunity_Connector_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Icommunity_Connector_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->icommunity_connector, plugin_dir_url(__FILE__) . 'js/icommunity-connector-admin.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * Add options page
     * @since   1.0.0
     */
    public function add_settings_page()
    {
        add_options_page( 'IBS Connector', 'IBS Connector', 'manage_options', $this->icommunity_connector, array(
            $this,
            'render_settings_page'
        ));
    }

    public function render_settings_page(){
        include( plugin_dir_path( __FILE__ ) . 'partials/icommunity-connector-admin-display.php' );
    }

    /**
     * Registers settings on options page
     * @since 1.0.0
     */
    public function register_settings() {
        //array($this,'dbi_ibs_connector_plugin_options')
        register_setting( 'icommunity_connector_plugin_options', 'icommunity_connector_plugin_options');
        add_settings_section( 'api_settings', __('API Settings','icommunity-connector'), array($this,'plugin_section_text'), 'icommunity-connector' );

        add_settings_field( 'icommunity_setting_api_url', __('Url de API','icommunity-connector'), array($this,'plugin_setting_api_url'), 'icommunity-connector', 'api_settings' );
        add_settings_field( 'icommunity_setting_api_token', __('Token de acceso','icommunity-connector'), array($this,'plugin_setting_api_token'), 'icommunity-connector', 'api_settings' );
    }

    public function plugin_section_text() {
        echo '<p>'.__('Configuración de conectividad con la API de IBS','icommunity-connector').'</p>';
    }

    public function plugin_setting_api_url() {
        $options = get_option( 'icommunity_connector_plugin_options' );
        echo "<input class='large-text' id='icommunity_setting_api_token' name='icommunity_connector_plugin_options[api_url]' type='text' value='" . esc_attr( $options['api_url'] ) . "' />";
    }
    public function plugin_setting_api_token() {
        $options = get_option( 'icommunity_connector_plugin_options' );
        echo "<input class='large-text' id='icommunity_setting_api_token' name='icommunity_connector_plugin_options[api_token]' type='text' value='" . esc_attr( $options['api_token'] ) . "' />";
    }

    /**
     * Adds Custom Column To Users List Table
     * @param $columns
     * @since 1.0.0
     */
    function custom_add_signature_status_column($columns) {
        $columns['signature_status'] = 'Signature status';
        return $columns;
    }

    /**
     * Adds Content To The Custom Added Column
     * @param $value
     * @param $column_name
     * @param $user_id
     * @since 1.0.0
     */
    function custom_show_signature_status_column_content($value, $column_name, $user_id) {
        if ( 'signature_status' == $column_name )
            return $this->get_status_label(get_user_meta( $user_id,'signature_status',true));
        return $value;
    }

    /**
     * Displays user profile field for KYC status
     * @param $user
     * @since 1.0.0
     */
    public function display_signature_user_profile_field( $user ) { ?>
        <h3><?php echo __('Estado de KYC', 'icommunity-connector'); ?></h3>
        <table class="form-table">
            <tr>
                <th><label for="address"><?php echo  __('Estado de la firma','icommunity-connector'); ?></label></th>
                <td>
                    <?php echo $this->get_status_label(get_user_meta( $user->ID,'signature_status',true)); ?>
                </td>
        </table>
    <?php }

    /**
     * Returns KYC status label with styles
     * @param $status
     * @return string
     * @since 1.0.0
     */
    public function get_status_label($status){
        switch ($status) {
            case 'PENDING':
                $type = 'warning';
                break;
            case 'SUCCESS':
                $type = 'success';
                break;
            case 'FAILED':
                $type = 'error';
                break;
            default:
                $type = 'info hidden';
        }
        return '<span class="notice notice-'.$type.'" inline>'.$status.'<span>';
    }

    public function add_signature_status_meta( $user_id ) {

        $options = get_option( 'icommunity_connector_plugin_options' );

        if(empty($options) || is_null($options['api_url']) || is_null('api_token')){
            return new WP_Error( 'broke', __( 'Compruebe que la URL y Token están definidos antes de continuar', 'icommunity-connector' ) );
        }

        $api_url = $options['api_url'];
        $api_token = $options['api_token'];
        $signature_name = $user_id;

        $request = wp_remote_post(
            $api_url . 'signatures',
            [
                'method' => 'POST',
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_token,
                    'Content-Type' => 'application/json',
                ],
                'body' => wp_json_encode([
                    'signature_name' => $signature_name
                ]),
                'timeout' => 60,
            ]
        );

        if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 201 ) {
            $status = 'FAILED';
        }
        else{
            $response = json_decode( wp_remote_retrieve_body( $request ), true );
            $status = 'PENDING';
            update_user_meta( $user_id, 'signature_id', $response["signature_id"] );
            update_user_meta($user_id, 'signature_url', $response["url"]);
        }

        update_user_meta( $user_id, 'signature_status', $status );

    }

    public function register_signature_rest_endpoint(){
        register_rest_route( 'icommunity-connector/v1', '/signature', array(
            'methods' => 'POST',
            'callback' => array($this,'update_signature_status'),
            'args' => array(
                'event' => array(
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Signature verification status',
                    'enum' => array(
                        'signature.verification.success',
                        'signature.verification.fail'
                    ),
                ),
                'data' => array(
                    'required' => true,
                    'type' => 'object',
                    'description' => 'Data object containing signature_id',
                    'properties' => array(
                        'signature_id' => array(
                            'required' => 'true',
                            'type' => 'string'
                        )
                    )
                )
            )
        ));
    }

    public function update_signature_status(WP_REST_Request $request) {

        $parameters = $request->get_json_params();

        $user = reset(
            get_users(
                array(
                    'meta_key' => 'signature_id',
                    'meta_value' => $parameters['data']['signature_id'],
                    'number' => 1
                )
            )
        );

        if ( empty( $user ) ) {
            return new WP_Error( 'no_user', 'No user matches signature_id', array( 'status' => 404 ) );
        }
        $idx = strrpos($parameters['event'], '.');
        $status = substr($parameters['event'], $idx + 1);

        update_user_meta( $user->ID, 'signature_status', strtoupper($status) );
    }
}
