<?php
/**
 * @package unomoon-form
 * @author websoudan
 * @license GPL-2.0+
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Unomoon_Form_Csrf
 */
class Unomoon_Form_Csrf {

	const KEY = 'unomoon-form-token';

	/**
	 * @var string
	 */
	private static $token;

	/**
	 * Validate.
	 *
	 * @param string $posted_token Posted token.
	 * @return boolean
	 */
	public static function validate( $posted_token ) {
		if ( ! preg_match( '|^[a-z0-9]+$|', $posted_token ) ) {
			return false;
		}

		$cookie_token = static::saved_token();
		return ! is_null( $cookie_token ) && ! is_null( $posted_token ) && hash_equals( $cookie_token, $posted_token );
	}

	/**
	 * Save token to the cookie.
	 */
	public static function save_token() {
		$saved_token   = static::saved_token();
		static::$token = ! $saved_token ? static::generate_token() : $saved_token;
		if ( ! $saved_token && ! headers_sent() ) {
			$secure = apply_filters( 'unomoonform_secure_cookie', is_ssl() );
			setcookie(
				static::KEY,
				static::$token,
				array(
					'expires'  => 0,
					'path'     => COOKIEPATH,
					'domain'   => COOKIE_DOMAIN,
					'secure'   => $secure,
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		}
	}

	/**
	 * Return set token.
	 *
	 * @return string
	 */
	public static function token() {
		return static::$token;
	}

	/**
	 * Return token saved in the cookie.
	 *
	 * @return string
	 */
	public static function saved_token() {
		if ( ! isset( $_COOKIE[ static::KEY ] ) ) {
			return null;
		}

		$token = sanitize_text_field( wp_unslash( $_COOKIE[ static::KEY ] ) );
		if ( ! preg_match( '|\A[a-z0-9]+\z|', $token ) ) {
			return null;
		}

		return $token;
	}

	/**
	 * Generate token.
	 * Only alphanumeric values are allowed for token values.
	 *
	 * @return string
	 */
	public static function generate_token() {
		if ( function_exists( 'random_bytes' ) ) {
			// phpcs:disable PHPCompatibility.FunctionUse.NewFunctions.random_bytesFound
			return bin2hex( random_bytes( 32 ) );
			// phpcs:enable
		}

		if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
			return bin2hex( openssl_random_pseudo_bytes( 32 ) );
		}

		return bin2hex( uniqid( mt_rand(), true ) );
	}
}
