<?php
/**
 * Integration Test Bootstrap
 *
 * Sets up the WordPress environment for integration testing.
 * This file is loaded before running integration tests.
 *
 * @package Tests\Integration
 */

// Define ABSPATH if not already defined.
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', '/var/www/html' );
}

// Define plugin file constant.
if ( ! defined( 'PLUGIN_FILE' ) ) {
	define( 'PLUGIN_FILE', __DIR__ . '/../../retrologin.php' );
}

// Define debug constants for testing.
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}

if ( ! defined( 'WP_DEBUG_LOG' ) ) {
	define( 'WP_DEBUG_LOG', true );
}

if ( ! defined( 'RETRORLOGIN_DEBUG' ) ) {
	define( 'RETRORLOGIN_DEBUG', true );
}

// Mock WordPress functions that are not available in PHPUnit environment.
if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $value ) {
		return trim( htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type, $gmt = false ) {
		return date( 'Y-m-d H:i:s' );
	}
}

if ( ! function_exists( 'wp_kses_normalize_entities' ) ) {
	function wp_kses_normalize_entities( $string ) {
		return $string;
	}
}

if ( ! function_exists( 'wp_kses_split' ) ) {
	function wp_kses_split( $string, $allowed_html, $allowed_protocols ) {
		return $string;
	}
}

// Load Composer autoloader.
require_once __DIR__ . '/../../vendor/autoload.php';
