# Howdy Framework Structure

## Directory Layout
```
retrologin/
├── app/                 # PSR-4 classes (Retrologin\ namespace)
├── inc/                 # Includes, bootstrap, languages
├── dist/                # Built assets (generated)
├── templates/           # Template files
├── retrologin.php       # Main plugin file
└── composer.json
```

## Autoloading
- Composer maps `Retrologin\` to `app/` directory
- Example: `app/Admin/Login.php` → `Retrologin\Admin\Login`
- Rely on autoloading; don't manually require files

## Build Commands
```bash
npm run start    # Watch and rebuild assets during dev
npm run build    # Production build (minified)
composer run lint    # Check PHP code style
composer run format  # Fix code style issues
composer scope       # Scope dependencies for production
```

## Dependencies
- PHP-Scoper prefixes vendor namespaces as `Retrologin\Vendor`
- Prevents conflicts with other plugins using same libraries
- Scoped output: `dist/autoload/`

## WordPress Compatibility
- Supports WordPress 6.0+
- PHP 7.4 minimum, 8.0+ recommended
- Syntatis coding standard enforced via phpcs.xml.dist

## File Header Pattern
Each PHP file should start with:
```php
<?php

declare(strict_types=1);

/**
 * Short description.
 *
 * @package Retrologin
 * @since   0.1.0
 */
```
