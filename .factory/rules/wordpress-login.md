# WordPress Login Customization

## Login Page Hooks
The login page has its own hooks separate from the main WordPress site:

- `login_enqueue_scripts` - Enqueue CSS/JS for login page
- `login_headerurl` - Change logo link (default: wordpress.org)
- `login_headertitle` - Change logo title attribute
- `login_message` - Add custom message above login form
- `login_footer_text` - Change footer text
- `login_redirect` - Customize redirect after login
- `logout_redirect` - Customize redirect after logout

## Styling Approach
- Enqueue retro styles via `login_enqueue_scripts`
- Login page uses `.login` as wrapper class
- Common elements: `#login`, `#loginform`, `.login-message`
- Use CSS custom properties for retro color palettes

## Retro Styling Guidelines
- Define colors as CSS variables at `:root` level
- Keep styling focused on login page only
- Don't assume theme styles are available
- Ensure contrast and accessibility still work

## Error Handling
- Use generic error messages (don't reveal valid usernames)
- Hook: `login_errors` filter to customize

## Testing
- URL: `http://retrologin.local/wp-login.php`
- Test: Login, logout, password reset, error messages
- Check mobile responsiveness
