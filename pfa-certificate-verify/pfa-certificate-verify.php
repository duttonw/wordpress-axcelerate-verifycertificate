<?php
/**
 * Plugin Name: PFA certificate verify
 * Plugin URI: http://www.williamjdutton.com/pfa-certificate-verify
 * Description: Allows clients to verify if their certificate is still valid
 * Author: William Dutton
 * Author URI: http://www.williamjdutton.com
 * Version: 1.0.0
 * Text Domain: Pfa_Certificate_Verify
 * Domain Path: /languages/
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-Certificate-Verify
 * @author    William Dutton
 * @category  Marketing
 * @copyright Copyright (c) 2015, Enhance Industries
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );



if ( ! defined('NONCE_SECRET')){
    define('NONCE_SECRET', 'jvTGophIQ108Pqw9Hej2');
}   

// Check if WooCommerce is active
if ( ! is_woocommerce_active() )
	return;

/**
 * The Pfa_Certificate_Verify global object
 * @name $Pfa_Certificate_Verify
 * @global Pfa_Certificate_Verify $GLOBALS['Pfa_Certificate_Verify']
 */
$GLOBALS['Pfa_Certificate_Verify'] = new Pfa_Certificate_Verify();

class Pfa_Certificate_Verify {

	/** plugin version number */
	const VERSION = '1.0.0';

	/** @var string the plugin path */
	private $plugin_path;

	/** @var string the plugin url */
	private $plugin_url;

	/** @var \WC_Logger instance */
	private $logger;
	
	/** @var \Pfa_Certificate_Verify_Admin admin class */
	private $admin;

	/** @var \Pfa_Certificate_Verify_Admin product admin class */
	private $product_admin;

	/** @var WP_Admin_Message_Handler admin message handler class */
	public $admin_message_handler;
    
    public $formFields;

	/** @var Pfa_Certificate_Verify_Actions the core actions integration */
	public $actions;
	
	public $successfulPost;
	
	public $result;
    
    /**
	 * Initializes the plugin
	 *
	 * @since 1.0
	 */
	public function __construct() {

		global $wpdb;

		// include required files
		$this->includes();

		// called just before the woocommerce template functions are included
		add_action( 'init', array( $this, 'load_translation' ) );
		add_action( 'init', array( $this, 'include_template_functions' ), 25 );

        add_action( 'init', array( 'pfa_certificate_verifier_Shortcodes', 'init' ) );

		// admin
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			// run every time
			$this->install();
		}
	}
    
    /**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}
    
    /**
	 * Handle localization, WPML compatible
	 *
	 * @since 1.0
	 */
	public function load_translation() {
		// localization in the init action for WPML support
		load_plugin_textdomain( 'Pfa_Certificate_Verify', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
    
    /**
	 * Function used to init Points to Link Sned template functions,
	 * making them pluggable by plugins and themes.
	 *
	 * @since 1.0
	 */
	public function include_template_functions() {
	}
    
    /**
	 * Include required files
	 *
	 * @since 1.0
	 */
	private function includes() {

        $this->formFields = array (
                'firstName' => array(
    				'title'    => __( 'First Name:', 'Pfa_Certificate_Verify' ),
    				'label'             => 'First Name:',
    				'description'       => '',
    				'id'       => 'firstName',
    				'default'  => '',
    				'type'     => 'text',
                    'value' => '',
                    'validate'		=> array( 'text' ),
                    'required' => true,
                ),
                'lastName' => array(
    				'title'    => __( 'Last Name:', 'Pfa_Certificate_Verify' ),
    				'label'             => 'Last Name:',
    				'description'       => '',
    				'id'       => 'lastName',
    				'default'  => '',
    				'type'     => 'text',
                    'value' => '',
                    'validate'		=> array( 'text' ),
                    'required' => true,
                ),
                'contactId' => array(
    				'title'    => __( 'Contact ID / Student Number:', 'Pfa_Certificate_Verify' ),
    				'label'             => 'Contact ID / Student Number:',
    				'description'       => '',
    				'id'       => 'contactId',
    				'default'  => '',
    				'type'     => 'text',
                    'value' => '',
                    'validate'		=> array( 'text' ),
                    'required' => true,
                ),
                'statementNumber' => array(
    				'title'    => __( 'Statement of Attainment Number:', 'Pfa_Certificate_Verify' ),
    				'label'             => 'Statement of Attainment Number:',
    				'description'       => '',
    				'id'       => 'statementNumber',
    				'default'  => '',
    				'type'     => 'text',
                    'value' => '',
                    'validate'		=> array( 'statementNumber' ),
                    'required' => true,
                ),
                
			);

        include('lib/httpful.phar');
        
        // actions class
        require( 'classes/class-pfa-certificate-verify-actions.php');
        $this->actions = new pfa_certificate_verifier_Actions();
        
        if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			// Classes
			include_once( 'classes/class-pfa-certificate-verify-form-handler.php' );                    //Form Handlers
			include_once( 'shortcodes/class-pfa-certificate-verify-form.php' );                       // A Shortcode class
		}
			include_once( 'classes/class-pfa-certificate-verify-shortcodes.php' );                     // Shortcodes class
		if ( is_admin() )
			$this->admin_includes();
	}
    
	/**
	 * Include required admin files
	 *
	 * @since 1.0
	 */
	private function admin_includes() {

	}

	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory
	 *
	 * @since 1.0
	 * @return string plugin path
	 */
	public function get_plugin_path() {

		if ( $this->plugin_path )
			return $this->plugin_path;

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Gets the plugin url without a trailing slash
	 *
	 * @since 1.0
	 * @return string the plugin url
	 */
	public function get_plugin_url() {

		if ( $this->plugin_url )
			return $this->plugin_url;

		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/** Lifecycle methods ******************************************************/

	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 1.0
	 */
	private function install() {

		// get current version to check for upgrade
		$installed_version = get_option( 'Pfa_Certificate_Verify_version' );

		// install
		if ( ! $installed_version ) {

			// initial install work if required
		}

		// upgrade if installed version lower than plugin version
		if ( -1 === version_compare( $installed_version, self::VERSION ) )
			$this->upgrade( $installed_version );
	}

	/**
	 * Perform any version-related changes. Changes to custom db tables are handled by the migrate() method
	 *
	 * @since 1.0
	 * @param int $installed_version the currently installed version of the plugin
	 */
	private function upgrade( $installed_version ) {

		// update the installed version option
		update_option( 'Pfa_Certificate_Verify_version', self::VERSION );
	}

} // end \Pfa_Certificate_Verify class (note the newline after this line)
