<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */
?>

<input type="file"
	name="<?php echo esc_attr( $name ); ?>"
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'id', $id ); ?>
	<?php echo Unomoon_Form_Functions::generate_input_attribute( 'class', $class ); ?>
/>
<span data-unomoonform-file-delete="<?php echo esc_attr( $name ); ?>" class="unomoonform-file-delete">&times;</span>
