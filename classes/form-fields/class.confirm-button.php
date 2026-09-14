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
 * Unomoon_Form_Field_Confirm_Button
 */
class Unomoon_Form_Field_Confirm_Button extends Unomoon_Form_Abstract_Form_Field {

	/**
	 * Types of form type.
	 * input|select|button|input_button|error|other.
	 *
	 * @var string
	 */
	public $type = 'input_button';

	/**
	 * Set shortcode_name and display_name.
	 * Overwrite required for each child class.
	 *
	 * @return array
	 */
	protected function set_names() {
		return array(
			'shortcode_name' => 'unomoonform_confirmButton',
			'display_name'   => __( 'Confirm Button', 'unomoon-form' ),
		);
	}

	/**
	 * Set default attributes.
	 *
	 * @return array
	 */
	protected function set_defaults() {
		return array(
			'class' => null,
			'value' => __( 'Confirm', 'unomoon-form' ),
		);
	}

	/**
	 * Callback of add shortcode for input page.
	 *
	 * @return string
	 */
	protected function input_page() {
		return $this->Form->submit(
			Unomoon_Form_Config::CONFIRM_BUTTON,
			$this->atts['value'],
			array(
				'class' => $this->atts['class'],
			)
		);
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
	public function unomoonform_tag_generator_dialog( array $options = array() ) {
		?>
		<p>
			<strong>class</strong>
			<?php $class = $this->get_value_for_generator( 'class', $options ); ?>
			<input type="text" name="class" value="<?php echo esc_attr( $class ); ?>" />
		</p>
		<p>
			<strong><?php esc_html_e( 'String on the button', 'unomoon-form' ); ?></strong>
			<?php $name = $this->get_value_for_generator( 'name', $options ); ?>
			<input type="text" name="value" value="<?php echo esc_attr( $name ); ?>" />
		</p>
		<?php
	}
}
