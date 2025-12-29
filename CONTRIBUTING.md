# Contributing to RetroLogin

Thank you for your interest in contributing to RetroLogin! This document provides guidelines and instructions for contributing.

## Getting Started

### Prerequisites

- PHP 8.0+
- Composer
- Node.js 20+
- WordPress 6.0+ (for local testing)
- LocalWP or similar local development environment

### Development Setup

1. **Clone the repository:**

   ```bash
   git clone https://github.com/dreamworks2050/retro-login.git
   cd retro-login
   ```

2. **Install dependencies:**

   ```bash
   composer install && npm install
   ```

3. **Set up local environment:**

   ```bash
   cp .env.example .env
   # Edit .env with your local WordPress settings
   ```

4. **Verify installation:**
   ```bash
   composer test
   npm run lint:js
   ```

## Development Workflow

### 1. Create a Branch

Create a new branch for your changes:

```bash
git checkout -b feature/your-feature-name
# or for bug fixes:
git checkout -b fix/issue-description
```

### 2. Make Changes

Follow the coding standards:

- **PHP:** Syntatis coding standard (enforced via PHPCS)
- **JavaScript:** WordPress ESLint config
- **Formatting:** Use `composer run format` and `npm run format`

### 3. Test Your Changes

Run tests before committing:

```bash
# PHP tests
composer test

# PHP linting
composer run lint

# JavaScript linting
npm run lint:js

# Static analysis
composer phpstan
```

### 4. Commit Your Changes

Use conventional commit messages:

```
feat: Add new login page customization option
fix: Resolve styling issue on mobile devices
docs: Update installation instructions
chore: Update dependencies
```

### 5. Push and Create PR

```bash
git push origin feature/your-feature-name
```

Then create a pull request at:
https://github.com/dreamworks2050/retro-login/compare

## Code Standards

### PHP Coding Standards

We use the Syntatis coding standard. Run the linter:

```bash
# Check for issues
composer run lint

# Auto-fix issues
composer run format
```

### JavaScript Standards

We use `@wordpress/eslint-plugin`. Lint your code:

```bash
npm run lint:js

# Auto-format
npm run format
```

### Testing Requirements

All new features should include:

1. **Unit tests** in `tests/Unit/`
2. **Integration tests** in `tests/Integration/`

Run tests:

```bash
composer test
```

## Documentation

Update documentation when adding features:

- API documentation is auto-generated from docblocks
- Update `docs/hooks-api.md` for new hooks
- Update `README.md` for user-facing changes
- Update `AGENTS.md` for agent-specific instructions

## Issue Tracking

### Bug Reports

Use the bug report template:
https://github.com/dreamworks2050/retro-login/issues/new?template=bug_report.yml

### Feature Requests

Use the feature request template:
https://github.com/dreamworks2050/retro-login/issues/new?template=feature_request.yml

## Pull Request Process

1. **Fill out the PR template completely**
2. **Ensure all CI checks pass**
3. **Link related issues** (e.g., "Fixes #123")
4. **Request review** from maintainers
5. **Address feedback** and push updates
6. **Merge** after approval

## Labels

We use a standardized labeling system:

| Label           | Description           |
| --------------- | --------------------- |
| `bug`           | Bug reports           |
| `enhancement`   | Feature requests      |
| `documentation` | Documentation updates |
| `P0`-`P3`       | Priority levels       |
| `area:frontend` | UI/CSS/JavaScript     |
| `area:backend`  | PHP/WordPress         |

## Release Process

Releases follow semantic versioning:

1. Update version in `retrologin.php`
2. Update `readme.txt` stable tag
3. Create release on GitHub
4. CI automatically builds and uploads artifacts

## Getting Help

- **Documentation:** See `docs/` directory
- **AGENTS.md:** Agent-specific instructions
- **GitHub Issues:** For bugs and feature requests
- **GitHub Discussions:** General questions

## Code of Conduct

This project follows the [Contributor Covenant](https://www.contributor-covenant.org/).

---

By contributing, you agree to the LICENSE terms of this project.
