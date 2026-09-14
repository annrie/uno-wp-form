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
 * Unomoon_Form_Exec_Shortcode
 */
class Unomoon_Form_Exec_Shortcode {

	/**
	 * @var int
	 */
	protected $form_id;

	/**
	 * @var string
	 */
	protected $form_key;

	/**
	 * @var Unomoon_Form_Data
	 */
	protected $Data;

	/**
	 * @var string
	 */
	protected $view_flg;

	/**
	 * @var Unomoon_Form_Setting
	 */
	protected $Setting;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_shortcode( 'unomoonform', array( $this, '_unomoonform' ) );
		add_shortcode( 'unomoonform_complete_message', array( $this, '_unomoonform_complete_message' ) );

		add_filter( 'unomoonform_form_end_html', array( $this, '_unomoonform_form_end_html' ) );

		add_action( 'wp_footer', array( $this, '_enqueue_scripts' ) );
	}

	/**
	 * Add shortcode for [unomoonform_formkey]
	 *
	 * @example [unomoonform_formkey key="post_id"]
	 *
	 * @param array $attributes Attributes of [unomoonform_formkey].
	 * @return string
	 */
	public function initialize( $attributes ) {
		$this->form_id = $this->_get_form_id_by_unomoonform_formkey( $attributes );
		if ( ! $this->form_id ) {
			return;
		}

		$this->form_key = Unomoon_Form_Functions::get_form_key_from_form_id( $this->form_id );

		/**
		 * @deprecated since v4.0.0
		 * Because refactoring changed the timing to execute the shortcode
		 */
		do_action( 'unomoonform_after_exec_shortcode', $this->form_key );

		do_action( 'unomoonform_start_main_process', $this->form_key );

		$this->Data     = Unomoon_Form_Data::connect( $this->form_key );
		$this->view_flg = ( $this->Data->get_view_flg() ) ? $this->Data->get_view_flg() : 'input';
		$this->Setting  = new Unomoon_Form_Setting( $this->form_id );

		add_action( 'wp_footer', array( $this->Data, 'clear_values' ) );

		$Validation = new Unomoon_Form_Validation( $this->form_key );
		$is_valid   = $Validation->is_valid();

		$Redirected = new Unomoon_Form_Redirected( $this->form_key, $this->Setting, $is_valid, $this->Data->get_post_condition() );
		if ( $Redirected->get_request_uri() !== $Redirected->get_url() && $Redirected->get_url() ) {
			$Redirected->redirect_js();
		}

		do_action( 'unomoonform_before_load_content_' . $this->form_key );

		if ( $this->_is_direct_access() ) {
			$content = $this->_get_direct_access_error_page_content();
		} elseif ( $this->Data->get_send_error() ) {
			$content = $this->_get_send_error_page_content();
		} elseif ( 'input' === $this->view_flg ) {
			$content = $this->_get_input_page_content();
		} elseif ( 'confirm' === $this->view_flg ) {
			$content = $this->_get_confirm_page_content();
		} elseif ( 'complete' === $this->view_flg ) {
			$content = $this->_get_complete_page_content();
		} else {
			$content = '';
		}

		do_action( 'unomoonform_after_load_content_' . $this->form_key );

		// Enqueue scroll to Unomoon Form script
		if ( $this->Setting->get( 'scroll' ) ) {
			if (
				'input' !== $this->view_flg
				|| in_array( $this->Data->get_post_condition(), array( 'back', 'confirm', 'complete' ), true )
			) {
				add_action( 'wp_footer', array( $this, '_enqueue_scroll_script' ) );
			}
		}

		$Form_Fields = Unomoon_Form_Form_Fields::instantiation( $this->form_key );
		foreach ( $Form_Fields->get_form_fields() as $form_field ) {
			$form_field->initialize( new Unomoon_Form_Form(), $this->form_key, $this->view_flg );
		}

		// Sanitizes content for allowed HTML tags for post content.
		$content = wp_kses_post( $content );

		return do_shortcode( $content );
	}

	/**
	 * Add shortcode for [unomoonform].
	 *
	 * @param array  $attributes Attributes of [unomoonform].
	 * @param string $content    Content of [unomoonform].
	 * @return string
	 */
	public function _unomoonform( $attributes, $content = '' ) {
		$Form = new Unomoon_Form_Form();

		if ( in_array( $this->view_flg, array( 'input', 'confirm' ), true ) ) {
			$content            = $this->_get_the_content( $content );
			$upload_file_keys   = $this->Data->get_post_value_by_key( Unomoon_Form_Config::UPLOAD_FILE_KEYS );
			$upload_file_hidden = $this->_get_upload_file_hidden( $upload_file_keys );
			$old_confirm_class  = $this->_get_old_confirm_class();
			$class_by_style     = $this->_get_class_by_style();

			return sprintf(
				'<div id="unomoon_form_%s" class="unomoon_form unomoon_form_%s %s">
					%s
				<!-- end .unomoon_form --></div>',
				esc_attr( $this->form_key ),
				esc_attr( $this->view_flg . ' ' . $old_confirm_class ),
				$class_by_style,
				$Form->start() . do_shortcode( $content ) . $upload_file_hidden . $Form->end()
			);
		}
	}

	/**
	 * Add shortcode for [unomoonform_complete_message].
	 *
	 * @param array  $attributes Attributes of [unomoonform_complete_message].
	 * @param string $content    Content of [unomoonform_complete_message].
	 * @return string
	 */
	public function _unomoonform_complete_message( $attributes, $content = '' ) {
		return sprintf(
			'<div id="unomoon_form_%s" class="unomoon_form unomoon_form_%s">
				%s
			<!-- end .unomoon_form --></div>',
			esc_attr( $this->form_key ),
			esc_attr( $this->view_flg ),
			$content
		);
	}

	/**
	 * Display input page.
	 *
	 * @return string
	 */
	protected function _get_input_page_content() {
		global $post;
		$post = get_post( $this->form_id );
		setup_postdata( $post );
		// @todo 共通化 main._file_upload()
		$content = apply_filters( 'unomoonform_post_content_raw_' . $this->form_key, get_the_content(), $this->Data );
		$content = $this->_wpautop( $content );
		$content = apply_filters( 'unomoonform_post_content_' . $this->form_key, $content, $this->Data );
		$content = sprintf( '[unomoonform]%s[/unomoonform]', $content );
		wp_reset_postdata();
		return $content;
	}

	/**
	 * Display confirm page.
	 *
	 * @return string
	 */
	protected function _get_confirm_page_content() {
		return $this->_get_input_page_content();
	}

	/**
	 * Display complete page.
	 *
	 * @return string
	 */
	protected function _get_complete_page_content() {
		$Parser = new Unomoon_Form_Parser( $this->Setting );

		$content = apply_filters(
			'unomoonform_complete_content_raw_' . $this->form_key,
			$this->Setting->get( 'complete_message' ),
			$this->Data
		);

		$content = str_replace( '{' . Unomoon_Form_Config::TRACKINGNUMBER . '}', '{' . Unomoon_Form_Config::TRACKINGNUMBER . '_for_complete_page}', $content );
		$content = $this->_wpautop( $content );
		$content = $Parser->replace_for_complete_page( $content );
		$content = apply_filters( 'unomoonform_complete_content_' . $this->form_key, $content, $this->Data );

		$content = sprintf(
			'[unomoonform_complete_message]%s[/unomoonform_complete_message]',
			$content
		);
		return $content;
	}

	/**
	 * Display validation error page.
	 *
	 * @return string
	 */
	protected function _get_send_error_page_content() {
		$content = sprintf(
			'<div id="unomoon_form_%s" class="unomoon_form unomoon_form_send_error">
				%s
			<!-- end .unomoon_form --></div>',
			esc_attr( $this->form_key ),
			__( 'There was an error trying to send your message. Please try again later.', 'unomoon-form' )
		);
		$content = apply_filters( 'unomoonform_send_error_content_raw_' . $this->form_key, $content, $this->Data );
		$content = $this->_wpautop( $content );
		$content = apply_filters( 'unomoonform_send_error_content_' . $this->form_key, $content, $this->Data );
		return $content;
	}

	/**
	 * Display direct access error page
	 *
	 * @return string $content
	 */
	protected function _get_direct_access_error_page_content() {
		$content = sprintf(
			'<div id="unomoon_form_%s" class="unomoon_form unomoon_form_direct_access_error">
				%s
			<!-- end .unomoon_form --></div>',
			esc_attr( $this->form_key ),
			__( 'You can not access this page directly.', 'unomoon-form' )
		);
		$content = apply_filters( 'unomoonform_direct_access_error_content_raw_' . $this->form_key, $content, $this->Data );
		$content = $this->_wpautop( $content );
		$content = apply_filters( 'unomoonform_direct_access_error_content_' . $this->form_key, $content, $this->Data );
		return $content;
	}

	/**
	 * Return true when direct access to confirm or complete or validation error page.
	 *
	 * @return bool
	 */
	protected function _is_direct_access() {
		if ( 'input' !== $this->view_flg ) {
			return false;
		}

		$confirm  = $this->Setting->get( 'confirmation_url' );
		$complete = $this->Setting->get( 'complete_url' );
		$error    = $this->Setting->get( 'validation_error_url' );

		if ( ! $confirm && ! $complete && ! $error ) {
			return false;
		}

		$Validation = new Unomoon_Form_Validation( $this->form_key );
		$is_valid   = $Validation->is_valid();

		$Redirected = new Unomoon_Form_Redirected( $this->form_key, $this->Setting, $is_valid, $this->Data->get_post_condition() );
		if ( $Redirected->get_request_uri() === $Redirected->get_url() ) {
			return false;
		}

		return true;
	}

	/**
	 * Line breaks content according to wpautop().
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	protected function _wpautop( $content ) {
		$has_wpautop = false;

		if ( has_filter( 'the_content', '_restore_wpautop_hook' ) ) {
			$has_wpautop = true;
		} elseif ( has_filter( 'the_content', 'wpautop' ) ) {
			$has_wpautop = true;
		}

		$has_wpautop = apply_filters(
			'unomoonform_content_wpautop_' . $this->form_key,
			$has_wpautop,
			$this->view_flg
		);

		if ( $has_wpautop ) {
			$content = wpautop( $content );
		}

		return $content;
	}

	/**
	 * Replace {key} in the form.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public function _get_the_content( $content ) {
		$Parser  = new Unomoon_Form_Parser( $this->Setting );
		$content = $Parser->replace_for_page( $content );
		return $content;
	}

	/**
	 * Hidden field for file upload name attribute.
	 *
	 * @param array|string $upload_file_keys Upload file keys.
	 */
	protected function _get_upload_file_hidden( $upload_file_keys ) {
		$Form = new Unomoon_Form_Form();

		if ( ! is_array( $upload_file_keys ) ) {
			return;
		}

		$upload_file_hidden = '';
		foreach ( $upload_file_keys as $value ) {
			$upload_file_hidden .= $Form->hidden( Unomoon_Form_Config::UPLOAD_FILE_KEYS . '[]', $value );
		}

		return $upload_file_hidden;
	}

	/**
	 * Get classes for backward compatibility.
	 *
	 * @return string
	 */
	protected function _get_old_confirm_class() {
		if ( 'confirm' === $this->view_flg ) {
			return 'unomoon_form_preview';
		}
	}

	/**
	 * Get classes for style feature.
	 *
	 * @return string
	 */
	protected function _get_class_by_style() {
		$style = $this->Setting->get( 'style' );
		if ( $style ) {
			return 'unomoon_form_' . $style;
		}
	}

	/**
	 * ショートコード unomoonform_formkey をもとにフォームの ID を取得.
	 *
	 * @param array $attributes Attributes of unomoonform_formkey.
	 * @return string
	 */
	protected function _get_form_id_by_unomoonform_formkey( $attributes ) {
		$attributes = shortcode_atts(
			array(
				'key'  => '',
				'slug' => '',
			),
			$attributes
		);

		if ( ! empty( $attributes['slug'] ) ) {
			$post = get_page_by_path( $attributes['slug'], OBJECT, Unomoon_Form_Config::NAME );
		} elseif ( ! empty( $attributes['key'] ) ) {
			$post = get_post( $attributes['key'] );
		}

		if ( ! empty( $post ) && isset( $post->ID ) && 'publish' === $post->post_status ) {
			return $post->ID;
		}
	}

	/**
	 * Add nonce field and form meta data.
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	public function _unomoonform_form_end_html( $html ) {
		if ( ! $this->form_key ) {
			return $html;
		}

		$html .= sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			esc_attr( Unomoon_Form_Config::NAME . '-form-id' ),
			esc_attr( $this->form_id )
		);

		$html .= sprintf(
			'<input type="hidden" name="%1$s" value="%2$s" />',
			esc_attr( Unomoon_Form_Config::TOKEN_NAME ),
			esc_attr( Unomoon_Form_Csrf::token() )
		);
		return $html;
	}

	/**
	 * Enqueue Unomoon Form assets
	 *
	 * @return void
	 */
	public function _enqueue_scripts() {
		if ( wp_style_is( Unomoon_Form_Config::NAME ) ) {
			return;
		}

		Unomoon_Form_Functions::unomoonform_enqueue_scripts( $this->form_id );
	}

	/**
	 * Enqueue scroll to form script
	 *
	 * @return void
	 */
	public function _enqueue_scroll_script() {
		wp_register_script(
			Unomoon_Form_Config::NAME . '-scroll',
			UNOMOON_FORM_PLUGIN_URL . '/js/scroll.js',
			array( 'jquery' ),
			false,
			true
		);
		wp_localize_script(
			Unomoon_Form_Config::NAME . '-scroll',
			'unomoonform_scroll',
			array(
				'offset' => apply_filters( 'unomoonform_scroll_offset_' . $this->form_key, 0 ),
			)
		);
		wp_enqueue_script( Unomoon_Form_Config::NAME . '-scroll' );
	}
}
