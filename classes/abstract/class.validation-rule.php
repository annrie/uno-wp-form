<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Abstract_Validation_Rule
 */
abstract class Uno_WP_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * @var Uno_WP_Form_Data
	 */
	protected $Data;

	/**
	 * Constructor.
	 *
	 * @param Uno_WP_Form_Data $Data Uno_WP_Form_Data object.
	 */
	public function __construct( Uno_WP_Form_Data $Data = null ) {
		if ( ! $this->get_name() ) {
			exit( 'Uno_WP_Form_Abstract_Validation_Rule::$name must override.' );
		}

		if ( ! is_null( $Data ) ) {
			$this->Data = $Data;
		}

		add_filter( 'unoform_validation_rules', array( $this, '_unoform_validation_rules' ) );
	}

	/**
	 * Generate array of validation rules.
	 *
	 * @param array $validation_rules Array of Uno_WP_Form_Abstract_Validation_Rule.
	 * @return array
	 */
	public function _unoform_validation_rules( array $validation_rules ) {
		$validation_rules[ $this->get_name() ] = $this;
		return $validation_rules;
	}

	/**
	 * Inject Uno_WP_Form_Data.
	 *
	 * @deprecated
	 *
	 * @param Uno_WP_Form_Data $Data Uno_WP_Form_Data object.
	 */
	public function set_Data( Uno_WP_Form_Data $Data ) {
		$this->Data = $Data;
	}

	/**
	 * Return true when set $this->Data.
	 *
	 * @return boolean
	 */
	public function is_set_Data() {
		return ( is_a( $this->Data, 'Uno_WP_Form_Data' ) );
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
		UWF_Functions::deprecated_message(
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
