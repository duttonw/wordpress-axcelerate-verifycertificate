<?php
/**
 * WooCommerce Points and Rewards
 *
 * @author 		William Dutton
 * @package 	Pfa_Certificate_Verify/Templates
 * @version     1.0.0
 * @copyright Copyright (c) 2015, Enhance Industries
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * certificate success
 *
 * @version     1.0.0
 */
?>
<?php wc_print_notices(); ?>

	<span class="certificate-verified">Verified Certificate Found</span><br/><br/>
	
	<!--Valid Yes/No <br/>-->
	<div class="certificate-units">
		<span class="certificate-units-title">Units of Competencies achieved:</span>
		<ul class="certificate-units-values">
		<?php $courses = $result['courses'];
		
		foreach ( $courses as $course ) {
			echo "<li class='certificate-unit-value'><span class='course-code'>" . $course->CODE . "</span> <span class='course-name'>" . $course->NAME . '</span></li>';
		} ?>
		</ul>
	</div>
	<?php //print_r($result); ?>

	<form method="post">
		<input type="submit" class="button" name="send_link" value="<?php _e( 'Check another Certificate', 'Pfa_Certificate_Verify' ); ?>" />
	</form>
