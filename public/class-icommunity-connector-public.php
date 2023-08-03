<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Icommunity_Connector
 * @subpackage Icommunity_Connector/public
 * @author     Toni Ruiz <info@toniruiz.es>
 */
class Icommunity_Connector_Public {

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
	 * @param      string    $icommunity_connector       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $icommunity_connector, $version ) {

		$this->icommunity_connector = $icommunity_connector;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->icommunity_connector, plugin_dir_url(__FILE__) . 'css/icommunity-connector-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->icommunity_connector, plugin_dir_url(__FILE__) . 'js/icommunity-connector-public.js', array( 'jquery' ), $this->version, false );

	}

    /**
     * The [validate_signature] shortcode.
     *
     * Accepts a title and will display a validation button.
     *
     * @param array  $atts    Shortcode attributes. Default empty.
     * @param string $content Shortcode content. Default null.
     * @param string $tag     Shortcode tag (name). Default empty.
     * @return string Shortcode output.
     */
    function validate_signature_shortcode( $atts, $content = null ) {
        // shortcode attributes
        extract( shortcode_atts( array(
            'url'    => '',
            'title'  => '',
            'target' => '',
            'class' =>'',
            'text'   => ''
        ), $atts ) );
        $content = $text ? $text : $content;

        $user = wp_get_current_user();
        $signature_status = get_user_meta( $user->ID,'signature_status',true);
        $url = get_user_meta(  $user->ID,'signature_url',true);

        // Returns the button with a link
        if ( $url && $signature_status == Kyc_Status::PENDING) {
            $link_attr = array(
                'href'   => esc_url( $url ),
                'title'  => esc_attr( $title ),
                'target' => ( 'blank' == $target ) ? '_blank' : '',
                'class'  => esc_attr($class)
            );
            $link_attrs_str = "";
            foreach ( $link_attr as $key => $val ) {
                if ( $val ) {
                    $link_attrs_str .= ' ' . $key . '="' . $val . '"';
                }
            }
            return '<a' . $link_attrs_str . '><span>' . do_shortcode( $content ) . '</span></a>';
        }
    }

}
