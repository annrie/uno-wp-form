<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<input type="button"
	name="<?php echo esc_attr( $name ); ?>"
	value="<?php echo esc_attr( $value ); ?>"
	<?php echo UWF_Functions::generate_input_attribute( 'class', $class ); ?>
/>
