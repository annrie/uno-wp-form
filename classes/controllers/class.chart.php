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
 * Unomoon_Form_Chart_Controller
 */
class Unomoon_Form_Chart_Controller extends Unomoon_Form_Controller {

	/**
	 * Chart types that can be rendered.
	 *
	 * @var array
	 */
	const CHART_TYPES = array( 'pie', 'bar' );

	/**
	 * Post type of saved inquiry data to display in this chart.
	 *
	 * @var string
	 */
	protected $formkey;

	/**
	 * Settings of the form.
	 *
	 * @var array
	 */
	protected $postdata = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only screen selector, validated against known post types below.
		if ( ! empty( $_GET['formkey'] ) ) {
			$this->formkey = sanitize_key( wp_unslash( $_GET['formkey'] ) );
		}
		// phpcs:enable

		$contact_data_post_types = Unomoon_Form_Contact_Data_Setting::get_form_post_types();
		if ( ! in_array( $this->formkey, $contact_data_post_types, true ) ) {
			exit;
		}
		add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );

		$screen = get_current_screen();
		add_action( 'load-' . $screen->id, array( $this, '_save' ) );
		add_action( $screen->id, array( $this, '_index' ) );
	}

	/**
	 * Enqueue assets.
	 */
	public function _admin_enqueue_scripts() {
		Unomoon_Form_Functions::enqueue_jquery_ui_style();

		wp_enqueue_script( 'jquery-ui-sortable' );

		$url = UNOMOON_FORM_PLUGIN_URL;

		wp_enqueue_style(
			Unomoon_Form_Config::NAME . '-admin-repeatable',
			$url . '/css/admin-repeatable.css',
			array(),
			UNOMOON_FORM_VERSION
		);

		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-repeatable',
			$url . '/js/unomoon-form-repeatable.js',
			array( 'jquery' ),
			UNOMOON_FORM_VERSION,
			true
		);

		// Bundled Chart.js (MIT). Replaces the Google Charts loader so that nothing is fetched from external servers.
		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-chartjs',
			$url . '/js/vendor/chart.umd.js',
			array(),
			'4.5.1',
			true
		);

		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-chart',
			$url . '/js/unomoon-form-chart.js',
			array( 'jquery', Unomoon_Form_Config::NAME . '-chartjs' ),
			UNOMOON_FORM_VERSION,
			true
		);

		wp_enqueue_script(
			Unomoon_Form_Config::NAME . '-admin-chart',
			$url . '/js/admin-chart.js',
			array( 'jquery', 'jquery-ui-sortable', Unomoon_Form_Config::NAME . '-repeatable' ),
			UNOMOON_FORM_VERSION,
			true
		);
	}

	/**
	 * Save.
	 */
	public function _save() {
		if ( ! isset( $_POST[ Unomoon_Form_Config::NAME . '-chart-nonce-field' ] ) ) {
			return;
		}

		if ( empty( $_POST[ Unomoon_Form_Config::NAME . '-chart-nonce-field' ] ) ) {
			return;
		}

		if ( ! check_admin_referer( Unomoon_Form_Config::NAME . '-chart-action', Unomoon_Form_Config::NAME . '-chart-nonce-field' ) ) {
			return;
		}

		if ( ! current_user_can( Unomoon_Form_Config::CAPABILITY ) ) {
			return;
		}

		if ( ! $this->formkey ) {
			return;
		}

		$option_name = Unomoon_Form_Config::NAME . '-chart-' . $this->formkey;
		$posted      = array();
		if ( isset( $_POST[ $option_name ] ) && is_array( $_POST[ $option_name ] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized per value in _sanitize().
			$posted = wp_unslash( $_POST[ $option_name ] );
		}
		update_option( $option_name, $this->_sanitize( $posted ) );
		wp_safe_redirect(
			admin_url(
				'edit.php?post_type=' . Unomoon_Form_Config::NAME . '&page=' . Unomoon_Form_Config::NAME . '-chart&formkey=' . $this->formkey
			)
		);
		exit;
	}

	/**
	 * Display chart page.
	 */
	public function _index() {
		$post_type = $this->formkey;

		$args = apply_filters( 'unomoonform_get_inquiry_data_args-' . $post_type, array() );
		if ( empty( $args ) || ! is_array( $args ) ) {
			$args = array();
		}
		$args = array_merge(
			$args,
			array(
				'posts_per_page' => -1,
				'post_type'      => $post_type,
			)
		);

		$form_posts = get_posts( $args );

		// custom_keys
		$custom_keys = array();
		foreach ( $form_posts as $post ) {
			$post_custom_keys = get_post_custom_keys( $post->ID );
			if ( is_array( $post_custom_keys ) ) {
				foreach ( $post_custom_keys as $post_custom_key ) {
					if ( preg_match( '/^_/', $post_custom_key ) ) {
						continue;
					}
					$post_meta                                       = get_post_meta( $post->ID, $post_custom_key, true );
					$custom_keys[ $post_custom_key ][ $post_meta ][] = $post->ID;
				}
			}
		}

		// postdata
		$postdata = array();
		$option   = get_option( Unomoon_Form_Config::NAME . '-chart-' . $post_type );
		if ( is_array( $option ) && isset( $option['chart'] ) && is_array( $option['chart'] ) ) {
			$postdata = $option['chart'];
		}

		$chart_data = $this->_build_chart_data( $postdata, $custom_keys, count( $form_posts ) );

		// Hand the aggregated data to the renderer as a JSON literal instead of an inline <script> in the template.
		wp_add_inline_script(
			Unomoon_Form_Config::NAME . '-chart',
			'var unomoonformChartData = ' . wp_json_encode( $chart_data ) . ';',
			'before'
		);

		$default_keys = array(
			'target'    => '',
			'separator' => '',
			'chart'     => '',
		);
		// 空の隠れフィールド（コピー元）を挿入
		array_unshift( $postdata, $default_keys );

		$this->_render(
			'chart/index',
			array(
				'post_type'   => $post_type,
				'form_posts'  => $form_posts,
				'custom_keys' => $custom_keys,
				'postdata'    => $postdata,
				'chart_data'  => $chart_data,
			),
			$this->formkey
		);
	}

	/**
	 * Aggregate saved inquiry data into a series for each configured chart.
	 *
	 * @param array $postdata    Chart rows saved in the option (target / chart / separator).
	 * @param array $custom_keys Meta key => meta value => array of post IDs.
	 * @param int   $total       Number of inquiries.
	 * @return array Chart row index => array( target, chart, labels, counts, total ).
	 */
	protected function _build_chart_data( array $postdata, array $custom_keys, $total ) {
		$chart_data = array();

		foreach ( $postdata as $postdata_key => $chart ) {
			if ( empty( $chart['target'] ) || ! isset( $custom_keys[ $chart['target'] ] ) ) {
				continue;
			}

			$separator = isset( $chart['separator'] ) ? (string) $chart['separator'] : '';
			$raw_data  = array();
			foreach ( $custom_keys[ $chart['target'] ] as $item => $values ) {
				$item  = (string) $item;
				$items = ( '' !== $separator && false !== strpos( $item, $separator ) ) ? explode( $separator, $item ) : array( $item );
				foreach ( $items as $_item ) {
					if ( '' === $_item ) {
						$_item = '(Empty)';
					}
					if ( empty( $raw_data[ $_item ] ) ) {
						$raw_data[ $_item ] = count( $values );
					} else {
						$raw_data[ $_item ] += count( $values );
					}
				}
			}

			$chart_data[ $postdata_key ] = array(
				'target' => (string) $chart['target'],
				'chart'  => ( isset( $chart['chart'] ) && in_array( $chart['chart'], self::CHART_TYPES, true ) ) ? $chart['chart'] : 'pie',
				'labels' => array_map( 'strval', array_keys( $raw_data ) ),
				'counts' => array_values( $raw_data ),
				'total'  => (int) $total,
			);
		}

		return $chart_data;
	}

	/**
	 * Sanitize for settings.
	 *
	 * @param array $input Posted data from chart settings page.
	 * @return array
	 */
	public function _sanitize( $input ) {
		if ( ! is_array( $input ) || ! isset( $input['chart'] ) || ! is_array( $input['chart'] ) ) {
			return array();
		}

		$new_input = array();

		foreach ( $input['chart'] as $key => $value ) {
			if ( ! is_array( $value ) || empty( $value['target'] ) ) {
				continue;
			}

			$chart = isset( $value['chart'] ) ? sanitize_key( $value['chart'] ) : '';

			$new_input['chart'][ absint( $key ) ] = array(
				'target'    => sanitize_text_field( $value['target'] ),
				'chart'     => in_array( $chart, self::CHART_TYPES, true ) ? $chart : 'pie',
				'separator' => isset( $value['separator'] ) ? sanitize_text_field( $value['separator'] ) : '',
			);
		}

		return $new_input;
	}
}
