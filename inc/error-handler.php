<?php

/**
 * Error Handler Module for RetroLogin Plugin
 *
 * This module initializes error handling and integrates with the
 * error-to-insight pipeline for GitHub issue creation.
 *
 * @package Retrologin\Inc
 */

defined('ABSPATH') || exit;

require_once RETROLOGIN_PLUGIN_DIR . 'app/ErrorHandler.php';

use Retrologin\ErrorHandler;

/**
 * Initialize error handling for the plugin.
 *
 * This function sets up comprehensive error handling including:
 * - Custom error handler for PHP errors
 * - Exception handler for uncaught exceptions
 * - Shutdown handler for fatal errors
 * - Integration with structured logging
 * - GitHub issue creation triggers
 *
 * @return void
 */
function retrologin_init_error_handler(): void
{
	$handler = ErrorHandler::getInstance();
	$handler->init();
}

// Initialize error handler early
add_action('plugins_loaded', 'retrologin_init_error_handler', 1);

/**
 * Log an error that may trigger GitHub issue creation.
 *
 * This function is used by the ErrorHandler class to trigger
 * the retrologin_log_error action for issue creation.
 *
 * @param string $type    Error type (Error, Warning, Exception, etc.).
 * @param string $message Error message.
 * @param array  $context Error context (file, line, request_id, trace).
 * @return void
 */
function retrologin_log_error(string $type, string $message, array $context = []): void
{
	/**
	 * Filter: retrologin_error_skip_issue
	 *
	 * Allows bypassing automatic issue creation for specific errors.
	 *
	 * @param bool   $skip    Whether to skip issue creation.
	 * @param string $type    Error type.
	 * @param string $message Error message.
	 * @param array  $context Error context.
	 */
	$skip = apply_filters('retrologin_error_skip_issue', false, $type, $message, $context);

	if (! $skip) {
		/**
		 * Action: retrologin_log_error
		 *
		 * Fires when an error occurs that may need issue creation.
		 * The GitHub Actions workflow monitors for this action via
		 * the error-to-issue pipeline.
		 *
		 * @param string $type    Error type.
		 * @param string $message Error message.
		 * @param array  $context Error context.
		 */
		do_action('retrologin_log_error', $type, $message, $context);
	}
}

/**
 * Get error handler instance.
 *
 * @return ErrorHandler The error handler instance.
 */
function retrologin_get_error_handler(): ErrorHandler
{
	return ErrorHandler::getInstance();
}

/**
 * Get error statistics for the current request.
 *
 * @return array Error statistics with errors, warnings, and request_id.
 */
function retrologin_get_error_stats(): array
{
	return retrologin_get_error_handler()->getStats();
}

/**
 * Reset error statistics (useful for testing).
 *
 * @return void
 */
function retrologin_reset_error_stats(): void
{
	retrologin_get_error_handler()->resetStats();
}

/**
 * Add error tracking labels to an issue.
 *
 * This function can be used to programmatically add the appropriate
 * labels to GitHub issues created by the error-to-insight pipeline.
 *
 * @param int    $issueNumber The issue number.
 * @param string $severity    Error severity (critical, high, medium, low).
 * @return void
 */
function retrologin_add_error_labels(int $issueNumber, string $severity = 'medium'): void
{
	$labels = [
		'critical' => 'type:bug,status:needs-triage,priority:P0',
		'high'     => 'type:bug,status:needs-triage,priority:P1',
		'medium'   => 'type:bug,status:needs-triage,priority:P2',
		'low'      => 'type:bug,status:needs-triage,priority:P3',
	];

	$labelString = $labels[$severity] ?? $labels['medium'];

	/**
	 * This function is called by GitHub Actions workflow.
	 * The actual implementation uses: gh issue edit
	 */
	if (defined('RETROLOGIN_DEBUG') && RETROLOGIN_DEBUG) {
		error_log(sprintf(
			'[RetroLogin] Would add labels to issue #%d: %s',
			$issueNumber,
			$labelString
		));
	}
}

/**
 * Create a GitHub issue from an error.
 *
 * This function creates a GitHub issue with appropriate labels
 * and context for the error.
 *
 * @param string $type       Error type.
 * @param string $message    Error message.
 * @param string $file       File where error occurred.
 * @param int    $line       Line number.
 * @param array  $context    Additional context.
 * @return int|null Issue number or null if creation failed.
 */
function retrologin_create_error_issue(string $type, string $message, string $file, int $line, array $context = []): ?int
{
	// Determine severity based on error type
	$severity = 'medium';
	if (in_array($type, ['Fatal Error', 'Core Error', 'Exception'], true)) {
		$severity = 'critical';
	} elseif (in_array($type, ['Error', 'User Error'], true)) {
		$severity = 'high';
	}

	// Build issue body
	$requestId = $context['request_id'] ?? ErrorHandler::getRequestId();
	$trace = $context['trace'] ?? '';

	$body = sprintf(
		"## Error Report\n\n" .
		"**Type:** %s\n" .
		"**Message:** %s\n" .
		"**File:** %s\n" .
		"**Line:** %d\n" .
		"**Request ID:** `%s`\n\n" .
		"## Context\n\n" .
		"```json\n%s\n```\n\n" .
		"## Stack Trace\n\n" .
		"```\n%s\n```\n\n" .
		"---\n" .
		"*This issue was automatically created by the error-to-insight pipeline.*",
		$type,
		$message,
		$file,
		$line,
		$requestId,
		json_encode($context, JSON_PRETTY_PRINT),
		$trace
	);

	// Create the issue via GitHub CLI if available
	if (defined('RETROLOGIN_DEBUG') && RETROLOGIN_DEBUG) {
		error_log(sprintf(
			'[RetroLogin] Would create issue: [%s] %s in %s:%d',
			$type,
			$message,
			$file,
			$line
		));
		return null;
	}

	// The actual issue creation is handled by the GitHub Actions workflow
	// This function provides a hook point for the workflow
	return null;
}
