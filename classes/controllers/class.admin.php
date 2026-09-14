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
 * Unomoon_Form_Admin_Controller
 */
class Unomoon_Form_Admin_Controller extends Unomoon_Form_Controller {

	/**
	 * @var array
	 */
	protected $styles = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, '_add_meta_boxes' ) );
		add_filter( 'default_content', array( $this, '_default_content' ) );
		add_action( 'media_buttons', array( $this, '_tag_generator' ) );
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );
		add_action( 'save_post', array( $this, '_save_post' ) );
	}

	/**
	 * Add meta boxes.
	 */
	public function _add_meta_boxes() {
		global $post;

		$this->styles = apply_filters( 'unomoonform_styles', $this->styles );
		$form_key     = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );
		$Form_Fields  = Unomoon_Form_Form_Fields::instantiation( $form_key );
		$form_fields  = $Form_Fields->get_form_fields();
		foreach ( $form_fields as $form_field ) {
			$form_field->add_tag_generator();
		}

		add_meta_box(
			Unomoon_Form_Config::NAME . '_complete_message_metabox',
			__( 'Complete Message', 'unomoon-form' ),
			array( $this, '_complete_message' ),
			Unomoon_Form_Config::NAME,
			'normal'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_url',
			__( 'URL Options', 'unomoon-form' ),
			array( $this, '_url' ),
			Unomoon_Form_Config::NAME,
			'normal'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_validation',
			__( 'Validation Rule', 'unomoon-form' ),
			array( $this, '_validation_rule' ),
			Unomoon_Form_Config::NAME,
			'normal'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_formkey',
			__( 'Form Key', 'unomoon-form' ),
			array( $this, '_form_key' ),
			Unomoon_Form_Config::NAME,
			'side'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_mail',
			__( 'Automatic Reply Email Options', 'unomoon-form' ),
			array( $this, '_mail_options' ),
			Unomoon_Form_Config::NAME,
			'side'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_admin_mail',
			__( 'Admin Email Options', 'unomoon-form' ),
			array( $this, '_admin_mail_options' ),
			Unomoon_Form_Config::NAME,
			'side'
		);

		add_meta_box(
			Unomoon_Form_Config::NAME . '_settings',
			__( 'settings', 'unomoon-form' ),
			array( $this, '_settings' ),
			Unomoon_Form_Config::NAME,
			'side'
		);

		if ( $this->styles ) {
			add_meta_box(
				Unomoon_Form_Config::NAME . '_styles',
				__( 'Style setting', 'unomoon-form' ),
				array( $this, '_style' ),
				Unomoon_Form_Config::NAME,
				'side'
			);
		}
	}

	/**
	 * Set default form html.
	 *
	 * @return string
	 */
	public function _default_content() {
		return apply_filters( 'unomoonform_default_content', '' );
	}

	/**
	 * Render tag generator.
	 *
	 * @param string $editor_id Editor ID.
	 */
	public function _tag_generator( $editor_id ) {
		$post_type = get_post_type();
		if ( Unomoon_Form_Config::NAME !== $post_type ) {
			return;
		}

		if ( 'content' !== $editor_id ) {
			return;
		}

		$this->_render( 'admin/tag-generator' );
	}

	/**
	 * Enqueue assets.
	 */
	public function _admin_enqueue_scripts() {
		$url = UNOMOON_FORM_PLUGIN_URL;

		wp_enqueue_style(
			Unomoon_Form_Config::NAME . '-admin',
			$url . '/css/admin.css',
			array(),
			UNOMOON_FORM_VERSION
		);

		wp_enqueue_style(
			Unomoon_Form_Config::NAME . '-admin-repeatable',
			$url . '/css/admin-repeatable.css',
			array(),
			UNOMOON_FORM_VERSION
		);

		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-repeatable',
			$url . '/js/unomoon-form-repeatable.js',
			array( 'jquery' ),
			UNOMOON_FORM_VERSION,
			true
		);

		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-admin',
			$url . '/js/admin.js',
			array( 'jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', Unomoon_Form_Config::NAME . '-repeatable' ),
			UNOMOON_FORM_VERSION,
			true
		);

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		Unomoon_Form_Functions::enqueue_jquery_ui_style();
	}

	/**
	 * Save.
	 *
	 * @param int $post_id Post ID.
	 */
	public function _save_post( $post_id ) {
		if ( ! isset( $_POST['post_type'] ) || Unomoon_Form_Config::NAME !== $_POST['post_type'] ) {
			return;
		}

		if ( ! isset( $_POST[ Unomoon_Form_Config::NAME . '_nonce' ] ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST[ Unomoon_Form_Config::NAME . '_nonce' ] ) );
		if ( ! wp_verify_nonce( $nonce, Unomoon_Form_Config::NAME ) ) {
			return;
		}

		if ( ! current_user_can( Unomoon_Form_Config::CAPABILITY ) ) {
			return;
		}

		if ( ! isset( $_POST[ Unomoon_Form_Config::NAME ] ) || ! is_array( $_POST[ Unomoon_Form_Config::NAME ] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per key in _sanitize_settings().
		$data = $this->_sanitize_settings( wp_unslash( $_POST[ Unomoon_Form_Config::NAME ] ) );

		$triminglists = array(
			'mail_from',
			'mail_return_path',
			'mail_to',
			'mail_cc',
			'mail_bcc',
			'admin_mail_from',
			'mail_reply_to',
			'admin_mail_reply_to',
		);
		foreach ( $triminglists as $name ) {
			if ( ! isset( $data[ $name ] ) ) {
				continue;
			}
			if ( function_exists( 'mb_convert_kana' ) ) {
				$data[ $name ] = trim( mb_convert_kana( $data[ $name ], 's', get_option( 'blog_charset' ) ) );
			} else {
				$data[ $name ] = trim( $data[ $name ] );
			}
		}

		if ( ! empty( $data['validation'] ) && is_array( $data['validation'] ) ) {
			$validation = array();
			foreach ( $data['validation'] as $_validation ) {
				if ( empty( $_validation['target'] ) ) {
					continue;
				}

				foreach ( $_validation as $key => $value ) {
					if ( 'between' === $key ) {
						if ( ! Unomoon_Form_Functions::is_numeric( $value['min'] ) ) {
							unset( $_validation[ $key ]['min'] );
						}
						if ( ! Unomoon_Form_Functions::is_numeric( $value['max'] ) ) {
							unset( $_validation[ $key ]['max'] );
						}
					}

					if ( 'minlength' === $key && ! Unomoon_Form_Functions::is_numeric( $value['min'] ) ) {
						unset( $_validation[ $key ] );
					}

					if ( 'fileType' === $key && isset( $value['types'] ) && ! preg_match( '/^[0-9A-Za-z,]+$/', $value['types'] ) ) {
						unset( $_validation[ $key ] );
					}

					if ( 'fileSize' === $key && ! Unomoon_Form_Functions::is_numeric( $value['bytes'] ) ) {
						unset( $_validation[ $key ] );
					}

					if ( empty( $value ) ) {
						unset( $_validation[ $key ] );
					}

					if ( is_array( $value ) && ! array_diff( $value, array( '' ) ) ) {
						unset( $_validation[ $key ] );
					}
				}

				$validation[] = $_validation;
			}

			$data['validation'] = $validation;
		}

		if ( empty( $data['querystring'] ) ) {
			$data['querystring'] = false;
		}

		if ( empty( $data['usedb'] ) ) {
			$data['usedb'] = false;
		}

		if ( empty( $data['scroll'] ) ) {
			$data['scroll'] = false;
		}

		$Setting = new Unomoon_Form_Setting( $post_id );
		$Setting->sets( $data );

		if ( isset( $_POST[ Unomoon_Form_Config::TRACKINGNUMBER ] ) ) {
			$tracking_number = absint( wp_unslash( $_POST[ Unomoon_Form_Config::TRACKINGNUMBER ] ) );
			$Setting->update_tracking_number( $tracking_number );
		}

		$Setting->save();
	}

	/**
	 * Sanitize the posted form settings.
	 *
	 * Every value is a plain string except the HTML complete message, the two
	 * multi-line mail bodies and the four redirect URLs. Nested arrays (validation
	 * rules, add-on fields) are sanitized recursively as plain text.
	 *
	 * @param array $data Unslashed settings posted from the edit screen.
	 * @return array
	 */
	protected function _sanitize_settings( array $data ) {
		$sanitized = array();

		foreach ( $data as $key => $value ) {
			$key = sanitize_key( $key );
			if ( '' === $key ) {
				continue;
			}

			if ( 'complete_message' === $key ) {
				$sanitized[ $key ] = wp_kses_post( (string) $value );
			} elseif ( in_array( $key, array( 'mail_content', 'admin_mail_content' ), true ) ) {
				$sanitized[ $key ] = sanitize_textarea_field( (string) $value );
			} elseif ( in_array( $key, array( 'input_url', 'confirmation_url', 'complete_url', 'validation_error_url' ), true ) ) {
				// URL-aware: keeps percent-encoded octets and query strings that sanitize_text_field() would mangle.
				$sanitized[ $key ] = esc_url_raw( trim( (string) $value ) );
			} elseif ( is_array( $value ) ) {
				$sanitized[ $key ] = map_deep( $value, 'sanitize_text_field' );
			} else {
				$sanitized[ $key ] = sanitize_text_field( (string) $value );
			}
		}

		return $sanitized;
	}

	/**
	 * Render complete message meta box.
	 */
	public function _complete_message() {
		global $post;

		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );

		$this->_render(
			'admin/complete-message',
			array(
				'content' => $this->_get_option( 'complete_message' ),
			),
			$form_key
		);
	}

	/**
	 * Render URL setting meta box.
	 */
	public function _url() {
		global $post;

		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );

		$this->_render(
			'admin/url',
			array(
				'input_url'            => $this->_get_option( 'input_url' ),
				'confirmation_url'     => $this->_get_option( 'confirmation_url' ),
				'complete_url'         => $this->_get_option( 'complete_url' ),
				'validation_error_url' => $this->_get_option( 'validation_error_url' ),
			),
			$form_key
		);
	}

	/**
	 * Render validation meta box.
	 */
	public function _validation_rule() {
		global $post;

		$validation = $this->_get_option( 'validation' );
		if ( ! $validation ) {
			$validation = array();
		}

		$validation_keys = array(
			'target' => '',
		);

		$form_key         = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );
		$Validation_Rules = Unomoon_Form_Validation_Rules::instantiation( $form_key );

		foreach ( $Validation_Rules->get_validation_rules() as $instance ) {
			$validation_keys[ $instance->getName() ] = '';
		}

		// 空の隠れバリデーションフィールド（コピー元）を挿入
		array_unshift( $validation, $validation_keys );
		$this->_render(
			'admin/validation-rule',
			array(
				'validation'       => $validation,
				'validation_rules' => $Validation_Rules->get_validation_rules(),
				'validation_keys'  => $validation_keys,
			),
			$form_key
		);
	}

	/**
	 * Render form key meta box.
	 */
	public function _form_key() {
		$this->_render(
			'admin/form-key',
			array(
				'post_id' => get_the_ID(),
			)
		);
	}

	/**
	 * Render reply mail meta box.
	 */
	public function _mail_options() {
		global $post;

		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );

		$mail_sender = $this->_get_option( 'mail_sender' );
		if ( is_null( $mail_sender ) ) {
			$mail_sender = get_bloginfo( 'name' );
		}

		$mail_reply_to = $this->_get_option( 'mail_reply_to' );
		if ( is_null( $mail_reply_to ) ) {
			$mail_reply_to = get_bloginfo( 'admin_email' );
		}

		$this->_render(
			'admin/mail-options',
			array(
				'mail_subject'          => $this->_get_option( 'mail_subject' ),
				'mail_sender'           => $mail_sender,
				'mail_reply_to'         => $mail_reply_to,
				'mail_from'             => $this->_get_option( 'mail_from' ),
				'mail_content'          => $this->_get_option( 'mail_content' ),
				'automatic_reply_email' => $this->_get_option( 'automatic_reply_email' ),
			),
			$form_key
		);
	}

	/**
	 * Render admin mail meta box.
	 */
	public function _admin_mail_options() {
		global $post;

		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );

		$mail_to = $this->_get_option( 'mail_to' );
		if ( is_null( $mail_to ) ) {
			$mail_to = get_bloginfo( 'admin_email' );
		}

		$admin_mail_sender = $this->_get_option( 'admin_mail_sender' );
		if ( is_null( $admin_mail_sender ) ) {
			$admin_mail_sender = get_bloginfo( 'name' );
		}

		$admin_mail_reply_to = $this->_get_option( 'admin_mail_reply_to' );
		if ( is_null( $admin_mail_reply_to ) ) {
			$admin_mail_reply_to = get_bloginfo( 'admin_email' );
		}

		$this->_render(
			'admin/admin-mail-options',
			array(
				'mail_to'             => $mail_to,
				'mail_cc'             => $this->_get_option( 'mail_cc' ),
				'mail_bcc'            => $this->_get_option( 'mail_bcc' ),
				'admin_mail_subject'  => $this->_get_option( 'admin_mail_subject' ),
				'admin_mail_sender'   => $admin_mail_sender,
				'admin_mail_reply_to' => $admin_mail_reply_to,
				'mail_return_path'    => $this->_get_option( 'mail_return_path' ),
				'admin_mail_from'     => $this->_get_option( 'admin_mail_from' ),
				'admin_mail_content'  => $this->_get_option( 'admin_mail_content' ),
			),
			$form_key
		);
	}

	/**
	 * Render settings meta box.
	 */
	public function _settings() {
		global $post;

		$form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $post->ID );

		$this->_render(
			'admin/settings',
			array(
				'querystring'          => $this->_get_option( 'querystring' ),
				'usedb'                => $this->_get_option( 'usedb' ),
				'scroll'               => $this->_get_option( 'scroll' ),
				'akismet_author'       => $this->_get_option( 'akismet_author' ),
				'akismet_author_email' => $this->_get_option( 'akismet_author_email' ),
				'akismet_author_url'   => $this->_get_option( 'akismet_author_url' ),
				'tracking_number'      => $this->_get_option( Unomoon_Form_Config::TRACKINGNUMBER ),
			),
			$form_key
		);
	}

	/**
	 * Render styles meta box.
	 */
	public function _style() {
		$this->_render(
			'admin/style',
			array(
				'styles' => $this->styles,
				'style'  => $this->_get_option( 'style' ),
			)
		);
	}

	/**
	 * Get form option.
	 *
	 * @param string $key Key of option.
	 * @return mixed
	 */
	protected function _get_option( $key ) {
		global $post;
		$Setting = new Unomoon_Form_Setting( $post->ID );

		if ( Unomoon_Form_Config::TRACKINGNUMBER === $key ) {
			$value = $Setting->get_tracking_number();
		} else {
			$value = $Setting->get( $key );
		}

		if ( ! empty( $value ) ) {
			return $value;
		}

		if ( 'auto-draft' === $post->post_status ) {
			return apply_filters( 'unomoonform_default_settings', null, $key );
		}
		return '';
	}
}
