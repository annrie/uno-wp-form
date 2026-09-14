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

<textarea
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'class', $class ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'maxlength', $maxlength ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'cols', $cols ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'rows', $rows ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'placeholder', $placeholder ); ?>
><?php echo esc_html( $value ); ?></textarea>
