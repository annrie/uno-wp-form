<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<p>
	<span id="formkey_field">[unomoonform_formkey key="<?php echo esc_html( $post_id ); ?>"]</span>
	<span class="unomoonform_note">
		<?php esc_html_e( 'Copy and Paste this shortcode.', 'unomoon-form' ); ?><br />
		<?php esc_html_e( 'The key to use with hook is ', 'unomoon-form' ); ?><?php echo Unomoon_Form_Config::NAME; ?>-<?php echo esc_html( $post_id ); ?>
	</span>
</p>
