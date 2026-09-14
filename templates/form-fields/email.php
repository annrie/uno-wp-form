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

<input type="email"
	name="<?php echo esc_attr( $name ); ?>"
	<?php Unomoon_Form_Functions::input_attribute( 'id', $id ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'size', $size ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'maxlength', $maxlength ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'value', $value ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'placeholder', $placeholder ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'data-conv-half-alphanumeric', $conv_half_alphanumeric ); ?>
/>
