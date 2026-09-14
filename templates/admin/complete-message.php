<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

wp_editor(
	$this->_get_option( 'complete_message' ),
	Unomoon_Form_Config::NAME . '_complete_message',
	array(
		'textarea_name' => Unomoon_Form_Config::NAME . '[complete_message]',
		'textarea_rows' => 7,
	)
);
?>
<p class="unomoonform_note">
	<?php esc_html_e( '{name of form tag} is converted to posted data.', 'unomoon-form' ); ?>
	<?php
	echo sprintf(
		// translators: %s: Tracking Number
		esc_html__( 'It is automatically converted to Tracking number when you input {%s}.', 'unomoon-form' ),
		Unomoon_Form_Config::TRACKINGNUMBER
	);
	?>
</p>
