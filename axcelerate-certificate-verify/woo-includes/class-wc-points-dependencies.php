<?php
/**
 * Axcelerate Certificate Validation Dependency Checker
 *
 * Checks if WooCommerce points is enabled
 */
class WC_Points_Dependencies {

	private static $active_plugins;

	public static function init() {

		self::$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() )
			self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
	}

	public static function woocommerce_wc_points_rewards_active_check() {

		if ( ! self::$active_plugins ) self::init();

		return in_array( 'woocommerce-points-and-rewards/wc_points_rewards.php', self::$active_plugins ) || array_key_exists( 'woocommerce-points-and-rewards/wc_points_rewards.php', self::$active_plugins );
	}
    
    	public static function woocommerce_active_check() {

		if ( ! self::$active_plugins ) self::init();

		return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );
	}

}