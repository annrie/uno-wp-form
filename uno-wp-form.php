<?php
/**
 * Plugin Name: Uno WP Form
 * Plugin URI: https://github.com/annrie/uno-wp-form
 * Description: A WordPress 7 compatible fork of MW WP Form using uno-wp-form identifiers.
 * Version: 5.1.4-uno.1
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Tested up to: 7.0
 * Author: annrie
 * Author URI: https://phantomoon.com
 * Original Author: inc2734
 * Original Author URI: https://2inc.org
 * Text Domain: uno-wp-form
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package uno-wp-form
 * @author annrie
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'UNO_WP_FORM_PLUGIN_FILE', __FILE__ );
define( 'UNO_WP_FORM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UNO_WP_FORM_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Include files.
 */
include_once( UNO_WP_FORM_PLUGIN_DIR . 'classes/functions.php' );
include_once( UNO_WP_FORM_PLUGIN_DIR . 'classes/config.php' );
include_once( UNO_WP_FORM_PLUGIN_DIR . 'classes/deprecated.php' );

class Uno_WP_Form {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, '_load_initialize_files' ), 9 );
		add_action( 'plugins_loaded', array( $this, '_initialize' ), 11 );

		register_uninstall_hook( __FILE__, array( __CLASS__, '_uninstall' ) );
	}

	/**
	 * Load classes.
	 */
	public function _load_initialize_files() {
		$plugin_dir_path = UNO_WP_FORM_PLUGIN_DIR;
		$includes        = array(
			'/classes/abstract',
			'/classes/controllers',
			'/classes/models',
			'/classes/services',
			'/classes/validation-rules',
			'/classes/form-fields',
		);
		foreach ( $includes as $include ) {
			foreach ( glob( $plugin_dir_path . $include . '/*.php' ) as $file ) {
				require_once( $file );
			}
		}
	}

	/**
	 * Load text domain, The starting point of the process.
	 */
	public function _initialize() {
		load_plugin_textdomain(
			'uno-wp-form',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);

		Uno_WP_Form_Csrf::save_token();

		add_action( 'after_setup_theme', array( $this, '_after_setup_theme' ), 11 );
		add_action( 'init', array( $this, '_register_post_type' ) );
		add_action( 'template_redirect', array( $this, '_do_empty_temp_dir' ) );
	}

	/**
	 * Initialize each screens.
	 */
	public function _after_setup_theme() {
		if ( current_user_can( UWF_Config::CAPABILITY ) && is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, '_admin_enqueue_scripts' ) );
			add_action( 'admin_menu', array( $this, '_admin_menu_for_chart' ) );
			add_action( 'admin_menu', array( $this, '_admin_menu_for_inquiry_data_list' ) );
			add_action( 'current_screen', array( $this, '_current_screen' ) );
			new Uno_WP_Form_Deprecation_Notice_Controller();
		} elseif ( ! is_admin() ) {
			new Uno_WP_Form_Main_Controller();
		}
	}

	/**
	 * Enqueue assets.
	 */
	public function _admin_enqueue_scripts() {
		$url = UNO_WP_FORM_PLUGIN_URL;
		wp_enqueue_style( UWF_Config::NAME . '-admin-common', $url . '/css/admin-common.css' );
	}

	/**
	 * Add admin menu for chart.
	 */
	public function _admin_menu_for_chart() {
		$contact_data_post_types = Uno_WP_Form_Contact_Data_Setting::get_form_post_types();
		if ( empty( $contact_data_post_types ) ) {
			return;
		}

		add_submenu_page(
			'edit.php?post_type=' . UWF_Config::NAME,
			esc_html__( 'Chart', 'uno-wp-form' ),
			esc_html__( 'Chart', 'uno-wp-form' ),
			UWF_Config::CAPABILITY,
			UWF_Config::NAME . '-chart',
			'__return_false'
		);
	}

	/**
	 * Add admin menu for saved inquiry data.
	 */
	public function _admin_menu_for_inquiry_data_list() {
		$contact_data_post_types = Uno_WP_Form_Contact_Data_Setting::get_form_post_types();
		if ( empty( $contact_data_post_types ) ) {
			return;
		}

		add_submenu_page(
			'edit.php?post_type=' . UWF_Config::NAME,
			__( 'Inquiry data', 'uno-wp-form' ),
			__( 'Inquiry data', 'uno-wp-form' ),
			UWF_Config::CAPABILITY,
			UWF_Config::NAME . '-save-data',
			'__return_false'
		);
	}

	/**
	 * Front controller.
	 *
	 * @param WP_Screen $screen WP_Screen object.
	 */
	public function _current_screen( $screen ) {
		if ( UWF_Config::NAME === $screen->id ) {
			new Uno_WP_Form_Admin_Controller();
		} elseif ( 'edit-' . UWF_Config::NAME === $screen->id ) {
			new Uno_WP_Form_Admin_List_Controller();
		} elseif ( UWF_Functions::is_contact_data_post_type( $screen->id ) ) {
			new Uno_WP_Form_Contact_Data_Controller();
		} elseif ( preg_match( '/^edit-' . UWF_Config::DBDATA . '\d+$/', $screen->id ) ) {
			new Uno_WP_Form_Contact_Data_List_Controller();
		} elseif ( UWF_Config::NAME . '_page_' . UWF_Config::NAME . '-chart' === $screen->id ) {
			new Uno_WP_Form_Chart_Controller();
		} elseif ( UWF_Config::NAME . '_page_' . UWF_Config::NAME . '-save-data' === $screen->id ) {
			new Uno_WP_Form_Stores_Inquiry_Data_Form_List_Controller();
		}
	}

	/**
	 * Register post types for Uno WP Form and inquiry data.
	 */
	public function _register_post_type() {
		if ( ! current_user_can( UWF_Config::CAPABILITY ) && is_admin() ) {
			return;
		}

		// Existing Uno WP Form form settings are kept under the original post type.
		register_post_type(
			UWF_Config::NAME,
			array(
				'label'           => 'Uno WP Form',
				'labels'          => array(
					'name'               => 'Uno WP Form',
					'singular_name'      => 'Uno WP Form',
					'add_new_item'       => __( 'Add New Form', 'uno-wp-form' ),
					'edit_item'          => __( 'Edit Form', 'uno-wp-form' ),
					'new_item'           => __( 'New Form', 'uno-wp-form' ),
					'view_item'          => __( 'View Form', 'uno-wp-form' ),
					'search_items'       => __( 'Search Forms', 'uno-wp-form' ),
					'not_found'          => __( 'No Forms found', 'uno-wp-form' ),
					'not_found_in_trash' => __( 'No Forms found in Trash', 'uno-wp-form' ),
				),
				'capability_type' => 'page',
				'public'          => false,
				'show_ui'         => true,
			)
		);

		$admin = new Uno_WP_Form_Admin();
		$forms = $admin->get_forms_using_database();
		foreach ( $forms as $form ) {
			$post_type = UWF_Functions::get_contact_data_post_type_from_form_id( $form->ID );
			register_post_type(
				$post_type,
				array(
					'label'           => $form->post_title,
					'labels'          => array(
						'name'               => $form->post_title,
						'singular_name'      => $form->post_title,
						'edit_item'          => __( 'Edit ', 'uno-wp-form' ) . ':' . $form->post_title,
						'view_item'          => __( 'View', 'uno-wp-form' ) . ':' . $form->post_title,
						'search_items'       => __( 'Search', 'uno-wp-form' ) . ':' . $form->post_title,
						'not_found'          => __( 'No data found', 'uno-wp-form' ),
						'not_found_in_trash' => __( 'No data found in Trash', 'uno-wp-form' ),
					),
					'capability_type' => 'page',
					'public'          => false,
					'show_ui'         => true,
					'show_in_menu'    => false,
					'supports'        => array( 'title' ),
				)
			);
		}
	}

	/**
	 * Uninstall processes.
	 */
	public static function _uninstall() {
		$plugin_dir_path = UNO_WP_FORM_PLUGIN_DIR;
		include_once( $plugin_dir_path . 'classes/models/class.admin.php' );
		include_once( $plugin_dir_path . 'classes/models/class.file.php' );
		include_once( $plugin_dir_path . 'classes/models/class.directory.php' );

		$admin = new Uno_WP_Form_Admin();
		$forms = $admin->get_forms();

		$data_post_ids = array();
		foreach ( $forms as $form ) {
			$data_post_ids[] = $form->ID;
			wp_delete_post( $form->ID, true );
		}

		foreach ( $data_post_ids as $data_post_id ) {
			delete_option( UWF_Config::NAME . '-chart-' . $data_post_id );

			$data_posts = get_posts(
				array(
					'post_type'      => UWF_Functions::get_contact_data_post_type_from_form_id( $data_post_id ),
					'posts_per_page' => -1,
				)
			);
			if ( empty( $data_posts ) ) {
				continue;
			}

			foreach ( $data_posts as $data_post ) {
				wp_delete_post( $data_post->ID, true );
			}
		}

		try {
			Uno_WP_Form_Directory::do_empty( Uno_WP_Form_Directory::get(), true );
			Uno_WP_Form_Directory::remove( Uno_WP_Form_Directory::get( false ) );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}

		delete_option( UWF_Config::NAME );
	}

	public function _do_empty_temp_dir() {
		try {
			Uno_WP_Form_Directory::do_empty( Uno_WP_Form_Directory::get() );
		} catch ( \Exception $e ) {
			error_log( $e->getMessage() );
		}
	}
}

new Uno_WP_Form();
