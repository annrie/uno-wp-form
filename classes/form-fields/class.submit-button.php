<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Field_Submit_Button
 */
class Uno_WP_Form_Field_Submit_Button extends Uno_WP_Form_Abstract_Form_Field {

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
			'shortcode_name' => 'unoform_submitButton',
			'display_name'   => __( 'Confirm &amp; Submit', 'uno-wp-form' ),
		);
	}

	/**
	 * Set default attributes.
	 *
	 * @return array
	 */
	protected function set_defaults() {
		return array(
			'name'          => '',
			'class'         => null,
			'confirm_value' => __( 'Confirm', 'uno-wp-form' ),
			'submit_value'  => __( 'Send', 'uno-wp-form' ),
		);
	}

	/**
	 * Callback of add shortcode for input page.
	 *
	 * @return string
	 */
	protected function input_page() {
		if ( ! empty( $this->atts['confirm_value'] ) ) {
			return $this->Form->submit(
				UWF_Config::CONFIRM_BUTTON,
				$this->atts['confirm_value'],
				array(
					'class' => $this->atts['class'],
				)
			);
		}
		return $this->Form->submit(
			$this->atts['name'],
			$this->atts['submit_value'],
			array(
				'class' => $this->atts['class'],
			)
		);
	}

	/**
	 * Callback of add shortcode for confirm page.
	 *
	 * @return string
	 */
	protected function confirm_page() {
		return $this->Form->submit(
			$this->atts['name'],
			$this->atts['submit_value'],
			array(
				'class' => $this->atts['class'],
			)
		);
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
			<strong>name</strong>
			<?php $name = $this->get_value_for_generator( 'name', $options ); ?>
			<input type="text" name="name" value="<?php echo esc_attr( $name ); ?>" />
		</p>
		<p>
			<strong>class</strong>
			<?php $class = $this->get_value_for_generator( 'class', $options ); ?>
			<input type="text" name="class" value="<?php echo esc_attr( $class ); ?>" />
		</p>
		<p>
			<strong><?php esc_html_e( 'String on the confirm button', 'uno-wp-form' ); ?></strong>
			<?php $confirm_value = $this->get_value_for_generator( 'confirm_value', $options ); ?>
			<input type="text" name="confirm_value" value="<?php echo esc_attr( $confirm_value ); ?>" />
		</p>
		<p>
			<strong><?php esc_html_e( 'String on the submit button', 'uno-wp-form' ); ?></strong>
			<?php $submit_value = $this->get_value_for_generator( 'submit_value', $options ); ?>
			<input type="text" name="submit_value" value="<?php echo esc_attr( $submit_value ); ?>" />
		</p>
		<?php
	}
}
