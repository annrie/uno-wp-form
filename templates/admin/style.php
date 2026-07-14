<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<p>
	<select name="<?php echo UWF_Config::NAME; ?>[style]">
		<option value=""><?php esc_html_e( 'Select Style', 'uno-wp-form' ); ?></option>
		<?php foreach ( $styles as $style_key => $css ) : ?>
		<option value="<?php echo esc_attr( $style_key ); ?>" <?php selected( $style, $style_key ); ?>>
			<?php echo esc_html( $style_key ); ?>
		</option>
		<?php endforeach; ?>
	</select>
</p>
