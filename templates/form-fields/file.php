<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<input type="file"
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo UWF_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'class', $class ); ?>
/>
<span data-unoform-file-delete="<?php echo esc_attr( $name ); ?>" class="unoform-file-delete">&times;</span>
