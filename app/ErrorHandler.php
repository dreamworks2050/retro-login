<?php

declare(strict_types=1);

/**
 * Error Handler for RetroLogin Plugin
 *
 * Provides comprehensive error handling with:
 * - Error capturing and logging
 * - Request ID correlation for distributed tracing
 * - GitHub issue creation triggers
 * - Error-to-insight pipeline integration
 *
 * @package Retrologin
 */

namespace Retrologin;

use function add_action;
use function apply_filters;
use function defined;
use function do_action;
use function error_log;
use function error_reporting;
use function ini_set;
use function set_error_handler;
use function set_exception_handler;
use function register_shutdown_function;

/**
 * Error Handler Class
 *
 * Handles all errors, warnings, and exceptions in the plugin.
 * Integrates with the structured logger and error-to-issue pipeline.
 */
class ErrorHandler
{
	/**
	 * Singleton instance.
	 */
	private static ?self $instance = null;

	/**
	 * Request ID for correlation.
	 */
	private static ?string $requestId = null;

	/**
	 * Error count for tracking.
	 */
	private int $errorCount = 0;

	/**
	 * Warning count for tracking.
	 */
	private int $warningCount = 0;

	/**
	 * Whether error handling is initialized.
	 */
	private bool $initialized = false;

	/**
	 * Get the singleton instance.
	 */
	public static function getInstance(): self
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 */
	private function __construct()
	{
	}

	/**
	 * Initialize error handling.
	 */
	public function init(): void
	{
		if ($this->initialized) {
			return;
		}

		// Set error reporting
		error_reporting(E_ALL);

		// Set custom error handler
		set_error_handler([$this, 'handleError']);

		// Set custom exception handler
		set_exception_handler([$this, 'handleException']);

		// Register shutdown function for fatal errors
		register_shutdown_function([$this, 'handleShutdown']);

		// Add WordPress action hooks
		add_action('retrologin_log_error', [$this, 'processErrorForIssue'], 10, 3);

		$this->initialized = true;
	}

	/**
	 * Generate a unique request ID for correlation.
	 */
	public static function getRequestId(): string
	{
		if (self::$requestId === null) {
			self::$requestId = bin2hex(random_bytes(8));
		}

		return self::$requestId;
	}

	/**
	 * Handle PHP errors.
	 *
	 * @param int    $severity Error severity.
	 * @param string $message  Error message.
	 * @param string $file     File where error occurred.
	 * @param int    $line     Line number.
	 * @return bool Whether the error was handled.
	 */
	public function handleError(int $severity, string $message, string $file, int $line): bool
	{
		// Ignore warnings if WP_DEBUG is off
		if ((E_WARNING === $severity || E_NOTICE === $severity) && ! defined('WP_DEBUG') || ! WP_DEBUG) {
			return false;
		}

		// Ignore strict standards in production
		if (E_STRICT === $severity && ! defined('WP_DEBUG') || ! WP_DEBUG) {
			return false;
		}

		$errorTypes = [
			E_ERROR => 'Error',
			E_WARNING => 'Warning',
			E_PARSE => 'Parse Error',
			E_NOTICE => 'Notice',
			E_CORE_ERROR => 'Core Error',
			E_CORE_WARNING => 'Core Warning',
			E_COMPILE_ERROR => 'Compile Error',
			E_COMPILE_WARNING => 'Compile Warning',
			E_USER_ERROR => 'User Error',
			E_USER_WARNING => 'User Warning',
			E_USER_NOTICE => 'User Notice',
			E_STRICT => 'Strict Notice',
			E_RECOVERABLE_ERROR => 'Recoverable Error',
			E_DEPRECATED => 'Deprecated',
			E_USER_DEPRECATED => 'User Deprecated',
		];

		$type = $errorTypes[$severity] ?? 'Unknown Error';

		// Log the error
		$this->logError($type, $message, $file, $line);

		// Check if we should trigger issue creation
		if ($this->shouldCreateIssue($severity)) {
			$this->triggerIssueCreation($type, $message, $file, $line);
		}

		// Return false to let PHP's default error handler run as well
		return false;
	}

