<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<p>
	<span id="formkey_field">[unoform_formkey key="<?php echo esc_html( $post_id ); ?>"]</span>
	<span class="uwf_note">
		<?php esc_html_e( 'Copy and Paste this shortcode.', 'uno-wp-form' ); ?><br />
		<?php esc_html_e( 'The key to use with hook is ', 'uno-wp-form' ); ?><?php echo UWF_Config::NAME; ?>-<?php echo esc_html( $post_id ); ?>
	</span>
</p>
