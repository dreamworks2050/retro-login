# RetroLogin Hook API Reference

This document describes the WordPress hooks (actions and filters) that RetroLogin provides for customization.

## Action Hooks

### `retrologin_enqueue_scripts`

Fires when scripts and styles are enqueued on the login page.

**Usage:**
```php
add_action('retrologin_enqueue_scripts', 'my_custom_login_styles');
function my_custom_login_styles(): void {
    wp_enqueue_style(
        'my-custom-login',
        get_template_directory_uri() . '/css/login.css',
        [],
        '1.0.0'
    );
}
```

**Parameters:** None

---

### `retrologin_login_header`

Fires in the login header section.

**Usage:**
```php
add_action('retrologin_login_header', 'my_login_header_content');
function my_login_header_content(): void {
    echo '<div class="my-custom-header">Welcome!</div>';
}
```

**Parameters:** None

---

### `retrologin_login_form_top`

Fires at the top of the login form.

**Usage:**
```php
add_action('retrologin_login_form_top', 'my_form_top_message');
function my_form_top_message(): void {
    echo '<p class="login-message">Please log in to continue.</p>';
}
```

**Parameters:** None

---

### `retrologin_login_form_bottom`

Fires at the bottom of the login form.

**Usage:**
```php
add_action('retrologin_login_form_bottom', 'my_form_bottom_content');
function my_form_bottom_content(): void {
    echo '<p class="forgot-password"><a href="/wp-login.php?action=lostpassword">Forgot password?</a></p>';
}
```

**Parameters:** None

---

### `retrologin_login_footer`

Fires in the login footer section.

**Usage:**
```php
add_action('retrologin_login_footer', 'my_footer_content');
function my_footer_content(): void {
    echo '<div class="my-custom-footer">&copy; 2025 My Site</div>';
}
```

**Parameters:** None

---

## Filter Hooks

### `retrologin_login_logo_url`

Modify the login logo URL.

**Usage:**
```php
add_filter('retrologin_login_logo_url', 'my_custom_logo_url');
function my_custom_logo_url(string $url): string {
    return home_url('/');
}
```

**Parameters:**
- `$url` (string): The original logo URL

**Returns:** (string) Modified logo URL

---

### `retrologin_login_logo_title`

Modify the login logo title (tooltip).

**Usage:**
```php
add_filter('retrologin_login_logo_title', 'my_custom_logo_title');
function my_custom_logo_title(string $title): string {
    return get_bloginfo('name');
}
```

**Parameters:**
- `$title` (string): The original logo title

**Returns:** (string) Modified logo title

---

### `retrologin_login_message`

Modify the login message displayed above the form.

**Usage:**
```php
add_filter('retrologin_login_message', 'my_custom_login_message');
function my_custom_login_message(string $message): string {
    if (empty($message)) {
        return '';
    }
    return '<p class="message">' . esc_html($message) . '</p>';
}
```

**Parameters:**
- `$message` (string): The original login message

**Returns:** (string) Modified login message

---

### `retrologin_login_errors`

Modify the error messages displayed on failed login.

**Usage:**
```php
add_filter('retrologin_login_errors', 'my_custom_login_errors');
function my_custom_login_errors(WP_Error $errors): WP_Error {
    // Add custom error handling
    $errors->add('custom_error', 'Please contact support for assistance.');
    return $errors;
}
```

**Parameters:**
- `$errors` (WP_Error): The WP_Error object containing login errors

**Returns:** (WP_Error) Modified WP_Error object

---

### `retrologin_remember_me_label`

Modify the "Remember Me" checkbox label.

**Usage:**
```php
add_filter('retrologin_remember_me_label', 'my_remember_me_label');
function my_remember_me_label(string $label): string {
    return __('Keep me signed in', 'my-textdomain');
}
```

**Parameters:**
- `$label` (string): The original label text

**Returns:** (string) Modified label text

---

## Example: Complete Customization

```php
<?php
/**
 * RetroLogin Hook Examples
 *
 * Add this code to your theme's functions.php or a custom plugin.
 */

// Enqueue custom styles
add_action('retrologin_enqueue_scripts', 'example_enqueue_styles');
function example_enqueue_styles(): void {
    wp_enqueue_style(
        'example-login-style',
        get_stylesheet_directory_uri() . '/css/example-login.css',
        [],
        '1.0.0'
    );
}

// Change logo URL
add_filter('retrologin_login_logo_url', 'example_logo_url');
function example_logo_url(string $url): string {
    return home_url('/');
}

// Change logo title
add_filter('retrologin_login_logo_title', 'example_logo_title');
function example_logo_title(string $title): string {
    return get_bloginfo('name');
}

// Add custom message
add_action('retrologin_login_form_top', 'example_form_message');
function example_form_message(): void {
    echo '<p class="welcome-message">Welcome back to our site!</p>';
}
```

## Hook Priority

RetroLogin hooks support priority parameters. Lower numbers execute first.

```php
// Execute early (default priority is 10)
add_action('retrologin_enqueue_scripts', 'early_hook', 5);

// Execute late
add_action('retrologin_enqueue_scripts', 'late_hook', 20);
```

## Version Added

- All hooks: v0.1.0
