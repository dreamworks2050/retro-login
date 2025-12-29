# Development Container for RetroLogin

This directory contains configuration for VS Code Development Containers, providing a consistent development environment.

## Quick Start

1. **Open in VS Code**
   ```bash
   code .
   ```

2. **Reopen in Container**
   - Press `F1` or `Ctrl+Shift+P`
   - Type "Dev Containers: Reopen in Container"
   - Press Enter

3. **Wait for Setup**
   - First run: Dependencies install automatically
   - Subsequent runs: Instant startup

## Manual Setup (Alternative)

If you prefer command line:

```bash
# Build and start the container
docker-compose -f .devcontainer/docker-compose.yml up -d

# Enter the container
docker exec -it retro-login-dev bash

# Inside the container, install dependencies
composer install
npm install

# Run tests
composer test
npm run lint:js
```

## What's Included

- **PHP 8.2** with Composer
- **Node.js 20** with npm
- **VS Code Extensions**
  - Intelephense (PHP IDE)
  - ESLint
  - Prettier
  - PHP CS Fixer

## Commands

```bash
# Run PHP tests
composer test

# Run JavaScript linting
npm run lint:js

# Build assets
npm run build

# Start development server
npm run start
```

## Ports

| Port | Service |
|------|---------|
| 80   | HTTP    |
| 443  | HTTPS   |

## Troubleshooting

### Container fails to build
- Ensure Docker is running
- Check available memory (4GB minimum recommended)

### Dependencies not installing
- Try rebuilding: `Dev Containers: Rebuild Container`
- Check network connectivity

### VS Code extensions not loading
- Reload VS Code: `Ctrl+Shift+P` → "Developer: Reload Window"
- Check extension logs: `Output` → "Dev Containers"
