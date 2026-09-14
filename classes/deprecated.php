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
 * Unomoon_Form_Deprecated
 */
class Unomoon_Form_Deprecated {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'unomoonform_after_exec_shortcode', array( $this, '_unomoonform_after_exec_shortcode2' ), 10000 );
	}

	/**
	 * Deprecated message for unomoonform_after_exec_shortcode.
	 */
	public function _unomoonform_after_exec_shortcode2() {
		remove_action(
			'unomoonform_after_exec_shortcode',
			array( $this, '_unomoonform_after_exec_shortcode2' ),
			10000
		);

		if ( has_action( 'unomoonform_after_exec_shortcode' ) ) {
			Unomoon_Form_Functions::deprecated_message(
				'unomoonform_after_exec_shortcode',
				'unomoonform_start_main_process'
			);
		}
	}
}

new Unomoon_Form_Deprecated();
