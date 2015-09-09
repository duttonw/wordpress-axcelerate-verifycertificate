<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Handle frontend forms
 *
 * @class 		pfa_certificate_verifier_Handler
 * @version		2.2.0
 * @package		Pfa_Certificate_Verify/Classes/
 * @category	Class
 * @copyright Copyright (c) 2015, Enhance Industries
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */
class pfa_certificate_verifier_Handler {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'process_verify_certificate' ) );
	}
		
      public static function pfa_form_validate($validation_array) {
        foreach ( $validation_array as $key => $field ) {

			if ( ! isset( $field['type'] ) ) {
				$field['type'] = 'text';
			}

			// Get Value
			switch ( $field['type'] ) {
				case "checkbox" :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
				break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
				break;
			}

			// Validation: Required fields
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( $field['label'] . ' ' . __( 'is a required field.', 'woocommerce' ), 'error' );
			}

			if ( ! empty( $_POST[ $key ] ) ) {

				// Validation rules
				if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
					foreach ( $field['validate'] as $rule ) {
						switch ( $rule ) {
							case 'phone' :
								$_POST[ $key ] = wc_format_phone_number( $_POST[ $key ] );

								if ( ! WC_Validation::is_phone( $_POST[ $key ] ) ) {
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid phone number.', 'woocommerce' ), 'error' );
								}
							break;
							case 'email' :
								$_POST[ $key ] = strtolower( $_POST[ $key ] );

								if ( ! is_email( $_POST[ $key ] ) ) {
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'woocommerce' ), 'error' );
								}
							break;
							case 'statementNumber' :
								if (  count(explode("-", $_POST[ $key ])) != 3 ) {
									wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . 'Is not a valid. e.g 1234567-1234-1234567' , 'error' );
								}
						}
					}
				}
			}
		}
    }
        
	/**
	 * Handle link sending
	 */
	public static function process_verify_certificate() {
       global $Pfa_Certificate_Verify;
	   global $wp;

		if ( 'POST' !== strtoupper( $_SERVER[ 'REQUEST_METHOD' ] ) ) {
			return;
		}

		if ( empty( $_POST[ 'action' ] ) || ( 'ptl_verify_certificate' !== $_POST[ 'action' ] ) || empty( $_POST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'ptl_verify_certificate' ) ) {
			return;
		}

            pfa_certificate_verifier_Handler::pfa_form_validate($Pfa_Certificate_Verify->formFields);
            
            $statementNumber = ! empty( $_POST[ 'statementNumber' ] ) ? wc_clean( $_POST[ 'statementNumber' ] ) : '';
            $firstName = ! empty( $_POST[ 'firstName' ] ) ? wc_clean( $_POST[ 'firstName' ] ) : '';
            $lastName = ! empty( $_POST[ 'lastName' ] ) ? wc_clean( $_POST[ 'lastName' ] ) : '';
            $contactId = ! empty( $_POST[ 'contactId' ] ) ? wc_clean( $_POST[ 'contactId' ] ) : '';

		if ( wc_notice_count( 'error' ) == 0 ) {
			$results =  $Pfa_Certificate_Verify->actions->retrieve_certificate($statementNumber ,$firstName, $lastName, $contactId);

			if ($results == null ){
				wc_add_notice( '<strong>No Certificate Found for data entered</strong> ', 'error' );
				return;
			}
			
			$Pfa_Certificate_Verify->successfulPost = true;
			$Pfa_Certificate_Verify->result = $results;
			return;
		}
	}   
}

pfa_certificate_verifier_Handler::init();
