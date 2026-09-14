<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

/**
 * Unomoon_Form_Config
 */
class Unomoon_Form_Config {

	/**
	 * Plugin ID.
	 *
	 * @var string
	 */
	const NAME = 'unomoon-form';

	/**
	 * Text Domain.
	 *
	 * @var string
	 */
	const DOMAIN = 'unomoon-form';

	/**
	 * Prefix of post type of saved inquiry data.
	 *
	 * @var string
	 */
	const DBDATA = 'unomoonform_';

	/**
	 * The name of field that array of uploaded file names.
	 *
	 * @var string
	 */
	const UPLOAD_FILE_KEYS = 'unomoonform_upload_files';

	/**
	 * The name of field that array of custom mail tag names.
	 *
	 * @var string
	 */
	const CUSTOM_MAIL_TAG_KEYS = 'unomoonform_custom_mail_tags';

	/**
	 * $_FILES.
	 *
	 * @var string
	 */
	const UPLOAD_FILES = 'unomoonform_files';

	/**
	 * Field name of Akismet.
	 *
	 * @var string
	 */
	const AKISMET = 'unomoonform_akismet';

	/**
	 * Capability.
	 *
	 * @var string
	 */
	const CAPABILITY = 'edit_pages';

	/**
	 * Name of tracking number.
	 *
	 * @var string
	 */
	const TRACKINGNUMBER = 'tracking_number';

	/**
	 * Field name of confirm button.
	 *
	 * @var string
	 */
	const CONFIRM_BUTTON = 'submitConfirm';

	/**
	 * Field name of back button.
	 *
	 * @var string
	 */
	const BACK_BUTTON = 'submitBack';

	/**
	 * Name of meta data of saved inquiry data.
	 *
	 * @var string
	 */
	const CONTACT_DATA_NAME = '_unomoon-form_data';

	/**
	 * Name of meta data of saved inquiry data.
	 *
	 * @var string
	 */
	const INQUIRY_DATA_NAME = self::CONTACT_DATA_NAME;

	/**
	 * Name of sending error data.
	 *
	 * @var string
	 */
	const SEND_ERROR = 'unomoon-form-send-error';

	/**
	 * Nonce field name.
	 *
	 * @var string
	 */
	const TOKEN_NAME = 'unomoon_form_token';
}
