<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Field_Error
 */
class Uno_WP_Form_Field_Error extends Uno_WP_Form_Abstract_Form_Field {

	/**
	 * Types of form type.
	 * input|select|button|input_button|error|other.
	 *
	 * @var string
	 */
	public $type = 'error';

	/**
	 * Set shortcode_name and display_name.
	 * Overwrite required for each child class.
	 *
	 * @return array
	 */
	protected function set_names() {
		return array(
			'shortcode_name' => 'unoform_error',
			'display_name'   => __( 'Error Message', 'uno-wp-form' ),
		);
	}

	/**
	 * Set default attributes.
	 *
	 * @return array
	 */
	protected function set_defaults() {
		return array(
			'keys' => '',
		);
	}

	/**
	 * Callback of add shortcode for input page.
	 *
	 * @return string
	 */
	protected function input_page() {
		$keys = explode( ',', $this->atts['keys'] );
		$_ret = '';
		foreach ( $keys as $key ) {
			$_ret .= $this->get_error( trim( $key ) );
		}
		return $_ret;
	}

	/**
	 * Callback of add shortcode for confirm page.
	 */
	protected function confirm_page() {
	}

	/**
	 * Display tag generator dialog.
	 * Overwrite required for each child class.
	 *
	 * @param array $options Options.
	 */
	public function unoform_tag_generator_dialog( array $options = array() ) {
		?>
		<p>
			<strong><?php esc_html_e( 'name of the element which wants to display error', 'uno-wp-form' ); ?></strong>
			<?php $keys = "\n" . $this->get_value_for_generator( 'keys', $options ); ?>
			<textarea name="keys"><?php echo esc_attr( $keys ); ?></textarea>
			<span class="uwf_note">
				<?php esc_html_e( 'Input one line about one item.', 'uno-wp-form' ); ?>
			</span>
		</p>
		<?php
	}
}
