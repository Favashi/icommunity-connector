<?php

use ElementorPro\Modules\Forms\Classes\Action_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * IBS API connection.
 *
 * Custom Elementor form action which will connect to the ibs API to make an evidence.
 *
 * @since 1.0.0
 */
class Icommunity_Connection_Action_After_Submit extends Action_Base {

    /**
     * Get action name.
     *
     * Retrieve IBS connection action name.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_name() {
        return 'IBS Evidence';
    }

    /**
     * Get action label.
     *
     * Retrieve ping action label.
     *
     * @since 1.0.0
     * @access public
     * @return string
     */
    public function get_label() {
        return esc_html__( 'IBS Evidence', 'ibs-connect-elementor' );
    }

    /**
     * Register action controls.
     *
     * Add input for the API Key and signature id to the new action.
     *
     * @since 1.0.0
     * @access public
     * @param \Elementor\Widget_Base $widget
     */
    public function register_settings_section( $widget ) {
        $widget->start_controls_section(
            'section_ibs',
            [
                'label' => esc_html__( 'IBS connection', 'ibs-connect-elementor' ),
                'condition' => [
                    'submit_actions' => $this->get_name(),
                ],
            ]
        );

        $widget->add_control(
            'ibs_evidence_title',
            [
                'label' => esc_html__( 'Title field id', 'ibs-connect-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( "The unique input id from this form's title field", 'ibs-connect-elementor' ),
            ]
        );

        $widget->add_control(
            'ibs_evidence_file',
            [
                'label' => esc_html__( 'File field id', 'ibs-connect-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( "The unique input id from this form's file field", 'ibs-connect-elementor' ),
            ]
        );

        $widget->add_control(
            'ibs_evidence_file_name',
            [
                'label' => esc_html__( 'File name id', 'ibs-connect-elementor' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'description' => esc_html__( "The unique input id from this form's file name field - it's not required, but as elementor changes the uploaded file name, it's necessary if you want to keep the original name.", 'ibs-connect-elementor' ),
            ]
        );


        $widget->end_controls_section();

    }

    /**
     * Run action.
     *
     * Filter data from form submission.
     *
     * @since 1.0.0
     * @access public
     * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
     * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
     */
    public function run( $record, $ajax_handler ) {

        $settings = $record->get( 'form_settings' );

        //  Make sure that there is a title field id.
        if ( empty( $settings['ibs_evidence_title'] ) ) {
            return;
        }

        //  Make sure that there is a file field id.
        if ( empty( $settings['ibs_evidence_file'] ) ) {
            return;
        }

        // Get submitted form data.
        $raw_fields = $record->get( 'fields' );

        // Normalize form data.
        $fields = [];
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = $field['value'];
        }

        // Make sure the user entered a title and file.
        if ( empty( $fields[ $settings['ibs_evidence_title'] ] ) || empty( $fields[ $settings['ibs_evidence_file'] ] ) ) {
            return;
        }

        if( !empty( $fields[ $settings['ibs_evidence_file_name'] ] ) ) {
            $file_name = $fields[ $settings['ibs_evidence_file_name'] ];
        } else {
            $file_path = $fields[ $settings['ibs_evidence_file'] ];
            $exploded_path = explode('/', $file_path);
            $file_name = end($exploded_path);

        }

        $file_content = file_get_contents($fields[ $settings['ibs_evidence_file'] ]);

        $user = wp_get_current_user();
        $signature = get_user_meta( $user->ID,'signature_id',true);
        $evidence = $this->ibs_make_evidence($signature, $file_name, $file_content, $fields[ $settings['ibs_evidence_title'] ] );

        if($evidence) {

            $user_id = get_current_user_id();

            $exploded_evidence_path = explode('/', $evidence);
            $evidence_id = end($exploded_evidence_path);

            $certification_data_post = array (
                'post_title'          => $fields[ $settings['ibs_evidence_title'] ],
                'post_content'        => '',
                'post_type'           => 'ibs_evidence',
                'post_status'         => 'publish',
                'post_author'         => $user_id,
                'comment_status'      => 'closed',
                'meta_input'          => array(
                    'evidence_id'      => $evidence_id,
                    'evidence_status' => Evidence_Status::WAITING
                )
            );

            wp_insert_post($certification_data_post);

        }
    }

    /**
     * Make evidence.
     *
     * Connects to IBS API and make an evidence after form submission.
     *
     * @since 1.0.0
     * @access private
     * @param string  $signature_id
     * @param string  $file_name
     * @param string  $file_content
     * @param string  $title
     */
    private function ibs_make_evidence( $signature_id, $file_name, $file_content, $title) : string {
        // Prepare IBS API call
        $options = get_option( 'icommunity_connector_plugin_options' );

        if(empty($options) || is_null($options['api_url']) || is_null('api_token')){
            return new WP_Error( 'broke', __( 'Compruebe que la URL y Token están definidos antes de continuar', 'icommunity-connector' ) );
        }

        $api_url = $options['api_url'];
        $api_token = $options['api_token'];

        $signature = new \stdClass();
        $signature->id = $signature_id;
        $signatures = array($signature);

        $file = new \stdClass();
        $file->name = $file_name;
        $file->file = base64_encode($file_content);

        $files = array($file);

        $payload = new \stdClass();
        $payload->title = $title;
        $payload->files = $files;

        // call IBS

        $response = wp_remote_post(
            $api_url . 'evidences',
            [
                'method' => 'POST',
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_token,
                    'Content-Type' => 'application/json',
                ],
                'body' => wp_json_encode([
                    'payload' => $payload,
                    'signatures' => $signatures
                ]),
                'timeout' => 60,
            ]
        );

        if ( !is_wp_error( $response ) && ($response['response']['code'] >= 200) && ($response['response']['code'] < 400) ) {
            return wp_remote_retrieve_header( $response, 'Location' );
        } else {
            $error_message = $response->get_error_message();
            throw new Exception( $error_message );
        }

    }

    /**
     * On export.
     *
     * Clears IBS form settings/fields when exporting.
     *
     * @since 1.0.0
     * @access public
     * @param array $element
     */
    public function on_export( $element ) {
        unset(
            $element['ibs_evidence_title'],
            $element['ibs_evidence_file']
        );

        return $element;
    }

}