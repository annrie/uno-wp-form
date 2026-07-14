<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

wp_editor(
	$this->_get_option( 'complete_message' ),
	UWF_Config::NAME . '_complete_message',
	array(
		'textarea_name' => UWF_Config::NAME . '[complete_message]',
		'textarea_rows' => 7,
	)
);
?>
<p class="uwf_note">
	<?php esc_html_e( '{name of form tag} is converted to posted data.', 'uno-wp-form' ); ?>
	<?php
	echo sprintf(
		// translators: %s: Tracking Number
		esc_html__( 'It is automatically converted to Tracking number when you input {%s}.', 'uno-wp-form' ),
		UWF_Config::TRACKINGNUMBER
	);
	?>
</p>
