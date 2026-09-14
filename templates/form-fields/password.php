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

<input type="password"
	name="<?php echo esc_attr( $name ); ?>"
	<?php Unomoon_Form_Functions::input_attribute( 'id', $id ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'size', $size ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'maxlength', $maxlength ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'value', $value ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'placeholder', $placeholder ); ?>
/>
