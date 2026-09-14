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
	<?php Unomoon_Form_Functions::input_attribute( 'id', $id ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'maxlength', $maxlength ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'cols', $cols ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'rows', $rows ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'placeholder', $placeholder ); ?>
><?php echo esc_html( $value ); ?></textarea>
