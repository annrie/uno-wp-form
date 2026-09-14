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
 * Unomoon_Form_Validation_Rule_Between
 */
class Unomoon_Form_Validation_Rule_Between extends Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'between';

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
			'max'     => 0,
			'message' => __( 'The number of characters is invalid.', 'unomoon-form' ),
		);
		$options  = array_merge( $defaults, $options );
		$length   = mb_strlen( $value, get_bloginfo( 'charset' ) );
		if ( Unomoon_Form_Functions::is_numeric( $options['min'] ) ) {
			if ( Unomoon_Form_Functions::is_numeric( $options['max'] ) ) {
				if ( $options['min'] > $length || $length > $options['max'] ) {
					return $options['message'];
				}
			}

			if ( $options['min'] > $length ) {
				return $options['message'];
			}
		} elseif ( Unomoon_Form_Functions::is_numeric( $options['max'] ) ) {
			if ( $options['max'] < $length ) {
				return $options['message'];
			}
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
		$max = '';
		if ( is_array( $value[ $this->get_name() ] ) ) {
			if ( isset( $value[ $this->get_name() ]['min'] ) ) {
				$min = $value[ $this->get_name() ]['min'];
			}
			if ( isset( $value[ $this->get_name() ]['max'] ) ) {
				$max = $value[ $this->get_name() ]['max'];
			}
		}
		?>
		<table>
			<tr>
				<td><?php esc_html_e( 'The range of the number of characters', 'unomoon-form' ); ?></td>
				<td>
					<input type="text" value="<?php echo esc_attr( $min ); ?>" size="3" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[validation][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $this->get_name() ); ?>][min]" />
					〜
					<input type="text" value="<?php echo esc_attr( $max ); ?>" size="3" name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[validation][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $this->get_name() ); ?>][max]" />
				</td>
			</tr>
		</table>
		<?php
	}
}
