# 🚀 Quick Start - Docker Development

**IMPORTANT**: Don't run npm/pnpm on macOS! Use Docker instead to avoid oxc-parser errors.

## One-Time Setup

```bash
# 1. Go to project root
cd /home/user/didonehousing

# 2. Clean any Mac npm artifacts (if you tried npm before)
cd frontend
rm -rf node_modules package-lock.json pnpm-lock.yaml .nuxt .output
cd ..

# 3. Make sure .env exists
cp backend/.env.example backend/.env  # if needed
```

## Daily Development

```bash
# Start all services (backend + frontend + databases)
docker compose up -d

# Watch frontend logs
docker compose logs -f frontend

# Watch backend logs
docker compose logs -f backend

# Open in browser
# Frontend: http://localhost:3000
# Backend:  http://localhost:8000
# MailHog:  http://localhost:8025
```

## Making Changes

### Edit Code
- Edit files in `frontend/` or `backend/` on your Mac
- Changes auto-reload via HMR (no restart needed)

### Add npm Package
```bash
# Edit package.json, then rebuild:
docker compose up -d --build frontend

# Or install directly in container:
docker compose exec frontend npm install <package-name>
```

### Clear Cache
```bash
# Frontend cache
docker compose exec frontend rm -rf .nuxt .output
docker compose restart frontend

# Backend cache
docker compose exec backend php artisan cache:clear
```

## Useful Commands

```bash
# Stop all services
docker compose down

# Rebuild after package.json changes
docker compose up -d --build frontend

# Access container shell
docker compose exec frontend sh
docker compose exec backend bash

# View all running containers
docker compose ps

# Complete clean restart
docker compose down -v
docker compose up -d --build
```

## Database Setup

```bash
# Run migrations
docker compose exec backend php artisan migrate

# Seed database
docker compose exec backend php artisan db:seed

# Fresh start
docker compose exec backend php artisan migrate:fresh --seed
```

## Testing

```bash
# Backend tests
docker compose exec backend php artisan test

# Frontend tests (when available)
docker compose exec frontend npm run test
```

## Stopping

```bash
# Stop all (keeps volumes/data)
docker compose down

# Stop and delete all data
docker compose down -v
```

---

## Why Docker?

- ✅ **No macOS npm bugs** (oxc-parser works in Linux)
- ✅ **Same environment for everyone** (Mac, Windows, Linux)
- ✅ **Same as production**
- ✅ **Easy onboarding** (one command to start)

**Full guide**: See [docs/DOCKER_FRONTEND_SETUP.md](docs/DOCKER_FRONTEND_SETUP.md)

---

## First Time Running?

```bash
# Start everything
docker compose up -d

# Wait 30 seconds for services to start, then:

# Setup backend
docker compose exec backend php artisan migrate:fresh --seed

# Check status
docker compose ps

# View logs
docker compose logs -f

# Access:
# - Frontend: http://localhost:3000
# - Backend API: http://localhost:8000/api
# - API Docs: http://localhost:8000/docs
# - MailHog: http://localhost:8025
# - MinIO Console: http://localhost:9001
# - Traefik Dashboard: http://localhost:8080
```

## Login Credentials (after seeding)

```
Admin:
  Email: admin@avianohousing.local
  Password: password

Landlord:
  Email: landlord@avianohousing.local
  Password: password

Tenant:
  Email: tenant@avianohousing.local
  Password: password

Housing Office:
  Email: ho@avianohousing.local
  Password: password
```

---

**Happy coding! 🎉**
