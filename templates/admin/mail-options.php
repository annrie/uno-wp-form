<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<p>
	<?php esc_html_e( '{name of form tag} is converted to posted data.', 'uno-wp-form' ); ?>
	<?php
	echo sprintf(
		// translators: %s: Tracing Number
		esc_html__( 'It is automatically converted to Tracking number when you input {%s}.', 'uno-wp-form' ),
		UWF_Config::TRACKINGNUMBER
	);
	?>
</p>
<p>
	<b><?php esc_html_e( 'Subject', 'uno-wp-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[mail_subject]" value="<?php echo esc_attr( $mail_subject ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Sender', 'uno-wp-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[mail_sender]" value="<?php echo esc_attr( $mail_sender ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Reply-to ( E-mail address )', 'uno-wp-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[mail_reply_to]" value="<?php echo esc_attr( $mail_reply_to ); ?>" />
</p>
<p>
	<b><?php esc_html_e( 'Content', 'uno-wp-form' ); ?></b><br />
	<textarea class="widefat" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[mail_content]" cols="30" rows="10"><?php echo esc_attr( $mail_content ); ?></textarea>
</p>
<p>
	<b><?php esc_html_e( 'Automatic reply email', 'uno-wp-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[automatic_reply_email]" value="<?php echo esc_attr( $automatic_reply_email ); ?>" /><br />
	<span class="uwf_note"><?php esc_html_e( 'Input the key to use as transmission to automatic reply email. {} is unnecessary.', 'uno-wp-form' ); ?></span>
</p>
<p>
	<b><?php esc_html_e( 'From ( E-mail address )', 'uno-wp-form' ); ?></b><br />
	<input class="widefat" type="text" name="<?php echo esc_attr( UWF_Config::NAME ); ?>[mail_from]" value="<?php echo esc_attr( $mail_from ); ?>" />
	<span class="uwf_note"><?php esc_html_e( 'Optional. You should specify an email address in the same domain as your server.', 'uno-wp-form' ); ?></span>
</p>
