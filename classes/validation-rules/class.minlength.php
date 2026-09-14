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
 * Unomoon_Form_Validation_Rule_MinLength
 */
class Unomoon_Form_Validation_Rule_MinLength extends Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'minlength';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );
		$value = Unomoon_Form_Functions::convert_eol( $value );

		if ( Unomoon_Form_Functions::is_empty( $value ) ) {
			return;
		}

		$defaults = array(
			'min'     => 0,
			'message' => __( 'The number of characters is a few.', 'unomoon-form' ),
		);
		$options  = array_merge( $defaults, $options );
		$length   = mb_strlen( $value, get_bloginfo( 'charset' ) );
		if ( Unomoon_Form_Functions::is_numeric( $options['min'] ) && $options['min'] > $length ) {
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
		$min = '';
		if ( is_array( $value[ $this->get_name() ] ) && isset( $value[ $this->get_name() ]['min'] ) ) {
			$min = $value[ $this->get_name() ]['min'];
		}
		?>
		<table>
			<tr>
				<td><?php esc_html_e( 'The number of the minimum characters', 'unomoon-form' ); ?></td>
				<td><input type="text" value="<?php echo esc_attr( $min ); ?>" size="3" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[validation][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $this->get_name() ); ?>][min]" /></td>
			</tr>
		</table>
		<?php
	}
}
