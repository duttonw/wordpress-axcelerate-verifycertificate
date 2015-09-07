<?php
/**
 * The Template for displaying points to link
 *
 * Override this template by copying it to yourtheme/woocommerce/certificateform.php
 *
 * @author 		William Dutton
 * @package 	Pfa_Certificate_Verify/Templates
 * @version     1.0.0
 * @copyright Copyright (c) 2015, Enhance Industries
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
 global $Pfa_Certificate_Verify;
 ?>

<?php wc_print_notices(); ?>
    
	<form method="post" action="<?php echo $url ?>" id="pfa_form">
    <div id="pfa_formFields" >
    	<?php foreach ( $Pfa_Certificate_Verify->formFields as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field, ! empty( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : $field['value'] ); ?>

		<?php endforeach; ?>
    </div>
		<p >
			<input type="submit" class="button" name="send_link" value="<?php _e( 'Check Certificate', 'Pfa_Certificate_Verify' ); ?>" />
			<?php wp_nonce_field( 'ptl_verify_certificate' ); ?>
			<input type="hidden" name="action" value="ptl_verify_certificate" />
		</p>

	</form>

