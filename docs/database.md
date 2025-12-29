# RetroLogin Data Storage

RetroLogin uses WordPress Options API for persistent data storage. No custom database tables are required.

## Options Stored

| Option Name | Type | Description | Default |
|------------|------|-------------|---------|
| `retrologin_settings` | array | Plugin configuration settings | `[]` |
| `retrologin_version` | string | Current plugin version for migrations | `''` |
| `retrologin_activated` | bool | First activation timestamp | `false` |
| `retrologin_activation_count` | int | Number of activations | `0` |

## Access Pattern

### Getting Options

```php
// Get all settings
$settings = get_option('retrologin_settings', []);

// Get a specific setting
$login_type = $settings['login_type'] ?? 'default';

// Check if plugin was activated
$activated = get_option('retrologin_activated', false);
```

### Updating Options

```php
// Update settings (preserves other settings)
$settings = get_option('retrologin_settings', []);
$settings['custom_login'] = true;
update_option('retrologin_settings', $settings);

// Simple update
update_option('retrologin_version', '0.1.0');
```

### Deleting Options

```php
// Remove a setting
$settings = get_option('retrologin_settings', []);
unset($settings['old_setting']);
update_option('retrologin_settings', $settings);

// Reset all settings
delete_option('retrologin_settings');
delete_option('retrologin_version');
```

## Settings Structure

The `retrologin_settings` option contains an associative array:

```php
$default_settings = [
    'login_type'        => 'retro',      // 'retro', 'modern', 'default'
    'background_color'  => '#f0f0f1',    // CSS color
    'logo_url'          => '',           // Custom logo URL
    'enable_animations' => true,         // CSS animations toggle
    'custom_css'        => '',           // Additional CSS
];
```

## Migration Pattern

RetroLogin handles migrations via the `update_option` hook:

```php
/**
 * Handle plugin migrations
 */
function retrologin_migrate(): void {
    $current_version = get_option('retrologin_version', '0.0.0');
    
    if (version_compare($current_version, '0.1.0', '<')) {
        // Migration from pre-0.1.0
        $old_settings = get_option('retro_settings', []);
        $new_settings = [
            'login_type' => $old_settings['type'] ?? 'retro',
            'background_color' => $old_settings['bg_color'] ?? '#f0f0f1',
        ];
        update_option('retrologin_settings', $new_settings);
        update_option('retrologin_version', '0.1.0');
    }
}
register_activation_hook(__FILE__, 'retrologin_migrate');
```

## Transient Storage (Optional)

For temporary data, RetroLogin uses WordPress transients:

```php
// Cache API responses
set_transient('retrologin_api_cache', $data, HOUR_IN_SECONDS);

// Get cached data
$cached = get_transient('retrologin_api_cache');

// Delete cache
delete_transient('retrologin_api_cache');
```

## User Meta (Future Enhancement)

When user-specific settings are needed:

```php
// Store user preference
update_user_meta($user_id, 'retrologin_theme', 'retro');

// Retrieve user preference
$theme = get_user_meta($user_id, 'retrologin_theme', true);
```

## WordPress.org Stats Compatible

The plugin tracks activation events for WordPress.org stats compatibility:

```php
// On activation
$count = get_option('retrologin_activation_count', 0);
update_option('retrologin_activation_count', $count + 1);

// First activation
if (!get_option('retrologin_activated')) {
    update_option('retrologin_activated', current_time('mysql'));
}
```

## Security Considerations

All settings are sanitized before saving:

```php
// Sanitize on save
$sanitize_callback = function($settings) {
    return [
        'login_type'        => sanitize_text_field($settings['login_type'] ?? 'retro'),
        'background_color'  => sanitize_hex_color($settings['background_color'] ?? '#f0f0f1'),
        'logo_url'          => esc_url_raw($settings['logo_url'] ?? ''),
        'enable_animations' => rest_sanitize_boolean($settings['enable_animations'] ?? true),
        'custom_css'        => wp_strip_all_tags($settings['custom_css'] ?? ''),
    ];
};
register_setting('retrologin_options', 'retrologin_settings', $sanitize_callback);
```

## Performance Notes

- Options API is fast for small data sets
- Use `wp_cache_get()` / `wp_cache_set()` for frequent access
- Consider transients for API-heavy operations
- No JOIN queries needed (simpler than custom tables)
