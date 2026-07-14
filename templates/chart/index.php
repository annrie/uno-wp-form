<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<div class="wrap">
	<?php $post_id = preg_replace( '/^(.+_)(\d+)$/', '$2', $post_type ); ?>
	<h2>
		<?php esc_html_e( 'Chart', 'uno-wp-form' ); ?>
		:
		<?php echo esc_html( get_the_title( $post_id ) ); ?>
	</h2>
	<form method="post" action="">
		<?php
		wp_nonce_field( UWF_Config::NAME . '-chart-action', UWF_Config::NAME . '-chart-nonce-field' );
		?>
		<div id="<?php echo esc_attr( UWF_Config::NAME . '_chart' ); ?>" class="postbox">
			<div class="inside">
				<b class="add-btn"><?php esc_html_e( 'Add Chart', 'uno-wp-form' ); ?></b>
				<div class="repeatable-boxes">
					<?php foreach ( $postdata as $key => $value ) : ?>
					<div class="repeatable-box"
						<?php
						if ( 0 === $key ) :
							?>
						style="display:none"<?php endif; ?>>
						<div class="sortable-icon-handle"></div>
						<div class="remove-btn"><b>×</b></div>
						<div class="open-btn"><span><?php echo esc_html( $value['target'] ); ?></span><b>▼</b></div>
						<div class="repeatable-box-content">
							<?php esc_html_e( 'Item that create chart', 'uno-wp-form' ); ?>
							<select class="targetKey" name="<?php echo esc_attr( sprintf( '%s-chart-%s[chart][%s][target]', UWF_Config::NAME, $post_type, $key ) ); ?>">
								<option value=""><?php esc_html_e( 'Select this.', 'uno-wp-form' ); ?></option>
								<?php foreach ( $custom_keys as $custom_key_name => $custom_key_value ) : ?>
								<option value="<?php echo esc_attr( $custom_key_name ); ?>" <?php selected( $value['target'], $custom_key_name ); ?>><?php echo esc_html( $custom_key_name ); ?></option>
								<?php endforeach; ?>
							</select>
							<br />
							<?php esc_html_e( 'Chart type', 'uno-wp-form' ); ?>
							<select name="<?php echo esc_attr( sprintf( '%s-chart-%s[chart][%s][chart]', UWF_Config::NAME, $post_type, $key ) ); ?>">
								<?php
								$chart_options = array(
									'pie' => esc_html__( 'Pie chart', 'uno-wp-form' ),
									'bar' => esc_html__( 'Bar chart', 'uno-wp-form' ),
								);
								foreach ( $chart_options as $chart_option_key => $chart_option ) {
									printf(
										'<option value="%s" %s>%s</option>',
										esc_attr( $chart_option_key ),
										selected( $value['chart'], $chart_option_key, false ),
										esc_html( $chart_option )
									);
								}
								?>
							</select>
							<br />
							<?php esc_html_e( 'Separator string (If the check box. If the separator attribute is not set to ",")', 'uno-wp-form' ); ?>
							<input type="text" name="<?php echo esc_attr( sprintf( '%s-chart-%s[chart][%s][separator]', UWF_Config::NAME, $post_type, $key ) ); ?>" value="<?php echo esc_attr( $value['separator'] ); ?>" size="5" />
						<!-- end .repeatable-box-content --></div>
					<!-- end .repeatable-box --></div>
					<?php endforeach; ?>
				<!-- end .repeatable-boxes --></div>
				<input type="hidden" name="<?php echo esc_attr( sprintf( '%s-formkey', UWF_Config::NAME ) ); ?>" value="<?php echo esc_attr( $post_type ); ?>" />
				<?php submit_button(); ?>
			<!-- end .inside --></div>
		<!-- end #uno-wp-form_chart --></div>
	</form>

	<?php
	foreach ( $postdata as $postdata_key => $chart ) {
		if ( ! isset( $custom_keys[ $chart['target'] ] ) ) {
			// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedUnsetVariable
			unset( $postdata[ $postdata_key ] );
			// phpcs:enable
			continue;
		}
		printf(
			'<h3>%s <span style="font-weight:normal;font-size:14px">( %s: %d )</span></h3>
			<div class="%s" style="width: 100%%; max-width: 800px"></div>',
			esc_html( $chart['target'] ),
			esc_html__( 'The number of inquiries', 'uno-wp-form' ),
			count( $form_posts ),
			esc_attr( UWF_Config::NAME . '-chart-div-' . $postdata_key )
		);
	}

	$chart_data = array();
	foreach ( $postdata as $postdata_key => $chart ) {
		$data     = array();
		$raw_data = array();
		foreach ( $custom_keys[ $chart['target'] ] as $item => $values ) {
			if ( $chart['separator'] && strstr( $item, $chart['separator'] ) ) {
				$item = explode( $chart['separator'], $item );
			}
			if ( is_array( $item ) ) {
				foreach ( $item as $_item ) {
					if ( '' === $_item ) {
						$_item = '(Empty)';
					}
					if ( empty( $raw_data[ $_item ] ) ) {
						$raw_data[ $_item ] = count( $values );
					} else {
						$raw_data[ $_item ] += count( $values );
					}
				}
			} else {
				if ( '' === $item ) {
					$item = '(Empty)';
				}
				if ( empty( $raw_data[ $item ] ) ) {
					$raw_data[ $item ] = count( $values );
				} else {
					$raw_data[ $item ] += count( $values );
				}
			}
		}
		$data[] = array( '', '' );
		foreach ( $raw_data as $raw_data_key => $raw_data_value ) {
			if ( 'bar' === $chart['chart'] ) {
				$value = $raw_data_value / count( $form_posts );
			} else {
				$value = $raw_data_value;
			}
			$data[] = array(
				(string) $raw_data_key,
				$value,
			);
		}
		$chart_data[ $postdata_key ] = array(
			'chart' => $chart['chart'],
			'data'  => $data,
		);
	}
	?>
	<script>
	google.load( 'visualization', 1, { packages:['corechart'] } );
	google.setOnLoadCallback( unoformDrawCharts );
	function unoformDrawCharts() {
		jQuery( function( $ ) {
			<?php foreach ( $chart_data as $postdata_key => $chart ) : ?>
			$( '.<?php echo esc_js( UWF_Config::NAME . '-chart-div-' . $postdata_key ); ?>' )
				.uno_wp_form_google_chart( {
					chart: <?php echo json_encode( $chart['chart'] ); ?>,
					data : <?php echo json_encode( $chart['data'] ); ?>
				} );
			<?php endforeach; ?>
		} );
	}
	</script>
<!-- end .wrap --></div>
