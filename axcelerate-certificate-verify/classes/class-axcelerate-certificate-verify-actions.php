<?php
/**
 * Axcelerate Certificate Validation
 * 
 * Admin actions and certificate verification function
 *
 * @class 		axcelerate_certificate_verifier_Actions
 * @package     Axcelerate_Cert_Validation/Classes
 * @author      William Dutton
 * @category	Class
 * @copyright Copyright (c) 2015, Enhance Industries
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * # Actions Class
 *
 * @since 1.0
 */
class axcelerate_certificate_verifier_Actions {
	/**
	 * Initialize the Certificate Verifier integration class
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// add the WooCommerce core action settings
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		      // Add the action setting
            add_filter( 'woocommerce_general_settings',  array( $this,  'certificate_verifier_action_settings' ), 10 );
		}
	}

	/**
	 * Adds the WooCommerce core actions integration settings
	 *
	 * @since 1.0
	 * @param array $settings the settings array
	 * @return array the settings array
	 */
    public function certificate_verifier_action_settings( $settings ) {
      
      	$settings = array_merge(
			$settings,
			array(
				array( 
                'title' => __( 'Axcelerate Certificate Verifier Settings', 'Axcelerate_Cert_Validation' ), 
                'type' => 'title', 
                 'desc' => '', 
                 'id' => 'certificate_verifier_settings' ),
				 
				 
				array(
            		'title'    => __( 'Axcelerate Url', 'Axcelerate_Cert_Validation' ),
            		'desc_tip' => __( 'Enter the Axcelerate Url.', 'Axcelerate_Cert_Validation' ),
            		'id'       => 'axcelerate_url',
					'default' => 'https://admin.axcelerate.com.au',
					'type'     => 'text',
    	       ),
               array(
            		'title'    => __( 'wstoken', 'Axcelerate_Cert_Validation' ),
            		'desc_tip' => __( 'ws token', 'Axcelerate_Cert_Validation' ),
            		'id'       => 'axcelerate_wstoken',
                    'type'     => 'text',
                    'css'      => 'min-width: 400px;',
                    //'default'  =>  __( '', 'Axcelerate_Cert_Validation' ),
    	       ),
               array(
            		'title'    => __( 'apitoken', 'Axcelerate_Cert_Validation' ),
            		'desc_tip' => __( 'api token', 'Axcelerate_Cert_Validation' ),
            		'id'       => 'axcelerate_apitoken',
                    'type'     => 'text',
                    'css'      => 'min-width: 400px;',
                    //'default'  =>  __( '', 'Axcelerate_Cert_Validation' ),
    	       ),
               
			   array( 'type' => 'sectionend', 'id' => 'certificate_verifier_options_start' ),
			   
            array( 
                'title' => __( 'Certificate Verifier Page', 'Axcelerate_Cert_Validation' ), 
                'type' => 'title', 
                'desc' => __( 'This pages need to be set so that WooCommerce knows where to send users to verify the Certificates.'  . ' [certificate_verifier_form]', 'Axcelerate_Cert_Validation' ), 
                 'id' => 'certificate_verifier_options' ),

			array(
				'title'    => __( 'Certificate Verifier Page', 'Axcelerate_Cert_Validation' ),
				'desc'     => __( 'Page contents:', 'Axcelerate_Cert_Validation' ) . ' [certificate_verifier_form]',
				'id'       => 'woocommerce_certificate_verifier_form_page_id',
				'type'     => 'single_select_page',
				'default'  => '',
				'class'    => 'chosen_select_nostd',
				'css'      => 'min-width:300px;',
				'desc_tip' => true,
			),
			
			array( 'type' => 'sectionend', 'id' => 'certificate_verifier_settings_end' ),
			)
		);
      
    	return $settings;
    }
	
	public function  retrieve_certificate($statementNumber, $firstName, $lastName, $contactId) {
		$wstoken = get_option('axcelerate_wstoken' );
		$apitoken = get_option('axcelerate_apitoken' );
		$url = get_option('axcelerate_url' );
		$statementNumberFields = explode("-", $statementNumber);
		
		$headerdata = array ("wstoken" => $wstoken,
						  "apitoken" => $apitoken,
		);
		
		$template = \Httpful\Request::init()
			->method(\Httpful\Http::GET)
			->expectsJSON()
			->addHeaders($headerdata);
			
		// Set it as a template
		\Httpful\Request::ini($template);

		//Use the verify endpoint with the enrolID to get the ContactID.
		$uri = $url . "/api/contact/enrolment/verifyCertificate/" . $statementNumberFields[2];
		$verifyCertificate = \Httpful\Request::get($uri)->send();

		//die early if no document
		if (! property_exists ($verifyCertificate->body, "DOCUMENT" )){
			return null;
		}
		
		$contactId_result = $verifyCertificate->body->DOCUMENT->DETAIL->CONTACTID;
		$enrolid = $verifyCertificate->body->DOCUMENT->DETAIL->ENROLID;
		$surname_result = $verifyCertificate->body->DOCUMENT->DETAIL->SURNAME;
		$givenname_result = $verifyCertificate->body->DOCUMENT->DETAIL->GIVENNAME;
		
		//return early if data provided does not match certificate
		if (strtolower($firstName) != strtolower($givenname_result) || strtolower($lastName) != strtolower($surname_result) || $contactId != $contactId_result) {
			return null;
		}
		
		//Retrieve enrolments from Contact/enrolments/:contactID for type 'P' and look for a matching enrolID
		$uri = $url . "/api/contact/enrolments/" . $contactId_result;
		$response_enrolments = \Httpful\Request::get($uri)->send();
		
		$enrolementsOnCertificate = array();
		foreach ($response_enrolments->body as $enrolments) {
			if ($enrolid == $enrolments->ENROLID && "p" == $enrolments->TYPE){
				array_push($enrolementsOnCertificate, $enrolments);
			}
		}
		
		//With the returned instance ID search the Course/enrolments/ with the parameters contactID and instanceID
		$courses = array();
		foreach($enrolementsOnCertificate as $course) {
			$instanceId = $course->INSTANCEID;
			$uri = $url . "/api/course/enrolments/?contactID=" . $contactId_result . "&instanceID=" . $instanceId;
			
			$response_data = \Httpful\Request::get($uri)->send();
			
			foreach($response_data->body as $data ) {
				foreach($data->ACTIVITIES as $activities) {
					array_push($courses, $activities );
				}
			}
		}
		
		$input_data = array();
		array_push($input_data, $statementNumber, $firstName, $lastName, $contactId);
		
		//This should return subjects, outcomes and any other information you might need regarding the enrolment.
		return array("certificate" => $verifyCertificate->body->DOCUMENT,
				"enrolmentsOnCertificate" =>  $enrolementsOnCertificate,
				"courses" => $courses, "input_data" => $input_data );
	}
}
?>