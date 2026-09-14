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
 * Unomoon_Form_Validation_Rule_Required
 */
class Unomoon_Form_Validation_Rule_Required extends Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'required';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );
		// When value exist, or value not exist but other values also not exist(= Not posted)
		if ( ! is_null( $value ) || is_null( $value ) && ! $this->Data->gets() ) {
			return;
		}

		$defaults = array(
			'message' => __( 'This is required.', 'unomoon-form' ),
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
		<label><input type="checkbox" <?php checked( $value[ $this->get_name() ], 1 ); ?> name="<?php echo esc_attr( Unomoon_Form_Config::NAME ); ?>[validation][<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $this->get_name() ); ?>]" value="1" /><?php esc_html_e( 'No empty( with checkbox )', 'unomoon-form' ); ?></label>
		<?php
	}
}
