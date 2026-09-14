<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unomoon_Form_Mail_Service
 */
class Unomoon_Form_Mail_Service {

	/**
	 * @var Unomoon_Form_Mail
	 */
	protected $Mail_raw;

	/**
	 * @var Unomoon_Form_Mail
	 */
	protected $Mail_admin_raw;

	/**
	 * @var Unomoon_Form_Mail
	 */
	protected $Mail_auto_raw;

	/**
	 * @var Unomoon_Form_Data
	 */
	protected $Data;

	/**
	 * @var string
	 */
	protected $form_key;

	/**
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * @var Unomoon_Form_Setting
	 */
	protected $Setting;

	/**
	 * @param Unomoon_Form_Mail    $Mail        Unomoon_Form_Mail object.
	 * @param strign             $form_key    Form key.
	 * @param Unomoon_Form_Setting $Setting     Unomoon_Form_Setting object.
	 * @param array              $attachments Array of attachment.
	 */
	public function __construct( Unomoon_Form_Mail $Mail, $form_key, Unomoon_Form_Setting $Setting, array $attachments = array() ) {
		$this->form_key       = $form_key;
		$this->Data           = Unomoon_Form_Data::connect( $form_key );
		$this->Mail_raw       = $Mail;
		$this->Mail_admin_raw = clone $Mail;
		$this->Mail_auto_raw  = clone $Mail;
		$this->attachments    = $attachments;
		$this->Setting        = $Setting;

		if ( $this->Setting->get( 'post_id' ) ) {
			$this->_set_admin_mail_raw_params();
			// Attach attachment only to e-mail addressed to administrator
			$this->_set_attachments_to( $this->Mail_admin_raw );
			$this->Mail_admin_raw = $this->_apply_filters_unomoonform_admin_mail_raw( $this->Mail_admin_raw );

			$this->_set_reply_mail_raw_params();
			$this->Mail_auto_raw = $this->_apply_filters_unomoonform_auto_mail_raw( $this->Mail_auto_raw );
		} else {
			$Mail = $this->_apply_filters_unomoonform_mail( $Mail );
		}
	}

	/**
	 * Send admin mail and save to database.
	 *
	 * @return boolean
	 */
	public function send_admin_mail() {
		$Mail_admin = $this->_get_parsed_mail_object( $this->Mail_admin_raw );
		$Mail_admin = $this->_apply_filters_unomoonform_mail( $Mail_admin );
		$Mail_admin = $this->_apply_filters_unomoonform_admin_mail( $Mail_admin );

		if ( $this->Setting->get( 'usedb' ) ) {
			$Mail_admin_for_save     = clone $this->Mail_admin_raw;
			$Mail_admin_for_save->to = $Mail_admin->to;
		}

		do_action(
			'unomoonform_before_send_admin_mail_' . $this->form_key,
			clone $Mail_admin,
			clone $this->Data
		);
		$is_admin_mail_sended = $Mail_admin->send();

		// to が false の場合は意図的に送信していない（例えばDB保存だけおこないたい等）ということなので
		// 送信エラー画面が表示されるのはおかしい。そのためここでは true を返す
		if ( ! $Mail_admin->to && $this->Setting->get( 'usedb' ) ) {
			$is_admin_mail_sended = true;
		}

		if ( isset( $Mail_admin_for_save ) && $is_admin_mail_sended ) {
			$this->_save( $Mail_admin_for_save );
		}

		// If not usedb, remove files after sending admin mail
		if ( ! $this->Setting->get( 'usedb' ) ) {
			$this->_delete_files();
		}

		return $is_admin_mail_sended;
	}

	/**
	 * Return parsed Mail object and save to database.
	 *
	 * @param Unomoon_Form_Mail $_Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _get_parsed_mail_object( Unomoon_Form_Mail $_Mail ) {
		$Mail = clone $_Mail;
		$Mail->parse( $this->Setting );
		return $Mail;
	}

	/**
	 * Save to database and return saved mail ID.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return int
	 */
	protected function _save( Unomoon_Form_Mail $Mail ) {
		return $Mail->save( $this->Setting );
	}

	/**
	 * Send reply mail.
	 *
	 * @return boolean
	 */
	public function send_reply_mail() {
		$Mail_auto = $this->_get_parsed_mail_object( $this->Mail_auto_raw );
		$Mail_auto = $this->_apply_filters_unomoonform_auto_mail( $Mail_auto );
		do_action(
			'unomoonform_before_send_reply_mail_' . $this->form_key,
			clone $Mail_auto,
			clone $this->Data
		);
		$is_reply_mail_sended = $Mail_auto->send();
		return $is_reply_mail_sended;
	}

	/**
	 * Set attachment files to Mail object.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 */
	protected function _set_attachments_to( Unomoon_Form_Mail $Mail ) {
		$Mail->attachments = $this->attachments;
	}

	/**
	 * Set admin mail params.
	 */
	protected function _set_admin_mail_raw_params() {
		$this->Mail_admin_raw->set_admin_mail_raw_params( $this->Setting );
	}

	/**
	 * Set reply mail params.
	 */
	private function _set_reply_mail_raw_params() {
		$this->Mail_auto_raw->set_reply_mail_raw_params( $this->Setting );
	}

	/**
	 * Apply unomoonform_admin_mail_raw filter hook.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _apply_filters_unomoonform_admin_mail_raw( Unomoon_Form_Mail $Mail ) {
		return apply_filters(
			'unomoonform_admin_mail_raw_' . $this->form_key,
			$Mail,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Apply unomoonform_mail filter hook.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _apply_filters_unomoonform_mail( Unomoon_Form_Mail $Mail ) {
		return apply_filters(
			'unomoonform_mail_' . $this->form_key,
			$Mail,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Apply unomoonform_admin_mail filter hook.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _apply_filters_unomoonform_admin_mail( Unomoon_Form_Mail $Mail ) {
		return apply_filters(
			'unomoonform_admin_mail_' . $this->form_key,
			$Mail,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Apply unomoonform_auto_mail_raw filter hook.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _apply_filters_unomoonform_auto_mail_raw( Unomoon_Form_Mail $Mail ) {
		return apply_filters(
			'unomoonform_auto_mail_raw_' . $this->form_key,
			$Mail,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Apply unomoonform_auto_mail filter hook.
	 *
	 * @param Unomoon_Form_Mail $Mail Unomoon_Form_Mail object.
	 * @return Unomoon_Form_Mail
	 */
	protected function _apply_filters_unomoonform_auto_mail( Unomoon_Form_Mail $Mail ) {
		return apply_filters(
			'unomoonform_auto_mail_' . $this->form_key,
			$Mail,
			$this->Data->gets(),
			clone $this->Data
		);
	}

	/**
	 * Delete attachment files.
	 */
	protected function _delete_files() {
		foreach ( $this->attachments as $file ) {
			$file = realpath( $file );
			if ( false !== $file && is_file( $file ) && 0 === strpos( $file, Unomoon_Form_Directory::get() ) ) {
				wp_delete_file( $file );
			}
		}
	}

	/**
	 * Update tracking number.
	 */
	public function update_tracking_number() {
		if ( preg_match( '{' . Unomoon_Form_Config::TRACKINGNUMBER . '}', $this->Mail_admin_raw->body ) ) {
			$this->Setting->update_tracking_number();
		}
	}
}
