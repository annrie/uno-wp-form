<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Validation_Rule_Hiragana
 */
class Uno_WP_Form_Validation_Rule_Hiragana extends Uno_WP_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'hiragana';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );

		if ( UWF_Functions::is_empty( $value ) ) {
			return;
		}

		if ( preg_match( '/^[ぁ-ゞ 　]*?[ぁ-ゞ]+?[ぁ-ゞ 　]*?$/u', $value ) ) {
			return;
		}

		$defaults = array(
			'message' => __( 'Please enter with a Japanese Hiragana.', 'uno-wp-form' ),
		);
		$options  = array_merge( $defaults, $options );
		return $options['message'];
	}

	/**
	 * Add setting field to validation rule setting panel.
	 *
	 * @param numeric $key ID of validation rule.
	 * @param array   $value Content of validation rule.
	 * @return void
	 */
	public function admin( $key, $value ) {
		?>
		<label><input type="checkbox" <?php checked( $value[ $this->get_name() ], 1 ); ?> name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>]" value="1" /><?php esc_html_e( 'Japanese Hiragana', 'uno-wp-form' ); ?></label>
		<?php
	}
}
