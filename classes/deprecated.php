<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * UWF_Deprecated
 */
class UWF_Deprecated {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'unoform_after_exec_shortcode', array( $this, '_unoform_after_exec_shortcode2' ), 10000 );
	}

	/**
	 * Deprecated message for unoform_after_exec_shortcode.
	 */
	public function _unoform_after_exec_shortcode2() {
		remove_action(
			'unoform_after_exec_shortcode',
			array( $this, '_unoform_after_exec_shortcode2' ),
			10000
		);

		if ( has_action( 'unoform_after_exec_shortcode' ) ) {
			UWF_Functions::deprecated_message(
				'unoform_after_exec_shortcode',
				'unoform_start_main_process'
			);
		}
	}
}

new UWF_Deprecated();
