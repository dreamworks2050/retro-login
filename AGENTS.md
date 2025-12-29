# RetroLogin WordPress Plugin

## Build & Test

```bash
# Install dependencies
composer install && npm install

# Run tests
composer test             # PHPUnit unit tests
composer test:coverage    # Generate HTML coverage report

# Verification (run before commit)
composer run verify        # PHP linting (Syntatis standard)
npm run lint:js           # JavaScript linting (ESLint)
# Note: npm run lint:css targets .scss files only

# Fix linting issues
composer run format        # PHP auto-fix
npm run format            # JS auto-fix

# Build assets
npm run build            # Production build (minified)
npm run start            # Dev mode with watch

# Translations
composer run make-pot    # Generate POT file
```

## Environment Setup

For local development:
1. Copy `.env.example` to `.env`
2. Update `WP_DEBUG=true` for development mode
3. Set `RETROLOGIN_DEBUG=false` (or `true` for verbose plugin logging)

See `.env.example` for all available configuration options.

## Code Verification

Run verification before any commit:
```bash
composer run verify        # PHP linting
composer run phpstan      # Static analysis (PHPStan level 5)
npm run lint:js           # JavaScript linting
npm run format            # Auto-fix JS issues
composer run format       # Auto-fix PHP issues
```

All linters pass:
- PHP: 8/8 checks pass (Syntatis coding standard)
- JavaScript: No errors (ESLint via wp-scripts)

## Error Handling & Debugging

The plugin includes a comprehensive error handling system with structured logging and error-to-insight pipeline integration.

### Structured Logging

```php
// Use the Logger for structured logging
$logger = \Retrologin\Logger::getInstance();

$logger->debug('Debug message', ['key' => 'value']);
$logger->info('Info message', ['context' => 'test']);
$logger->warning('Warning message');
$logger->error('Error message', ['error' => 'details']);

// Access request ID for log correlation
$requestId = \Retrologin\Logger::getRequestId();
```

### Error Handler

The ErrorHandler class captures PHP errors, exceptions, and fatal errors:

```php
use Retrologin\ErrorHandler;

$handler = ErrorHandler::getInstance();
$handler->init();

// Get error statistics
$stats = $handler->getStats();
// ['errors' => 0, 'warnings' => 0, 'request_id' => 'abc123']
```

### Error-to-Insight Pipeline

The plugin includes an automated error-to-issue pipeline:

1. **Error Detection**: Captures errors, warnings, and exceptions
2. **Issue Creation**: Automatically creates GitHub issues for critical errors
3. **Labels**: Applies type:bug, status:needs-triage, priority labels
4. **Correlation**: Links errors via request_id for distributed tracing

#### GitHub Actions Workflow

The workflow runs hourly to check for new errors:

```bash
# Manually trigger error check
gh workflow run error-to-issue.yml
```

#### Configuration

Set `RETROLOGIN_DEBUG=true` in `.env` to enable verbose error logging.

### Debug Commands

```bash
# View recent errors
grep -i "retrologin" wp-content/debug.log
```

## Automation Hooks

Configured to auto-run on file changes:
- **PHP files**: Auto-lint on save via PostToolUse hook
- **CSS/JS files**: Auto-build on save via PostToolUse hook

Configure hooks: `/hooks`

## Code Style

- Syntatis coding standard (via Howdy)
- PSR-4 autoloading with `Retrologin\` namespace
- Docblocks on classes and complex methods
- Hooks use `retrologin_` prefix
- Linter enforces standards: `composer run lint`

## Personal Preferences

Refer to `.factory/memories.md` for coding preferences and past decisions.

## Coding Standards

Follow the conventions documented in:
- `.factory/rules/howdy.md` - Framework structure and build commands
- `.factory/rules/php.md` - PHP naming and code style
- `.factory/rules/wordpress-login.md` - Login page hooks and styling
- `.factory/rules/security.md` - Security basics
- `.factory/rules/assets.md` - CSS/JS assets and enqueue
- `.factory/rules/testing.md` - Testing checklist

---

## Skills & Automation

Droid automatically uses skills when relevant to the task. Invoke droids manually with the Task tool.

### Skills (Context-Aware)

Skills are reusable capabilities invoked by Droid based on task context.

| Skill | When to Use |
|-------|-------------|
| `wordpress-hook-generator` | Creating WordPress actions/filters |
| `retro-css-generator` | Styling the login page |
| `howdy-structure` | Creating new classes |
| `i18n-helper` | Adding translatable strings |
| `asset-builder` | Building CSS/JS assets |
| `login-redirector` | Setting up redirects |
| `wp-settings-page` | Creating admin settings |
| `wp-security-audit` | Reviewing security |
| `wp-activation` | Handling activation/deactivation |
| `git-changelog` | Managing versions |

### Custom Droids (Subagents)

Invoke with Task tool: `Run the subagent <name> to <task>`

| Droid | Purpose | Tools | Reasoning |
|-------|---------|-------|-----------|
| `wp-code-reviewer` | Review code for standards | read-only | high |
| `wp-security-audit` | Scan for vulnerabilities | Read, Grep, WebSearch | high |
| `wp-lint-checker` | Run linting, auto-fix | edit | - |
| `wp-asset-builder` | Build CSS/JS assets | execute | - |
| `wp-test-runner` | Run PHPUnit tests | execute | - |
| `wp-local-tester` | Test in LocalWP | execute | - |
| `wp-changelog-manager` | Generate changelog | read-only | - |
| `wp-migration-handler` | Database migrations | edit | - |
| `wp-docs-generator` | Generate documentation | read-only, edit | medium |
| `wp-release-prep` | Prepare release zip | execute | - |
| `wp-task-coordinator` | Multi-step workflows | Read, Edit, Execute | - |

### Droid Usage Examples

```bash
# Code review
Run the subagent `wp-code-reviewer` on the staged changes.

