<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

do_action( 'unoform_tag_generator_dialog' );

$types = array(
	'input'        => 'input',
	'select'       => 'select',
	'button'       => 'button',
	'input_button' => 'input_button',
	'error'        => 'error',
	'other'        => 'other',
);
$group = apply_filters( 'unoform_tag_generator_group', $types );

$labels = array(
	'input'        => __( 'Input fields', 'uno-wp-form' ),
	'select'       => __( 'Select fields', 'uno-wp-form' ),
	'button'       => __( 'Button fields (button)', 'uno-wp-form' ),
	'input_button' => __( 'Button fields (input)', 'uno-wp-form' ),
	'error'        => __( 'Error fields', 'uno-wp-form' ),
	'other'        => __( 'Other fields', 'uno-wp-form' ),
);
$labels = apply_filters( 'unoform_tag_generator_labels', $labels );
?>
<div class="add-unoform-btn">
	<select>
		<option value=""><?php echo esc_html_e( 'Select this.', 'uno-wp-form' ); ?></option>
		<?php foreach ( $group as $type ) : ?>
			<?php
			$label = isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
			$tag   = 'other' === $type ? 'unoform_tag_generator_option' : 'unoform_tag_generator_' . $type . '_option';
			?>
			<optgroup label="<?php echo esc_attr( $label ); ?>">
				<?php do_action( $tag ); ?>
			</optgroup>
		<?php endforeach; ?>
	</select>
	<span class="button"><?php esc_html_e( 'Add form tag', 'uno-wp-form' ); ?></span>
</div>
