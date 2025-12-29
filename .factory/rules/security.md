# Security Basics

## ABSPATH Check

Every PHP file should include this check:

```php
if (! defined('ABSPATH')) {
    exit;
}
```

## Sanitization

Clean user input before use:

-   Text: `sanitize_text_field()`
-   Email: `sanitize_email()`
-   Integer: `absint()`
-   URL: `esc_url_raw()`

## Escaping

Escape output when displaying:

-   HTML content: `esc_html()`
-   HTML attributes: `esc_attr()`
-   URLs: `esc_url()`
-   Textarea content: `esc_textarea()`

## Nonces

Use for form submissions to prevent CSRF:

-   Generate: `wp_nonce_field('action_name', 'nonce_field')`
-   Verify: `wp_verify_nonce($_POST['nonce_field'], 'action_name')`

## Error Messages

Don't reveal valid usernames in error messages. Use generic messages like "Invalid credentials" or "Incorrect password."

## Capabilities

Check permissions for admin features:

```php
if (! current_user_can('manage_options')) {
    return;
}
```
