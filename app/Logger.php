<?php

declare(strict_types=1);

/**
 * Structured Logger for RetroLogin Plugin
 *
 * Provides structured logging with severity levels, request ID tracking,
 * and contextual data. Logs are only output when RETRORLOGIN_DEBUG is enabled.
 *
 * Features:
 * - Severity levels (debug, info, warning, error)
 * - Request ID for distributed tracing
 * - Structured JSON context data
 * - Analytics instrumentation hooks
 */

namespace Retrologin;

use function bin2hex;
use function defined;
use function error_log;
use function random_bytes;
use function sprintf;
use function strtoupper;
use function wp_json_encode;
use function do_action;
use function apply_filters;

defined('ABSPATH') || exit;

/**
 * Logger class for structured logging.
 *
 * This class provides a singleton interface for logging messages with
 * different severity levels (debug, info, warning, error). Each log entry
 * includes a timestamp, log level, message, request ID, and optional context data.
 *
 * @example
 * // Basic logging
 * Logger::getInstance()->info('User logged in', ['user_id' => 123]);
 *
 * // Debug logging (only when RETRORLOGIN_DEBUG is true)
 * Logger::getInstance()->debug('Processing login request', ['ip' => $_SERVER['REMOTE_ADDR']]);
 *
 * // Access request ID for correlation
 * $requestId = Logger::getRequestId();
 */
class Logger
{
	/**
	 * Singleton instance.
	 */
	private static Logger|null $instance = null;

	/**
	 * Request ID for distributed tracing.
	 */
	private static string|null $requestId = null;

	/**
	 * Get the singleton instance.
	 *
	 * @return Logger The singleton instance.
	 */
	public static function getInstance(): Logger
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Get the current request ID.
	 *
	 * Generates a new request ID if one doesn't exist.
	 * Used for distributed tracing and log correlation.
	 *
	 * @return string The request ID.
	 */
	public static function getRequestId(): string
	{
		if (self::$requestId === null) {
			self::$requestId = self::generateRequestId();
		}

		return self::$requestId;
	}

	/**
	 * Generate a new request ID.
	 *
	 * @return string A random 16-character hex string.
	 */
	private static function generateRequestId(): string
	{
		return bin2hex(random_bytes(8));
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct()
	{
	}

	/**
	 * Log a debug message.
	 *
	 * Debug messages are used for detailed information useful during
	 * development and debugging. Only logged when RETRORLOGIN_DEBUG is true.
	 *
	 * @param string              $message The log message.
	 * @param array<string,mixed> $context Additional context data as key-value pairs.
	 */
	public function debug(string $message, array $context = []): void
	{
		$this->log('debug', $message, $context);
	}

	/**
	 * Log an informational message.
	 *
	 * Info messages are used for general operational information about
	 * the plugin's normal operation.
	 *
	 * @param string              $message The log message.
	 * @param array<string,mixed> $context Additional context data as key-value pairs.
	 */
	public function info(string $message, array $context = []): void
	{
		$this->log('info', $message, $context);
	}

	/**
	 * Log a warning message.
	 *
	 * Warning messages indicate potential issues that don't prevent
	 * the plugin from functioning but may need attention.
	 *
	 * @param string              $message The log message.
	 * @param array<string,mixed> $context Additional context data as key-value pairs.
	 */
	public function warning(string $message, array $context = []): void
	{
		$this->log('warning', $message, $context);
	}

	/**
	 * Log an error message.
	 *
	 * Error messages indicate failures that should be addressed.
	 * These are always logged regardless of debug settings.
	 *
	 * @param string              $message The log message.
	 * @param array<string,mixed> $context Additional context data as key-value pairs.
	 */
	public function error(string $message, array $context = []): void
	{
		$this->log('error', $message, $context);
	}

	/**
	 * Log an event for analytics tracking.
	 *
	 * This method logs events that can be used for product analytics.
	 * Events are only logged when analytics mode is enabled.
	 *
	 * @param string $event_name  The name of the event.
	 * @param array  $properties  Event properties (user_id, action, etc.).
	 */
	public function trackEvent(string $event_name, array $properties = []): void
	{
		/**
		 * Filter: retrologin_track_event
		 *
		 * Allows external services to hook into event tracking.
		 *
		 * @param string $event_name The name of the event.
		 * @param array  $properties The event properties.
		 */
		do_action('retrologin_track_event', $event_name, $properties);

		$context = array_merge(
			[
				'event' => $event_name,
				'request_id' => self::getRequestId(),
			],
			$properties
		);

		$this->log('info', "Event: {$event_name}", $context);
	}

	/**
	 * Internal log method.
	 *
	 * Handles the actual logging with structured format.
	 * Includes request ID for distributed tracing.
	 *
	 * @param string              $level   Log level (debug, info, warning, error).
	 * @param string              $message The log message.
	 * @param array<string,mixed> $context Additional context data.
	 */
	private function log(string $level, string $message, array $context): void
	{
		// Check if debug mode is enabled
		if (! $this->isDebugEnabled() && $level !== 'error') {
			return;
		}

		$timestamp   = current_time('mysql');
		$level       = strtoupper($level);
		$request_id  = self::getRequestId();

		// Build structured log entry with request ID
		$entry = sprintf(
			'[%s] [%s] [request_id:%s] RetroLogin: %s',
			$timestamp,
			$level,
			$request_id,
			$message,
		);

		// Add context data if present
		if (! empty($context)) {
			$contextJson = wp_json_encode($context);
			$entry       .= ' ' . $contextJson;
		}

		// Output to PHP error log
		error_log($entry);
	}

	/**
	 * Check if debug logging is enabled.
	 *
	 * @return bool True if debug mode is enabled.
	 */
	private function isDebugEnabled(): bool
	{
		// Check for constant defined by the plugin or WordPress
		if (defined('RETRORLOGIN_DEBUG') && RETRORLOGIN_DEBUG) {
			return true;
		}

		// Fall back to WordPress debug setting
		return defined('WP_DEBUG') && WP_DEBUG;
	}
}
