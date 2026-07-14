<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<input type="number"
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo UWF_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'class', $class ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'value', $value ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'min', $min ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'max', $max ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'step', $step ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'placeholder', $placeholder ); ?>
/>
