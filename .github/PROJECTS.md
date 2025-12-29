# GitHub Projects Configuration for RetroLogin

This document describes how to set up and manage the GitHub Project for tracking RetroLogin development.

## Quick Setup

### Option 1: GitHub Projects (New)

1. Go to https://github.com/dreamworks2050/retro-login/projects
2. Click "New project"
3. Choose "Board" view
4. Name: "RetroLogin Development"
5. Create

### Option 2: Command Line Setup

Run the setup script:
```bash
# Create project using GitHub CLI
gh project create "RetroLogin Development" --owner dreamworks2050 --template "Automated kanban"
```

## Recommended Board Structure

| Column | Description | Automation |
|--------|-------------|------------|
| **Backlog** | Items not yet prioritized | Auto-add new issues |
| **Ready** | Ready to work on | Manual |
| **In Progress** | Actively being developed | Auto-move on assign |
| **Review** | Awaiting review (PR/QA) | Auto-move on PR |
| **Done** | Completed | Auto-move on merge |

## Issue Labels

Use the following label categories:

### Type Labels
- `bug` - Bug reports
- `enhancement` - Feature requests
- `documentation` - Documentation updates
- `refactoring` - Code improvements
- `testing` - Test-related changes

### Priority Labels
- `P0` - Critical (blocker)
- `P1` - High (important)
- `P2` - Medium (nice to have)
- `P3` - Low (backlog)

### Status Labels
- `status:triaged` - Initial assessment done
- `status:confirmed` - Confirmed and ready
- `status:blocked` - Blocked by dependency
- `status:duplicate` - Duplicate of existing

### Area Labels
- `area:frontend` - UI/CSS/JavaScript
- `area:backend` - PHP/WordPress
- `area:ci-cd` - Infrastructure/CI
- `area:docs` - Documentation

## Automation Rules

Configure in Project Settings > Workflows:

1. **When an issue is opened**
   - Add to "Backlog" column
   - Add `status:triaged` label

2. **When an issue is labeled**
   - If `P0`/`P1`, move to "Ready"

3. **When a PR is opened**
   - Move linked issue to "Review"

4. **When a PR is merged**
   - Move linked issue to "Done"
   - Remove from board

## Sample Issue Template

```markdown
## Description
Brief description of the issue/feature

## Acceptance Criteria
- [ ] Criterion 1
- [ ] Criterion 2
- [ ] Criterion 3

## Additional Context
Links, screenshots, or notes
```

## Commands for Project Management

```bash
# List projects
gh project list --owner dreamworks2050

# View project items
gh project item-list <project-number>

# Add item to project
gh project item-add <project-number> --body "Issue description"

# Link issue to project
gh issue edit <issue-number> --project "RetroLogin Development"
```

## Backlog Health Metrics

The CI pipeline includes a `backlog-health` job that checks:

1. **Issue Title Quality** - Titles > 10 characters
2. **Label Coverage** - Issues have at least one label
3. **Issue Freshness** - No issues older than 365 days without activity

### Running Health Check

```bash
# Local check
.github/scripts/backlog-health-check.sh

# Or via CI (runs on schedule)
# Triggers: schedule (weekly), workflow_dispatch
```

## Best Practices

1. **Create issues first** - Before starting work
2. **Link PRs to issues** - Use "Fixes #123" syntax
3. **Keep board current** - Update column status daily
4. **Use priorities** - Focus on P0/P1 first
5. **Regular grooming** - Review backlog weekly

## Related Files

- [Issue Templates](../ISSUE_TEMPLATE/)
- [Pull Request Template](../pull_request_template.md)
- [CI Workflows](../workflows/)
