<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<input type="range"
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'class', $class ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'value', $value ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'min', $min ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'max', $max ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'step', $step ); ?>
/>
