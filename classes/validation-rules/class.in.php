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
 * Unomoon_Form_Validation_Rule_In
 */
class Unomoon_Form_Validation_Rule_In extends Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'in';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );
		$value = (string) $value;

		if ( Unomoon_Form_Functions::is_empty( $value ) ) {
			return;
		}

		$defaults = array(
			'options' => array(),
			'message' => __( 'This value is invalid.', 'unomoon-form' ),
		);
		$options  = array_merge( $defaults, $options );
		if ( is_array( $options['options'] ) ) {
			foreach ( $options['options'] as $option ) {
				$option = (string) $option;
				if ( $value === $option ) {
					return;
				}
			}
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
	public function admin(
		// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$key,
		$value
		// phpcs:enable
	) {
	}
}