# Security scan
Run the subagent `wp-security-audit` to scan for vulnerabilities.

# Auto-fix linting
Run the subagent `wp-lint-checker` to fix linting issues.

# Build assets
Run the subagent `wp-asset-builder` to compile assets.

# Test in LocalWP
Run the subagent `wp-local-tester` to verify the plugin.

# Prepare release
Run the subagent `wp-release-prep` to create distribution zip.

# Coordinate multi-step task
Run the subagent `wp-task-coordinator` to implement login customization.
```

### Model Strategy

Choose models strategically for cost efficiency:

| Task Type | Recommended Model | Reasoning |
|-----------|-----------------|-----------|
| Simple analysis (linting, summaries) | `inherit` (default) or Sonnet | Fast, cost-effective |
| Code review | `inherit` with `reasoningEffort: high` | Thorough analysis |
| Security audit | `inherit` with `reasoningEffort: high` | Deep vulnerability scan |
| Documentation | `inherit` with `reasoningEffort: medium` | Comprehensive docs |
| Complex multi-step | Opus | Best reasoning capability |

**Note:** All droids use `inherit` by default to match the parent session's model. Set `reasoningEffort` explicitly for complex tasks.

### Reasoning Effort

Configure reasoning depth for analysis-heavy droids:

- `low` - Quick, surface-level analysis
- `medium` - Balanced analysis and speed
- `high` - Deep, thorough reasoning (security audits, code reviews)

### Droid Management

**View all droids:**
```
/droids
```

**Actions available:**
- View droid details
- Edit droid configuration
- Delete droid
- Reload to refresh list

**Claude Code Import:**
Import existing Claude Code agents:
1. Run `/droids`
2. Press `I` to import
3. Select agents from `~/.claude/agents/`
4. Confirm import

### Automation Hooks

Configure via `/hooks` in droid:

**Auto-lint on PHP changes:**
```json
{
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Write",
        "match_pattern": "*.php",
        "hooks": [
          {
            "type": "command",
            "command": "cd \"$FACTORY_PROJECT_DIR\" && composer run format && composer run lint 2>/dev/null | tail -10 || true"
          }
        ]
      }
    ]
  }
}
```

**Auto-build on CSS/JS changes:**
```json
{
  "hooks": {
    "PostToolUse": [
      {
        "matcher": "Edit",
        "match_pattern": "*.{css,scss,js}",
        "hooks": [
          {
            "type": "command",
            "command": "cd \"$FACTORY_PROJECT_DIR\" && npm run build 2>/dev/null | tail -5 || true"
          }
        ]
      }
    ]
  }
}
```

---

## Project Overview

RetroLogin is a WordPress plugin that provides a retro-themed login experience for WordPress sites. The plugin enhances the default WordPress login page with nostalgic design elements while maintaining full compatibility with WordPress authentication.

## Technology Stack

- **PHP**: 8.0+ (modern PHP with namespaces)
- **WordPress**: 6.0+ (LocalWP running at retrologin.local)
- **Build Tools**: Composer, NPM + wp-scripts
- **Code Standards**: Syntatis Coding Standard (Howdy default)
- **Framework**: Howdy (syntatis/howdy) boilerplate

## Project Structure

```
retrologin/
├── AGENTS.md              # This file
├── composer.json          # Dependencies and autoloading
├── retrologin.php         # Main plugin file
├── app/                   # PSR-4 autoloaded classes
│   └── <Namespace>/       # e.g., Retrologin/Admin/Login.php
├── inc/                   # Non-classified PHP includes
│   ├── bootstrap/
│   │   └── app.php        # Bootstrap file
│   └── languages/         # Translation files
├── dist/                  # Built assets (CSS/JS) - don't edit
├── templates/             # Template files
└── .factory/              # Factory config
    ├── memories.md        # Project preferences
    ├── rules/             # Coding conventions (6 files)
    ├── skills/            # Reusable skills (10 files)
    └── droids/            # Custom subagents (11 files)
```

## File Locations Quick Reference

| Purpose | Location |
|---------|----------|
| Skills | `.factory/skills/<name>/SKILL.md` |
| Custom Droids | `.factory/droids/<name>.md` |
| Rules | `.factory/rules/<name>.md` |
| Memories | `.factory/memories.md` |
| Agent Instructions | `AGENTS.md` |

## Development Notes

- Test changes locally via LocalWP at http://retrologin.local
- Run linting before each commit: `composer run lint`
- Asset files are built to `dist/` - don't edit directly
- Use skills when creating hooks, CSS, classes, etc.
- Use droids for focused tasks: review, security, release
- Automation hooks auto-lint and auto-build on file changes
- Configure `/droids` for droid management
- Use `reasoningEffort: high` for security/code review tasks
