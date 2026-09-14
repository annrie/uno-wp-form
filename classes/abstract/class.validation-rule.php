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
 * Unomoon_Form_Abstract_Validation_Rule
 */
abstract class Unomoon_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * @var Unomoon_Form_Data
	 */
	protected $Data;

	/**
	 * Constructor.
	 *
	 * @param Unomoon_Form_Data $Data Unomoon_Form_Data object.
	 */
	public function __construct( Unomoon_Form_Data $Data = null ) {
		if ( ! $this->get_name() ) {
			exit( 'Unomoon_Form_Abstract_Validation_Rule::$name must override.' );
		}

		if ( ! is_null( $Data ) ) {
			$this->Data = $Data;
		}

		add_filter( 'unomoonform_validation_rules', array( $this, '_unomoonform_validation_rules' ) );
	}

	/**
	 * Generate array of validation rules.
	 *
	 * @param array $validation_rules Array of Unomoon_Form_Abstract_Validation_Rule.
	 * @return array
	 */
	public function _unomoonform_validation_rules( array $validation_rules ) {
		$validation_rules[ $this->get_name() ] = $this;
		return $validation_rules;
	}

	/**
	 * Inject Unomoon_Form_Data.
	 *
	 * @deprecated
	 *
	 * @param Unomoon_Form_Data $Data Unomoon_Form_Data object.
	 */
	public function set_Data( Unomoon_Form_Data $Data ) {
		$this->Data = $Data;
	}

	/**
	 * Return true when set $this->Data.
	 *
	 * @return boolean
	 */
	public function is_set_Data() {
		return ( is_a( $this->Data, 'Unomoon_Form_Data' ) );
	}

	/**
	 * Return validation rule name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Return validation rule name.
	 *
	 * @deprecated
	 *
	 * @return string
	 */
	public function getName() {
		Unomoon_Form_Functions::deprecated_message(
			get_class( $this ) . '::getName()',
			get_class( $this ) . '::get_name()'
		);
		return $this->get_name();
	}

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	abstract public function rule( $name, array $options = array() );

	/**
	 * Add setting field to validation rule setting panel.
	 *
	 * @param int   $key   ID of validation rule.
	 * @param array $value Content of validation rule.
	 */
	abstract public function admin( $key, $value );
}
