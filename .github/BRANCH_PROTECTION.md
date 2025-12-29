# Branch Protection Rules

This document provides instructions for setting up branch protection rules on GitHub.

## Status: CONFIGURED ✅

Branch protection is now active via GitHub Rulesets API. Verify with:

```bash
gh api repos/dreamworks2050/retro-login/rulesets
```

**Ruleset ID**: `11443121`  
**Enforcement**: `active`  
**Rules Applied**: `non_fast_forward` (prevents force pushes to main)

## Required Protection Rules

For Level 2 Agent Readiness, the following branch protection rules should be configured:

### Main Branch Protection Settings

1. **Require a pull request before merging**
   - Require approvals: 1
   - Dismiss stale pull request approvals when new commits are pushed
   - Require review from Code Owners (if CODEOWNERS file exists)

2. **Require status checks to pass before merging**
   - Require branches to be up to date before merging
   - Add required status checks:
     - `ci` (GitHub Actions CI workflow)

3. **Restrict who can push to main**
   - Optionally: Restrict to specific teams or maintainers

## Manual Setup via GitHub UI

If you need to modify the ruleset manually:

1. Navigate to the repository: https://github.com/dreamworks2050/retro-login
2. Go to **Settings** → **Rules** → **Rulesets**
3. Click on "Main Branch Protection" ruleset
4. Click **Edit** to modify settings

## Automated Setup via API

The ruleset can also be configured programmatically:

```bash
# Create ruleset
curl -X POST \
  -H "Authorization: Bearer $(gh auth token)" \
  -H "Accept: application/vnd.github+json" \
  https://api.github.com/repos/dreamworks2050/retro-login/rulesets \
  -d '{
    "name": "Main Branch Protection",
    "target": "branch",
    "enforcement": "active",
    "conditions": {
      "ref_name": {
        "include": ["refs/heads/main"],
        "exclude": []
      }
    },
    "rules": [
      {"type": "non_fast_forward"}
    ]
  }'
```

## Verification

After setup, verify branch protection is active:

```bash
gh api repos/dreamworks2050/retro-login/rulesets
```

Expected response includes the active ruleset with ID `11443121`.
