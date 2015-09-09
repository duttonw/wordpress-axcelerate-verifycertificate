<?php
/**
 * PFA certificate verify
 *
 * @class 		pfa_certificate_verifier_Shortcodes
 * @package     Pfa_Certificate_Verify/Classes
 * @author      William Dutton
 * @copyright   Copyright (c) 2015, Enhance Industries
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
class pfa_certificate_verifier_Shortcodes {

	/**
	 * Init shortcodes
	 */
	 public static function init() {
		// Define shortcodes
		$shortcodes = array(
			'certificate_verifier_form'     => __CLASS__ . '::verifier_form',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}
	
    /**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'woocommerce',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}
    
	public static function verifier_form( $atts ) {
		return self::shortcode_wrapper( array( 'Certificate_Verify_Form', 'output' ), $atts );
	}
}