	/**
	 * Handle uncaught exceptions.
	 *
	 * @param \Throwable $exception The exception.
	 */
	public function handleException(\Throwable $exception): void
	{
		$message = $exception->getMessage();
		$file = $exception->getFile();
		$line = $exception->getLine();
		$trace = $exception->getTraceAsString();

		// Log the exception
		$this->logError('Exception', $message . ' in ' . $file . ':' . $line, $file, $line);

		// Trigger issue creation for exceptions
		$this->triggerIssueCreation('Exception', $message, $file, $line, $trace);

		// Log to error_log for WordPress
		error_log('[RetroLogin Exception] ' . $message);
	}

	/**
	 * Handle shutdown to catch fatal errors.
	 */
	public function handleShutdown(): void
	{
		$error = error_get_last();

		if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
			$this->logError('Fatal Error', $error['message'], $error['file'], $error['line']);
			$this->triggerIssueCreation('Fatal Error', $error['message'], $error['file'], $error['line']);
		}
	}

	/**
	 * Log an error to the structured logger.
	 *
	 * @param string $type    Error type.
	 * @param string $message Error message.
	 * @param string $file    File.
	 * @param int    $line    Line number.
	 */
	private function logError(string $type, string $message, string $file, int $line): void
	{
		// Use the Logger if available
		if (class_exists(Logger::class)) {
			$logger = Logger::getInstance();
			$logger->error($message, [
				'error_type' => $type,
				'file' => $file,
				'line' => $line,
				'request_id' => self::getRequestId(),
			]);
		} else {
			// Fallback to error_log
			error_log(sprintf(
				'[RetroLogin Error] [%s] %s in %s:%d [request_id:%s]',
				$type,
				$message,
				$file,
				$line,
				self::getRequestId()
			));
		}

		// Update counters
		if (stripos($type, 'warning') !== false) {
			$this->warningCount++;
		} else {
			$this->errorCount++;
		}
	}

	/**
	 * Determine if an issue should be created based on severity.
	 *
	 * @param int $severity Error severity.
	 * @return bool Whether to create an issue.
	 */
	private function shouldCreateIssue(int $severity): bool
	{
		// Create issues for these error types
		$criticalErrors = [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];

		return in_array($severity, $criticalErrors, true);
	}

	/**
	 * Trigger GitHub issue creation via action hook.
	 *
	 * @param string $type    Error type.
	 * @param string $message Error message.
	 * @param string $file    File.
	 * @param int    $line    Line number.
	 * @param string $trace   Stack trace (optional).
	 */
	private function triggerIssueCreation(string $type, string $message, string $file, int $line, string $trace = ''): void
	{
		/**
		 * Action: retrologin_log_error
		 *
		 * Fires when an error occurs that may need issue creation.
		 *
		 * @param string $type    Error type.
		 * @param string $message Error message.
		 * @param array  $context Error context (file, line, request_id).
		 */
		do_action('retrologin_log_error', $type, $message, [
			'file' => $file,
			'line' => $line,
			'request_id' => self::getRequestId(),
			'trace' => $trace,
		]);
	}

	/**
	 * Process error for GitHub issue creation.
	 *
	 * This method is called by the retrologin_log_error action.
	 *
	 * @param string $type    Error type.
	 * @param string $message Error message.
	 * @param array  $context Error context.
	 */
	public function processErrorForIssue(string $type, string $message, array $context = []): void
	{
		// Check if we should skip issue creation
		$skip = apply_filters('retrologin_error_skip_issue', false, $type, $message, $context);
		if ($skip) {
			return;
		}

		// Limit issue creation to avoid spam
		if ($this->errorCount > 10) {
			return;
		}

		// The actual issue creation is handled by the GitHub Action workflow
		// This method provides a hook point for custom processing
	}

	/**
	 * Get error statistics.
	 *
	 * @return array Error statistics.
	 */
	public function getStats(): array
	{
		return [
			'errors' => $this->errorCount,
			'warnings' => $this->warningCount,
			'request_id' => self::getRequestId(),
		];
	}

	/**
	 * Reset error counts (useful for testing).
	 */
	public function resetStats(): void
	{
		$this->errorCount = 0;
		$this->warningCount = 0;
	}
}
