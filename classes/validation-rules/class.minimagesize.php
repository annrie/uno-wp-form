<?php
/**
 * @package uno-wp-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Uno_WP_Form_Validation_Rule_MinImageSize
 */
class Uno_WP_Form_Validation_Rule_MinImageSize extends Uno_WP_Form_Abstract_Validation_Rule {

	/**
	 * Validation rule name.
	 *
	 * @var string
	 */
	protected $name = 'minfilesize';

	/**
	 * Validation process.
	 *
	 * @param string $name    Validation name.
	 * @param array  $options Validation options.
	 * @return string
	 */
	public function rule( $name, array $options = array() ) {
		$value = $this->Data->get( $name );

		if ( ! $value ) {
			return;
		}

		if ( ! UWF_Functions::is_numeric( $options['width'] ) || ! UWF_Functions::is_numeric( $options['width'] ) ) {
			return;
		}

		$upload_file_keys = $this->Data->get_post_value_by_key( UWF_Config::UPLOAD_FILE_KEYS );
		$upload_files     = $this->Data->get_post_value_by_key( UWF_Config::UPLOAD_FILES );
		$is_error         = false;

		if ( ! is_array( $upload_file_keys ) ) {
			$upload_file_keys = array();
		}

		if ( ! is_array( $upload_files ) ) {
			$upload_files = array();
		}

		if ( ! in_array( $name, $upload_file_keys, true ) && array_key_exists( $name, $upload_files ) ) {
			// Check after upload
			$filepath = $upload_files[ $name ]['tmp_name'];
		} else {
			// Check if uploaded
			$form_id  = UWF_Functions::get_form_id_from_form_key( $this->Data->get_form_key() );
			$filepath = Uno_WP_Form_Directory::generate_user_filepath( $form_id, $name, $value );
		}

		if ( file_exists( $filepath ) && exif_imagetype( $filepath ) ) {
			$imagesize = getimagesize( $filepath );
		} else {
			if ( ! in_array( $name, $upload_file_keys, true ) ) {
				$is_error = true;
			}
		}

		$defaults = array(
			'width'   => 1,
			'height'  => 1,
			'message' => __( 'This image size is too small.', 'uno-wp-form' ),
		);
		$options  = array_merge( $defaults, $options );
		if ( $is_error || $imagesize[0] < $options['width'] || $imagesize[1] < $options['height'] ) {
			return $options['message'];
		}
	}

	/**
	 * Add setting field to validation rule setting panel.
	 *
	 * @param numeric $key ID of validation rule.
	 * @param array   $value Content of validation rule.
	 * @return void
	 */
	public function admin( $key, $value ) {
		$width  = '';
		$height = '';
		if ( is_array( $value[ $this->get_name() ] ) ) {
			if ( isset( $value[ $this->get_name() ]['width'] ) ) {
				$width = $value[ $this->get_name() ]['width'];
			}
			if ( isset( $value[ $this->get_name() ]['height'] ) ) {
				$height = $value[ $this->get_name() ]['height'];
			}
		}
		?>
		<table>
			<tr>
				<td><?php esc_html_e( 'Minimum image size', 'uno-wp-form' ); ?></td>
				<td>
					<input type="text" value="<?php echo esc_attr( $width ); ?>" size="4" name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>][width]" />
					&times;
					<input type="text" value="<?php echo esc_attr( $height ); ?>" size="4" name="<?php echo UWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->get_name() ); ?>][height]" />
				</td>
			</tr>
		</table>
		<?php
	}
}
