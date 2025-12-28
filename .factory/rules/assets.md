# Assets (CSS/JS)

## Build Commands
```bash
npm run start    # Watch mode for development
npm run build    # Production build (minified)
```

## Login Page Assets
- Enqueue via `login_enqueue_scripts` hook
- Login page does not load theme assets
- Keep minimal for performance

## Asset Location
- Built assets: `dist/assets/`
- Use `plugins_url('dist/assets/...', __FILE__)` for URLs
- Source files: SCSS/JS in project root or `src/`

## Retro CSS Guidelines
- Use CSS custom properties for colors/fonts
- Target `.login` wrapper class
- Common selectors: `#login`, `#loginform`, `.login-message`
- Keep retro effects subtle for accessibility

## Enqueue Pattern
```php
add_action('login_enqueue_scripts', 'retrologin_enqueue_assets');
function retrologin_enqueue_assets() {
    wp_enqueue_style(
        'retrologin-login',
        plugins_url('dist/assets/login.css', __FILE__)
    );
}
```
