<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<textarea
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo UWF_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'class', $class ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'maxlength', $maxlength ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'cols', $cols ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'rows', $rows ); ?>
	<?php echo UWF_Functions::generate_input_attribute( 'placeholder', $placeholder ); ?>
><?php echo esc_html( $value ); ?></textarea>
