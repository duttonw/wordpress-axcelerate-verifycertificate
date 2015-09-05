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

	Verified Certificate Found<br/><br/>
	
	<!--Valid Yes/No <br/>-->
	<div class="certificate-details">
		<span class="certificate-title">Cerfiticate Name:</span>
		<span class="certificate-value"><?php echo $result['enrolmentsOnCertificate'][0]->NAME; ?></span><br/>
		<span class="certificate-completiondate-title">Completed On:</span>
		<span class="certificate-completiondate"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $result['enrolmentsOnCertificate'][0]->COMPLETIONDATE ) ); ?></span><br/>
		<span class="certificate-status-title">Status:</span>
		<span class="certificate-status"><?php echo $result['enrolmentsOnCertificate'][0]->STATUS ?></span>
	</div>
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
