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
 * Unomoon_Form_Validation_Rule_Tel
 */
class Unomoon_Form_Validation_Rule_Tel extends Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'tel';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );

		if ( Unomoon_Form_Functions::is_empty( $value ) ) {
			return;
		}

		$defaults = array(
			'message' => __( 'This is not the format of a tel number.', 'unomoon-form' ),
		);
		$options  = array_merge( $defaults, $options );
		if ( preg_match( '/^\d{2}-\d{4}-\d{4}$/', $value )
			|| preg_match( '/^\d{3}-\d{3,4}-\d{4}$/', $value )
			|| preg_match( '/^\d{4}-\d{2}-\d{4}$/', $value )
			|| preg_match( '/^\d{4}-\d{3}-\d{3}$/', $value )
			|| preg_match( '/^\d{5}-\d{1}-\d{4}$/', $value )
			|| preg_match( '/^\d{10,11}$/', $value ) ) {

			return;
		}

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
		<label><input type="checkbox" <?php checked( $value[ $this->get_name() ], 1 ); ?> name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[validation][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $this->get_name() ); ?>]" value="1" /><?php esc_html_e( 'Tel', 'unomoon-form' ); ?></label>
		<?php
	}

	/**
	 * This validation rule is for Japanese environments only.
	 *
	 * @param array $validation_rules Array of Unomoon_Form_Abstract_Validation_Rule.
	 * @return array
	 */
	public function _unomoonform_validation_rules( array $validation_rules ) {
		$validation_rules = parent::_unomoonform_validation_rules( $validation_rules );

		if ( 'ja' === get_locale() ) {
			return $validation_rules;
		}

		$validation_rules[ $this->get_name() ] = '';
		return $validation_rules;
	}
}
