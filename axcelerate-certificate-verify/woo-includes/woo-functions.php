<?php
/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WC_Points_Dependencies' ) )
	require_once 'class-wc-points-dependencies.php';

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WC_Points_Dependencies::woocommerce_active_check();
	}
}

/**
 * WC Points Detection
 */
if ( ! function_exists( 'is_woocommerce_points_active' ) ) {
	function is_woocommerce_points_active() {
		return WC_Points_Dependencies::woocommerce_wc_points_rewards_active_check();
	}
}

