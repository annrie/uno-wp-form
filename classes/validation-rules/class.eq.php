<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Validation_Rule_Eq
 */
class Uno_WP_Form_Validation_Rule_Eq extends Uno_WP_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'eq';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );

		if ( is_null( $value ) ) {
			return;
		}

		$defaults     = array(
			'target'  => null,
			'message' => __( 'This is not in agreement.', 'uno-wp-form' ),
		);
		$options      = array_merge( $defaults, $options );
		$target_value = $this->Data->get( $options['target'] );

		if ( (string) $value !== (string) $target_value ) {
			return $options['message'];
		}
	}

	/**
	 * Add setting field to validation rule setting panel.
	 *
	 * @param numeric $key ID of validation rule.
	 * @param array   $value Content of validation rule.
	 * @return void
	 */
	public function admin( $key, $value ) {
		$target = '';
		if ( is_array( $value[ $this->get_name() ] ) && isset( $value[ $this->get_name() ]['target'] ) ) {
			$target = $value[ $this->get_name() ]['target'];
		}
		?>
		<table>
			<tr>
				<td><?php esc_html_e( 'The key at same value', 'uno-wp-form' ); ?></td>
				<td><input type="text" value="<?php echo esc_attr( $target ); ?>" name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>][target]" /></td>
			</tr>
		</table>
		<?php
	}
}
