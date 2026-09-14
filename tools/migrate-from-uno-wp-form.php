<?php
/**
 * One-off migration: Uno WP Form (<= 5.1.6.1) -> Unomoon Form (>= 5.1.6.2).
 *
 * Renames every database identifier that changed with the plugin rename.
 * Not shipped in the distributed zip; run it once with WP-CLI:
 *
 *   wp eval-file tools/migrate-from-uno-wp-form.php dry-run   # counts only
 *   wp eval-file tools/migrate-from-uno-wp-form.php           # apply
 *
 * Deactivate Uno WP Form (and Uno WP Form reCAPTCHA) before running, back up
 * the database, then activate Unomoon Form afterwards.
 *
 * @package unomoon-form
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	echo "This script must be run through WP-CLI (wp eval-file).\n";
	exit( 1 );
}

$dry_run = in_array( 'dry-run', $args, true );

global $wpdb;

$log = function ( $message ) {
	WP_CLI::log( $message );
};

$run = function ( $label, $count_sql, $update_sql, ...$params ) use ( $wpdb, $dry_run, $log ) {
	$n = (int) $wpdb->get_var( $params ? $wpdb->prepare( $count_sql, ...$params ) : $count_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	if ( $dry_run ) {
		$log( sprintf( '[dry-run] %-60s %d rows', $label, $n ) );
		return;
	}
	$affected = $wpdb->query( $params ? $wpdb->prepare( $update_sql, ...$params ) : $update_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$log( sprintf( '%-70s %d rows', $label, (int) $affected ) );
};

$like = function ( $string ) use ( $wpdb ) {
	return $wpdb->esc_like( $string );
};

$log( $dry_run ? '== Uno WP Form -> Unomoon Form migration (DRY RUN) ==' : '== Uno WP Form -> Unomoon Form migration ==' );

// 1. Post types.
$run(
	"posts.post_type 'uno-wp-form' -> 'unomoon-form'",
	"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = %s",
	"UPDATE {$wpdb->posts} SET post_type = 'unomoon-form' WHERE post_type = %s",
	'uno-wp-form'
);
// Only numeric inquiry post types (uwf_<form ID>) belong to the plugin; other CPTs starting with uwf_ are left alone.
$run(
	"posts.post_type 'uwf_N' -> 'unomoon_N'",
	"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type REGEXP %s",
	"UPDATE {$wpdb->posts} SET post_type = CONCAT( 'unomoon_', SUBSTRING( post_type, 5 ) ) WHERE post_type REGEXP %s",
	'^uwf_[0-9]+$'
);

// 2. Post meta keys.
$run(
	"postmeta '_uno-wp-form_data' -> '_unomoon-form_data'",
	"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s",
	"UPDATE {$wpdb->postmeta} SET meta_key = '_unomoon-form_data' WHERE meta_key = %s",
	'_uno-wp-form_data'
);
$run(
	"postmeta 'uno-wp-form' (form settings) -> 'unomoon-form'",
	"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s",
	"UPDATE {$wpdb->postmeta} SET meta_key = 'unomoon-form' WHERE meta_key = %s",
	'uno-wp-form'
);
// Only the plugin's own hidden key is renamed. Other uwf_* meta keys may belong to user-defined
// form fields ({uwf_email} etc.) or to other plugins and must stay as they are.
$run(
	"postmeta '_uwf_upload_files' -> '_unomoonform_upload_files'",
	"SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s",
	"UPDATE {$wpdb->postmeta} SET meta_key = '_unomoonform_upload_files' WHERE meta_key = %s",
	'_uwf_upload_files'
);

// 3. Options (plugin, chart settings, reCAPTCHA add-on).
$run(
	"options 'uno-wp-form' -> 'unomoon-form'",
	"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s",
	"UPDATE {$wpdb->options} SET option_name = 'unomoon-form' WHERE option_name = %s",
	'uno-wp-form'
);
$run(
	"options 'uno-wp-form-chart-uwf_N' -> 'unomoon-form-chart-unomoon_N'",
	"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name REGEXP %s",
	"UPDATE {$wpdb->options} SET option_name = CONCAT( 'unomoon-form-chart-unomoon_', SUBSTRING( option_name, 23 ) ) WHERE option_name REGEXP %s",
	'^uno-wp-form-chart-uwf_[0-9]+$'
);
$run(
	"options 'uno-wp-form-recaptcha-*' -> 'unomoon-form-recaptcha-*'",
	"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
	"UPDATE {$wpdb->options} SET option_name = CONCAT( 'unomoon-form-recaptcha-', SUBSTRING( option_name, 23 ) ) WHERE option_name LIKE %s",
	$like( 'uno-wp-form-recaptcha-' ) . '%'
);

// 4. Shortcodes in post content ([unoform...] / [/unoform...]).
$run(
	"posts.post_content '[unoform' -> '[unomoonform'",
	"SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_content LIKE %s OR post_content LIKE %s",
	"UPDATE {$wpdb->posts} SET post_content = REPLACE( REPLACE( post_content, '[unoform', '[unomoonform' ), '[/unoform', '[/unomoonform' ) WHERE post_content LIKE %s OR post_content LIKE %s",
	'%' . $like( '[unoform' ) . '%',
	'%' . $like( '[/unoform' ) . '%'
);

// 5. Shortcodes inside serialized form settings (complete message etc.). Done through the API to keep serialization valid.
$form_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( 'uno-wp-form', 'unomoon-form' )" );
$touched  = 0;
foreach ( $form_ids as $form_id ) {
	foreach ( array( 'unomoon-form', 'uno-wp-form' ) as $meta_key ) {
		$settings = get_post_meta( $form_id, $meta_key, true );
		if ( ! is_array( $settings ) ) {
			continue;
		}
		$replaced = map_deep(
			$settings,
			function ( $value ) {
				return is_string( $value ) ? str_replace( array( '[unoform', '[/unoform' ), array( '[unomoonform', '[/unomoonform' ), $value ) : $value;
			}
		);
		if ( $replaced !== $settings ) {
			$touched++;
			if ( ! $dry_run ) {
				update_post_meta( $form_id, $meta_key, wp_slash( $replaced ) );
			}
		}
	}
}
$log( sprintf( '%s%-60s %d rows', $dry_run ? '[dry-run] ' : '', 'form settings containing [unoform shortcodes', $touched ) );

// 5b. Shortcodes stored outside post_content: widgets (serialized options) and arbitrary post meta
//     (page builders, custom fields). Done through the API so serialized data stays valid.
$replace_shortcodes = function ( $value ) {
	return is_string( $value ) ? str_replace( array( '[unoform', '[/unoform' ), array( '[unomoonform', '[/unomoonform' ), $value ) : $value;
};

$widget_options = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'widget\\_%' AND option_value LIKE '%[unoform%'" );
$touched        = 0;
foreach ( $widget_options as $option_name ) {
	$option = get_option( $option_name );
	$new    = map_deep( $option, $replace_shortcodes );
	if ( $new !== $option ) {
		$touched++;
		if ( ! $dry_run ) {
			update_option( $option_name, $new );
		}
	}
}
$log( sprintf( '%s%-60s %d rows', $dry_run ? '[dry-run] ' : '', 'widget options containing [unoform shortcodes', $touched ) );

$meta_rows = $wpdb->get_results( "SELECT meta_id FROM {$wpdb->postmeta} WHERE meta_key NOT IN ( 'unomoon-form', 'uno-wp-form' ) AND meta_value LIKE '%[unoform%'" );
$touched   = 0;
foreach ( $meta_rows as $row ) {
	$meta = get_metadata_by_mid( 'post', $row->meta_id );
	if ( ! $meta ) {
		continue;
	}
	$new = map_deep( $meta->meta_value, $replace_shortcodes );
	if ( $new !== $meta->meta_value ) {
		$touched++;
		if ( ! $dry_run ) {
			update_metadata_by_mid( 'post', $row->meta_id, $new );
		}
	}
}
$log( sprintf( '%s%-60s %d rows', $dry_run ? '[dry-run] ' : '', 'other post meta containing [unoform shortcodes', $touched ) );

// 6. Per-user screen options that embed an inquiry post type (edit_uwf_N_per_page, manageedit-uwf_Ncolumnshidden,
//    closedpostboxes_uwf_N, ...). Only keys where uwf_ is followed by a form ID are touched.
$user_meta_keys = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT meta_key FROM {$wpdb->usermeta} WHERE meta_key LIKE %s", '%' . $like( 'uwf_' ) . '%' ) );
$touched        = 0;
foreach ( $user_meta_keys as $meta_key ) {
	if ( ! preg_match( '/(^|[_-])uwf_\d+/', $meta_key ) ) {
		continue;
	}
	$new_key = preg_replace( '/(^|[_-])uwf_(\d+)/', '$1unomoon_$2', $meta_key );
	if ( $dry_run ) {
		$touched += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->usermeta} WHERE meta_key = %s", $meta_key ) );
		continue;
	}
	$touched += (int) $wpdb->update( $wpdb->usermeta, array( 'meta_key' => $new_key ), array( 'meta_key' => $meta_key ) );
}
$log( sprintf( '%s%-60s %d rows', $dry_run ? '[dry-run] ' : '', 'usermeta screen options for uwf_N post types', $touched ) );

// 7. Stale transients and the old temporary upload directory.
// (Old form-session transients were keyed by the raw sha1 session ID and simply expire; only the deprecation-notice transient is named.)
$run(
	"transient 'unoform_deprecated_shortcodes_forms' (deleted)",
	"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
	"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
	$like( '_transient_unoform' ) . '%',
	$like( '_transient_timeout_unoform' ) . '%'
);

$upload_dir = wp_get_upload_dir();
$old_tmp    = path_join( $upload_dir['basedir'], 'uno-wp-form_uploads' );
if ( is_dir( $old_tmp ) ) {
	if ( $dry_run ) {
		$log( sprintf( '[dry-run] would delete temporary directory %s', $old_tmp ) );
	} else {
		// Local uploads path: use the direct transport regardless of the site's FS_METHOD, and verify the result.
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
		$direct = new WP_Filesystem_Direct( null );
		$direct->delete( $old_tmp, true );
		if ( is_dir( $old_tmp ) ) {
			WP_CLI::warning( sprintf( 'could not delete temporary directory %s (remove it manually)', $old_tmp ) );
		} else {
			$log( sprintf( 'deleted temporary directory %s', $old_tmp ) );
		}
	}
}

if ( ! $dry_run ) {
	wp_cache_flush();
	$log( 'Object cache flushed. Now activate Unomoon Form: wp plugin activate unomoon-form' );
}
