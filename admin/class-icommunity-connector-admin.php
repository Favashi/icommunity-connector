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
        add_settings_field( 'icommunity_setting_verified_role', __( 'Rol de verificado', 'icommunity-connector' ), array($this,'plugin_setting_verified_role'), 'icommunity-connector', 'api_settings' );
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

    public function plugin_setting_verified_role(){
        $options = get_option( 'icommunity_connector_plugin_options' );
        echo "<select name='icommunity_connector_plugin_options[verified_role]'>";
        echo wp_dropdown_roles($options['verified_role'] );
        echo "</select>";
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
     * Adds Custom Column To Evidence List Table
     * @param $columns
     * @since 1.0.0
     */
    function custom_add_evidence_status_column($columns) {
        $post_type = get_post_type();

        if ($post_type == 'ibs_evidence') {
            unset($columns['date']);
            $columns['evidence_id'] = 'Evidence ID';
            $columns['evidence_network'] = 'Network';
            $columns['certification_hash'] = 'Certification Hash';
            $columns['certification_timestamp'] = 'Certification Timestamp';
            $columns['evidence_status'] = 'Evidence Status';
        }
        return $columns;
    }
    /**
     * Adds Content To The Custom Evidence Added Column
     * @param $value
     * @param $column_name
     * @param $evidence_id
     * @since 1.0.0
     */
    function custom_show_evidence_status_column_content($column_name, $evidence_id) {
        switch ($column_name){
            case 'evidence_id':
                echo get_post_meta( $evidence_id,'evidence_id',true);
                break;
            case 'evidence_network':
                echo get_post_meta( $evidence_id,'network',true);
                break;
            case 'certification_hash':
                echo get_post_meta( $evidence_id,'certification_hash',true);
                break;
            case 'certification_timestamp':
                echo get_post_meta( $evidence_id,'certification_timestamp',true);
                break;
            case 'evidence_status':
                echo $this->get_status_label(get_post_meta( $evidence_id,'evidence_status',true));
                break;
        }
    }

    /**
     * Adds Content To The Custom User Added Column
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
     * Returns status label with styles
     * @param $status
     * @return string
     * @since 1.0.0
     */
    public function get_status_label($status){
        switch ($status) {
            case Kyc_Status::PENDING:
            case Evidence_Status::WAITING:
                $type = 'warning';
                break;
            case Kyc_Status::SUCCESS:
            case Evidence_Status::CERTIFIED:
                    $type = 'success';
                    break;
            case Kyc_Status::FAILED:
                $type = 'error';
                break;
            default:
                $type = 'info hidden';
        }
        return '<span class="notice notice-'.$type.'" inline>'.$status.'<span>';
    }

    /**
     * Verifies signature status and calls creation if needed on registering
     * @param int $userid
     * @return void
     * @throws Exception
     */
    public function verify_signature_status_register_user(int $userid){
        $user = get_user_by('id',$userid);
        $this->verify_signature_status($user->user_login,$user);
    }

    /**
     * Verifies signature status and calls creation if needed on login
     * @param string $user_login
     * @param WP_User $user
     * @return void|WP_Error
     * @throws Exception
     * @since 1.0.0
     */
    public function verify_signature_status(string $user_login, WP_User $user){

            $options = get_option( 'icommunity_connector_plugin_options' );

            if(empty($options) || is_null($options['api_url']) || is_null('api_token')){
                return new WP_Error( 'broke', __( 'Compruebe que la URL y Token están definidos antes de continuar', 'icommunity-connector' ) );
            }

            $api_url = $options['api_url'];
            $api_token = $options['api_token'];

            $signature_status = get_user_meta( $user->ID,'signature_status',true);
            switch ($signature_status){
                case '':
                    $this->remote_create_signature($user, $api_url, $api_token);
                    break;
                case Kyc_Status::FAILED:
                    $this->remote_retry_signature($user, $api_url, $api_token);
                    break;
            }
    }

    /**
     * Calls to create new Signature to signatures endpoint
     * @param WP_User $user
     * @param string $api_url
     * @param string $api_token
     * @return void
     * @throws Exception
     * @since 1.0.0
     */
    public function remote_create_signature(WP_User $user, string $api_url, string $api_token){
        $userdata = get_userdata($user->ID);
        $signature_name = empty($userdata->user_firstname) || empty($userdata->user_lastname) ? $user->user_nicename.'-'.$user->ID : sanitize_title($userdata->user_firstname).'-'.sanitize_title($userdata->user_lastname).'-'.$user->ID;

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

        if (is_wp_error( $request )) {
            $error_message = $request->get_error_message();
            throw new Exception( $error_message );
        }
        elseif(wp_remote_retrieve_response_code( $request ) >= 400) {
            return;
        }
        else{
            $response = json_decode( wp_remote_retrieve_body( $request ), true );
            $status = Kyc_Status::PENDING;
            update_user_meta( $user->ID, 'signature_id', $response["signature_id"] );
            update_user_meta($user->ID, 'signature_url', $response["url"]);
            update_user_meta( $user->ID, 'signature_status', $status );
        }
    }

    /**
     * Calls to retry remote signature validation process
     * @param WP_User $user
     * @param string $api_url
     * @param string $api_token
     * @return void
     * @throws Exception
     * @since 1.0.0
     */
    public function remote_retry_signature(WP_User $user, string $api_url, string $api_token){
        $signature_id = get_user_meta( $user->ID,'signature_id',true);

        $request = wp_remote_post(
            $api_url . 'signatures/'.$signature_id,
            [
                'method' => 'PUT',
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_token,
                    'Content-Type' => 'application/json',
                ],
                'timeout' => 60,
            ]
        );

        if (is_wp_error( $request )) {
            $error_message = $request->get_error_message();
            throw new Exception( $error_message );
        }
        elseif(wp_remote_retrieve_response_code( $request ) >= 400) {
            return;
        }
        else{
            $status = Kyc_Status::PENDING;
            update_user_meta( $user->ID, 'signature_status', $status );
        }
    }

    /**
     * Registers KYC Signature endpoint
     * @return void
     * @since 1.0.0
     */
    public function register_signature_rest_endpoint(){
        register_rest_route( 'icommunity-connector/v1', '/signature', array(
            'methods' => 'POST',
            'callback' => array($this,'update_signature_status'),
            'permission_callback' => '__return_true',
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

    /**
     * Updates KYC Signature status
     * @param WP_REST_Request $request
     * @return void|WP_Error
     * @since 1.0.0
     */
    public function update_signature_status(WP_REST_Request $request) {
        $options = get_option( 'icommunity_connector_plugin_options' );

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
        $status =  strtoupper(substr($parameters['event'], $idx + 1));

        update_user_meta( $user->ID, 'signature_status', $status);

        if($status == Kyc_Status::SUCCESS) $user->add_role($options['verified_role']);
    }

    /**
     * Registers Evidence rest endpoint
     * @return void
     * @since 1.2.0
     */
    public function register_evidence_rest_endpoint(){
        register_rest_route( 'icommunity-connector/v1', '/evidence', array(
            'methods' => 'POST',
            'callback' => array($this,'update_evidence_status'),
            'permission_callback' => '__return_true',
            'args' => array(
                'event' => array(
                    'required' => true,
                    'type' => 'string',
                    'description' => 'Evidence certification status',
                     'enum' => array(
                        'evidence.waiting',
                        'evidence.certified'
                     ),
                ),
                'data' => array(
                    'required' => true,
                    'type' => 'object',
                    'description' => 'Data object containing evidence information',
                    'properties' => array(
                        'signature_id' => array(
                            'required' => 'true',
                            'type' => 'string'
                        ),
                        'network' => array(
                            'required' => 'true',
                            'type' => 'string'
                        ),
                        'evidence_id' => array(
                            'required' => 'true',
                            'type' => 'string'
                        ),
                        'certification_hash' => array(
                            'required' => 'true',
                            'type' => 'string'
                        ),
                        'certification_timestamp' => array(
                            'required' => 'true',
                            'type' => 'string'
                        ),
                        'payload' => array(
                            'required' => true,
                            'type' => 'object',
                            'description' => 'Object containing payload content',
                            'properties' => array(
                                'title' => array(
                                    'required' => 'true',
                                    'type' => 'string'
                                ),
                                'integrity' => array(
                                    'required' => true,
                                    'type' => 'object',
                                    'description' => 'Object containing integrity data',
                                    'properties' => array(
                                        'name' => array(
                                            'required' => 'true',
                                            'type' => 'string'
                                        ),
                                        'type' => array(
                                            'required' => 'true',
                                            'type' => 'string'
                                        ),
                                        'checksum' => array(
                                            'required' => 'true',
                                            'type' => 'string'
                                        ),
                                        'algorithm' => array(
                                            'required' => 'true',
                                            'type' => 'string'
                                        ),
                                        'sanitizer' => array(
                                            'required' => 'true',
                                            'type' => 'string'
                                        ),
                                    )
                                )
                            )
                        )
                    )
                )
            )
        ));
    }

    /**
     * Updates Evidence status and data from IBS
     * @param WP_REST_Request $request
     * @return void|WP_Error
     * @since 1.2.0
     */
    public function update_evidence_status(WP_REST_Request $request) {

        $parameters = $request->get_json_params();

        $post =  reset(get_posts(array(
            'numberposts'   => 1,
            'post_type'     => 'ibs_evidence',
            'meta_key'      => 'evidence_id',
            'meta_value'    => $parameters['data']['evidence_id'],
        )));


        if ( empty( $post ) ) {
            return new WP_Error( 'no_evidence', 'No Evidence matches evidence_id', array( 'status' => 404 ) );
        }
        $idx = strrpos($parameters['event'], '.');
        $status =  strtoupper(substr($parameters['event'], $idx + 1));


        $metaValues = array(
            'evidence_status' =>  $status,
            'network' =>  $parameters['data']['network'],
            'certification_hash' =>  $parameters['data']['certification_hash'],
            'certification_timestamp' =>  $parameters['data']['certification_timestamp'],
            'integrity_title' => $parameters['data']['payload']['title'],
            'integrity_name' => $parameters['data']['payload']['integrity'][0]['name'],
            'integrity_type' => $parameters['data']['payload']['integrity'][0]['type'],
            'integrity_checksum' => $parameters['data']['payload']['integrity'][0]['checksum'],
            'integrity_algorithm' => $parameters['data']['payload']['integrity'][0]['algorithm'],
            'integrity_sanitizer' => $parameters['data']['payload']['integrity'][0]['sanitizer']
        );

        wp_update_post(array(
            'ID'        => $post->ID,
            'meta_input'=> $metaValues,
        ));
    }
}
