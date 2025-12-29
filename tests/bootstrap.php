<?php
/**
 * PHPUnit bootstrap file for RetroLogin plugin tests.
 *
 * This loads the Composer autoloader for running unit tests without WordPress.
 * For WordPress integration tests, you would need wp-tests-lib.
 */

declare(strict_types=1);

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define plugin constants for tests
if (!defined('PLUGIN_FILE')) {
	define('PLUGIN_FILE', dirname(__DIR__) . '/retrologin.php');
}

if (!defined('ABSPATH')) {
	define('ABSPATH', '/tmp/wordpress/');
}

// Mock WordPress functions that plugin classes might use
// This is a simple mock - for real tests, use wp-tests-lib or Brain\Monkey
if (!function_exists('load_plugin_textdomain')) {
	function load_plugin_textdomain() {
		return true;
	}
}

if (!function_exists('plugin_basename')) {
	function plugin_basename($file) {
		return basename(dirname($file)) . '/' . basename($file);
	}
}

if (!function_exists('register_activation_hook')) {
	function register_activation_hook($file, $callback) {
		// Mock for testing
	}
}

if (!function_exists('register_deactivation_hook')) {
	function register_deactivation_hook($file, $callback) {
		// Mock for testing
	}
}

if (!function_exists('add_action')) {
	function add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1) {
		// Mock for testing
	}
}

if (!function_exists('esc_html__')) {
	function esc_html__(string $text, string $domain = 'default') {
		return $text;
	}
}

if (!function_exists('wp_unslash')) {
	function wp_unslash($value) {
		return $value;
	}
}

if (!function_exists('sanitize_text_field')) {
	function sanitize_text_field($value) {
		return $value;
	}
}

if (!function_exists('current_time')) {
	function current_time($type, $gmt = false) {
		return date('Y-m-d H:i:s');
	}
}

if (!function_exists('wp_json_encode')) {
	function wp_json_encode($data, $options = 0, $depth = 512) {
		return json_encode($data, $options, $depth);
	}
}

echo "PHPUnit Bootstrap Loaded\n";
