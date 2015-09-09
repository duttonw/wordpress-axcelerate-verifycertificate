<?php
/**
 * Points To Link Send Form
 *
 * Shows the 'certificateform' or 'certificatesuccess'
 *
 * @author 		William dutton
 * @category 	Shortcodes
 * @package 	Pfa_Certificate_Verify/Shortcodes/Certificate_Verify_Form
 * @version     2.0.0
 */

class Certificate_Verify_Form {

	/**
	 * Get the shortcode content.
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return WC_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	public static function output( $atts ) {
        self::form( $atts );
	}

	/**
	 * My account page
	 *
	 * @param  array $atts
	 */
	private static function form( $atts ) {
    global $wc_points_rewards;
    global $Pfa_Certificate_Verify;
	
	if ($Pfa_Certificate_Verify->successfulPost == true) {
	
			// load the template
		woocommerce_get_template(
			'certificatesuccess.php',
			array(
				'result' => $Pfa_Certificate_Verify->result,
			),
			'',
			$Pfa_Certificate_Verify->get_plugin_path() . '/templates/'
		);
		
	} else {
	
		// load the template
		woocommerce_get_template(
			'certificateform.php',
			array(
				'url' => get_permalink(get_option('woocommerce_certificate_verifier_form_page_id' ))
				
			),
			'',
			$Pfa_Certificate_Verify->get_plugin_path() . '/templates/'
		);
	}
	}

}
