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

<select name="<?php echo esc_attr( $name ); ?>"
	<?php Unomoon_Form_Functions::input_attribute( 'id', $id ); ?>
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
>
	<?php foreach ( $children as $option_value => $option_label ) : ?>
		<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $value, true ); ?>>
			<?php echo esc_html( $option_label ); ?>
		</option>
	<?php endforeach; ?>
</select>
