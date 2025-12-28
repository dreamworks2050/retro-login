# Security Audit Checklist

## Input Sanitization
- [ ] All user input sanitized with appropriate functions
- [ ] `$_GET`, `$_POST`, `$_COOKIE` values cleaned
- [ ] Database queries use prepared statements

## Output Escaping
- [ ] All HTML output escaped with `esc_*` functions
- [ ] Attributes escaped with `esc_attr()`
- [ ] URLs escaped with `esc_url()`

## Nonces & CSRF
- [ ] Forms include `wp_nonce_field()`
- [ ] Nonces verified before processing
- [ ] Action names are specific

## Capabilities
- [ ] Admin actions check `current_user_can()`
- [ ] Roles/permissions verified
- [ ] No privilege escalation

## File Security
- [ ] All PHP files have `ABSPATH` check
- [ ] No direct file access possible
- [ ] No sensitive data in logs

## Database
- [ ] `$wpdb->prepare()` used for all queries
- [ ] No string interpolation in SQL
- [ ] Escaped data in LIKE clauses

## Third-Party Data
- [ ] External APIs validated
- [ ] Output from external sources escaped
- [ ] No unsafe redirects
