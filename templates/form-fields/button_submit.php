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

<button type="submit"
	name="<?php echo esc_attr( $name ); ?>"
	value="<?php echo esc_attr( $value ); ?>"
	<?php Unomoon_Form_Functions::input_attribute( 'class', $class ); ?>
><?php echo wp_kses_post( $element_content ); ?></button>
