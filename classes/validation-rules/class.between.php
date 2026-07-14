<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Validation_Rule_Between
 */
class Uno_WP_Form_Validation_Rule_Between extends Uno_WP_Form_Abstract_Validation_Rule {

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
		$value = UWF_Functions::convert_eol( $value );

		if ( UWF_Functions::is_empty( $value ) ) {
			return;
		}

		$defaults = array(
			'min'     => 0,
			'max'     => 0,
			'message' => __( 'The number of characters is invalid.', 'uno-wp-form' ),
		);
		$options  = array_merge( $defaults, $options );
		$length   = mb_strlen( $value, get_bloginfo( 'charset' ) );
		if ( UWF_Functions::is_numeric( $options['min'] ) ) {
			if ( UWF_Functions::is_numeric( $options['max'] ) ) {
				if ( $options['min'] > $length || $length > $options['max'] ) {
					return $options['message'];
				}
			}

			if ( $options['min'] > $length ) {
				return $options['message'];
			}
		} elseif ( UWF_Functions::is_numeric( $options['max'] ) ) {
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
				<td><?php esc_html_e( 'The range of the number of characters', 'uno-wp-form' ); ?></td>
				<td>
					<input type="text" value="<?php echo esc_attr( $min ); ?>" size="3" name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>][min]" />
					〜
					<input type="text" value="<?php echo esc_attr( $max ); ?>" size="3" name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>][max]" />
				</td>
			</tr>
		</table>
		<?php
	}
}
