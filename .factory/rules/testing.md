# Testing & Quality

## Local Development
- LocalWP site: `retrologin.local`
- URL: `http://retrologin.local/wp-login.php`

## Quality Checks
```bash
composer run lint    # Check PHP code style
composer run format  # Auto-fix code style issues
npm run build        # Build assets
```

## What to Test
- Login form displays and works
- Logout works
- Password reset flow works
- Error messages display correctly
- Retro styling applies correctly
- Mobile responsive design

## Before Commit
1. Run `composer run lint` - should pass with no errors
2. Run `npm run build` - assets should build successfully
3. Test login page manually in browser

## Common Issues
- Assets not loading: Verify `dist/assets/` exists
- Linter errors: Run `composer run format` to auto-fix
- Styles not applying: Check login-specific CSS selectors
