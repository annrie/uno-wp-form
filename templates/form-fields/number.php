<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<input type="number"
	name="<?php echo esc_attr( $name ); ?>"
	<?php Unomoon_Form_Functions::input_attribute( 'id', $id ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'value', $value ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'min', $min ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'max', $max ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'step', $step ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'placeholder', $placeholder ); ?>
/>
