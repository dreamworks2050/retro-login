# PHP Conventions

## Namespacing
- All classes under `Retrologin\` namespace
- Follow PSR-4 directory structure: `app/Namespace/Class.php`

## Naming
- Classes: PascalCase (`LoginForm`, `RetroController`)
- Methods/properties: camelCase
- Constants: SCREAMING_SNAKE_CASE
- Hooks: `retrologin_` prefix (`retrologin_login_head`)

## Docblocks
Classes should have a description. Public methods with parameters/return values should be documented. Use `@since` for version tracking.

Example:
```php
/**
 * Handles login form display and validation.
 *
 * @since 0.1.0
 */
class LoginForm {

    /**
     * Validate user credentials.
     *
     * @param string $username Username.
     * @param string $password Password.
     * @return bool Whether valid.
     * @since 0.1.0
     */
    public function validate( string $username, string $password ): bool {
        // ...
    }
}
```

## Constants
Define in main plugin file or relevant class. Use prefix:
```php
const PLUGIN_VERSION = '0.1.0';
```

## ABSPATH Check
Include in files that could be accessed directly:
```php
if (! defined('ABSPATH')) {
    exit;
}
```

## Flexibility
- Multiple small helper classes in one file is OK if closely related
- Simple utility functions can be namespaced without a class
- The linter (composer run lint) enforces the Syntatis standard
