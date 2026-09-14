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
	<?php esc_html_e( '{name of form tag} is converted to posted data.', 'unomoon-form' ); ?>
	<?php
	echo sprintf(
		// translators: %s: Tracing Number
		esc_html__( 'It is automatically converted to Tracking number when you input {%s}.', 'unomoon-form' ),
		Unomoon_Form_Config::TRACKINGNUMBER
	);
	?>
</p>
<p>
	<b><?php esc_html_e( 'Subject', 'unomoon-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[mail_subject]" value="<?php echo esc_attr( $mail_subject ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Sender', 'unomoon-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[mail_sender]" value="<?php echo esc_attr( $mail_sender ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Reply-to ( E-mail address )', 'unomoon-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[mail_reply_to]" value="<?php echo esc_attr( $mail_reply_to ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Content', 'unomoon-form' ); ?></b><br />
	<textarea class="widefat" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[mail_content]" cols="30" rows="10"><?php echo esc_attr( $mail_content ); ?></textarea>
</p>
<p>
	<b><?php esc_html_e( 'Automatic reply email', 'unomoon-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[automatic_reply_email]" value="<?php echo esc_attr( $automatic_reply_email ); ?>" /><br />
	<span class="unomoonform_note"><?php esc_html_e( 'Input the key to use as transmission to automatic reply email. {} is unnecessary.', 'unomoon-form' ); ?></span>
</p>
<p>
	<b><?php esc_html_e( 'From ( E-mail address )', 'unomoon-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[mail_from]" value="<?php echo esc_attr( $mail_from ); ?>" />
	<span class="unomoonform_note"><?php esc_html_e( 'Optional. You should specify an email address in the same domain as your server.', 'unomoon-form' ); ?></span>
</p>
