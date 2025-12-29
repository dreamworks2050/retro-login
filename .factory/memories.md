# WordPress Plugin Development Memories

## Project Context

-   **Plugin Type**: WordPress custom login page
-   **Boilerplate**: Howdy (syntatis/howdy)
-   **Namespace**: `Retrologin\`
-   **Plugin Slug**: `retrologin`

## Howdy Framework Conventions

-   Use PSR-4 autoloading via Composer
-   Classes go in `src/` directory
-   Bootstrap via `inc/bootstrap/app.php`
-   Use Syntatis coding standard (not classic WPCS)
-   Scoped dependencies in `vendor` (PHP-Scoper)
-   Assets built with wp-scripts to `dist/assets`

## WordPress Login Specifics

-   Main file: `retrologin.php`
-   Entry point: `inc/bootstrap/app.php`
-   Template files: `templates/` directory
-   Internationalization: Text domain `retrologin`, files in `inc/languages/`

## Past Decisions

-   [2025-12] Chose Howdy boilerplate over vanilla WP plugin structure
-   [2025-12] Using Syntatis coding standard (configured in phpcs.xml.dist)
-   [2025-12] All Factory config kept project-specific (.factory/ directory)
-   [2025-12] Asset building via npm + wp-scripts (not custom webpack)

## Code Style (Retrologin-Specific)

-   All new PHP files: `declare(strict_types=1);`
-   Use 4-space indentation (Howdy default)
-   Docblocks required for all classes and public methods
-   Hooks: `retrologin_` prefix (e.g., `retrologin_login_head`)

## Testing & Quality

-   Lint: `composer run lint`
-   Build: `npm run build`
-   Local dev: LocalWP at retrologin.local
