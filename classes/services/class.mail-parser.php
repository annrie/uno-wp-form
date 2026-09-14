<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Unomoon_Form_Mail_Parser
 */
class Unomoon_Form_Mail_Parser {

	/**
	 * @var Unomoon_Form_Mail
	 */
	protected $Mail;

	/**
	 * @var Unomoon_Form_Data
	 */
	protected $Data;

	/**
	 * @var Unomoon_Form_Setting
	 */
	protected $Setting;

	/**
	 * @param Unomoon_Form_Mail    $Mail    Unomoon_Form_Mail object.
	 * @param Unomoon_Form_Setting $Setting Unomoon_Form_Setting object.
	 */
	public function __construct( Unomoon_Form_Mail $Mail, Unomoon_Form_Setting $Setting ) {
		$this->Mail    = $Mail;
		$this->Setting = $Setting;
		$form_id       = $Setting->get( 'post_id' );
		$form_key      = Unomoon_Form_Functions::get_form_key_from_form_id( $form_id );
		$this->Data    = Unomoon_Form_Data::connect( $form_key );
	}

	/**
	 * Return parsed Mail object.
	 *
	 * @return Unomoon_Form_Mail
	 */
	public function get_parsed_mail_object() {
		return $this->_parse_mail_object();
	}

	/**
	 * Return saved mail ID.
	 *
	 * @return int|null
	 */
	public function get_saved_mail_id() {
		return $this->Data->get_saved_mail_id();
	}

	/**
	 * Convert each properties of Mail object.
	 *
	 * @return Unomoon_Form_Mail
	 */
	protected function _parse_mail_object() {
		$parsed_Mail_vars = get_object_vars( $this->Mail );
		foreach ( $parsed_Mail_vars as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			// To, CC, BCC, Return-Path can not use {name}. But they can use {custom_mail_tag}
			if ( 'to' === $key || 'cc' === $key || 'bcc' === $key || 'return_path' === $key ) {
				$Parser           = new Unomoon_Form_Parser( $this->Setting );
				$this->Mail->$key = $Parser->replace_for_mail_destination( $value );
				continue;
			}

			$Parser           = new Unomoon_Form_Parser( $this->Setting );
			$this->Mail->$key = $Parser->replace_for_mail_content( $value );
		}
		return $this->Mail;
	}

	/**
	 * Save Mail content and attachment files.
	 * Set property of saved mail ID.
	 */
	public function save() {
		$form_id       = $this->Setting->get( 'post_id' );
		$Parser        = new Unomoon_Form_Parser( $this->Setting );
		$saved_mail_id = wp_insert_post(
			array(
				'post_title'  => $Parser->replace_for_mail_content( $this->Mail->subject ),
				'post_status' => 'publish',
				'post_type'   => Unomoon_Form_Functions::get_contact_data_post_type_from_form_id( $form_id ),
			)
		);

		if ( ! empty( $saved_mail_id ) ) {
			$this->Data->set_saved_mail_id( $saved_mail_id );

			// 添付ファイルをメディアに保存
			// save_mail_body 内のフックで添付ファイルの情報を使えるように、
			// save_mail_body より前にこのブロックを実行する
			// ここでポストメタとしてURLではなくファイルのIDを保存
			Unomoon_Form_Functions::save_attachments_in_media(
				$saved_mail_id,
				$this->Mail->attachments,
				$form_id
			);
		}

		$parsed_Mail_vars = get_object_vars( $this->Mail );
		foreach ( $parsed_Mail_vars as $key => $value ) {
			if ( is_array( $value ) ) {
				continue;
			}

			if ( 'body' === $key ) {
				$this->_save( $value );
			}
		}
	}

	/**
	 * Search {name} and save value to database.
	 * Save value even if it is null (e.g. posting which checkbox isn't check).
	 *
	 * @param string $value Parsing value.
	 */
	protected function _save( $value ) {
		$Parser  = new Unomoon_Form_Parser( $this->Setting );
		$matches = Unomoon_Form_Parser::search( $value );

		if ( ! isset( $matches[1] ) ) {
			return;
		}

		$form_id  = $this->Setting->get( 'post_id' );
		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $form_id );

		$data = array();

		foreach ( $matches[1] as $name ) {
			$value       = $Parser->parse( $name );
			$ignore_keys = apply_filters( 'unomoonform_no_save_keys_' . $form_key, array() );
			if ( in_array( $name, $ignore_keys, true ) ) {
				continue;
			}

			// ファイルは Unomoon_Form_Functions::save_attachments_in_media() で ID が保存されるため
			// ここで送信された値（URL）は保存しない
			if ( array_key_exists( $name, $this->Mail->attachments ) ) {
				continue;
			}

			$data[ $name ] = ( is_null( $value ) ) ? '' : $value;
		}

		$data = array_merge(
			array(
				'admin_mail_to' => $this->Mail->to, // admin_mail_to = The property of Unomoon_Form_Contact_Data_Setting
			),
			$data
		);

		$Contact_Data_Setting = new Unomoon_Form_Contact_Data_Setting( $this->Data->get_saved_mail_id() );
		$Contact_Data_Setting->sets( $data );
		$Contact_Data_Setting->save();
	}
}
