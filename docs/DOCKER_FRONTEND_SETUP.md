# Docker Frontend Setup - The RIGHT Way

## 🎯 The Problem We Were Having

We were trying to run `npm install` / `pnpm install` on macOS, which caused:
- ❌ oxc-parser native binding errors (npm bug #4828)
- ❌ Platform-specific optional dependency issues
- ❌ darwin-arm64 vs linux-x64 binding conflicts

## ✅ The Solution: Use Docker

**Your docker-compose.yml already has a frontend service configured!**

The frontend Docker container runs **Linux Alpine** (not macOS), so:
- ✅ oxc-parser installs `linux-x64-musl` bindings (not darwin-arm64)
- ✅ No npm optional dependencies bug
- ✅ Same environment for all developers (Mac, Windows, Linux)
- ✅ Same environment as production

---

## 🚀 Quick Start with Docker

### Step 1: Stop trying to run npm/pnpm on Mac

```bash
# If you have node_modules on Mac, remove them
cd frontend
rm -rf node_modules package-lock.json pnpm-lock.yaml .nuxt .output
```

**DON'T run `npm install` on your Mac!** The container will handle this.

### Step 2: Build and start the frontend container

```bash
# From project root
cd /home/user/didonehousing

# Build and start frontend (this runs npm install INSIDE the container)
docker compose up -d frontend

# Or with older docker-compose:
docker-compose up -d frontend
```

### Step 3: Watch the logs

```bash
# Follow the logs to see npm install progress
docker compose logs -f frontend

# You should see:
# ✔ Nuxt built successfully
# ✔ Vite server built in XXXms
# ➜ Local:   http://localhost:3000/
```

### Step 4: Access the frontend

Open your browser to:
- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000

---

## 📋 Complete Docker Workflow

### Starting the Entire Stack

```bash
# Start all services (backend, frontend, databases, etc.)
docker compose up -d

# Check status
docker compose ps

# You should see:
# - aviano_frontend    (port 3000)
# - aviano_backend     (port 8000)
# - aviano_postgres    (port 5432)
# - aviano_redis       (port 6379)
# - aviano_minio       (port 9000, 9001)
# - aviano_typesense   (port 8108)
# - aviano_mailhog     (port 1025, 8025)
```

### Viewing Logs

```bash
# All services
docker compose logs -f

# Just frontend
docker compose logs -f frontend

# Just backend
docker compose logs -f backend

# Last 100 lines
docker compose logs --tail=100 frontend
```

### Stopping Services

```bash
# Stop all
docker compose down

# Stop but keep volumes (databases, node_modules)
docker compose down

# Stop and remove volumes (clean slate)
docker compose down -v
```

### Rebuilding After Changes

```bash
# If you change package.json
docker compose up -d --build frontend

# Force complete rebuild
docker compose build --no-cache frontend
docker compose up -d frontend
```

---

## 🔧 Development Workflow

### Making Code Changes

Your code is mounted as a volume, so changes are reflected immediately:

```yaml
volumes:
  - ./frontend:/app                      # Your code
  - frontend_node_modules:/app/node_modules  # Persisted dependencies
  - frontend_nuxt:/app/.nuxt             # Nuxt cache
```

1. Edit files in `frontend/` on your Mac
2. Nuxt HMR (Hot Module Replacement) auto-reloads in the browser
3. No need to restart the container

### Adding New npm Packages

**Option 1: Let Docker handle it (recommended)**

```bash
# Edit package.json to add the dependency
vim frontend/package.json

# Rebuild the container (installs new packages)
docker compose up -d --build frontend
```

**Option 2: Run npm inside the container**

```bash
# Install a package
docker compose exec frontend npm install <package-name>

# Update package.json manually first, then:
docker compose exec frontend npm install
```

**NEVER run `npm install` on your Mac!** Always use the container.

### Running Commands in the Container

```bash
# Run any npm script
docker compose exec frontend npm run build
docker compose exec frontend npm run generate

# Access shell inside container
docker compose exec frontend sh

# Inside container shell:
/app $ npm install
/app $ npm run dev
/app $ ls -la
/app $ exit
```

### Clearing Nuxt Cache

```bash
# Remove .nuxt cache (volume is persisted)
docker compose exec frontend rm -rf .nuxt .output

# Or restart the container
docker compose restart frontend
```

---

## 🐛 Troubleshooting

### Port 3000 already in use

```bash
# Kill any local npm dev server on Mac
lsof -ti:3000 | xargs kill -9

# Or change the port in docker-compose.yml:
ports:
  - "3001:3000"  # Mac:Container
```

### oxc-parser still failing?

```bash
# Complete clean rebuild
docker compose down -v  # Remove ALL volumes
docker compose build --no-cache frontend
docker compose up -d frontend
docker compose logs -f frontend
```

### Frontend not connecting to backend

Check the environment variable in docker-compose.yml:

```yaml
environment:
  NUXT_PUBLIC_API_BASE: http://localhost:8000/api
```

This should work because Docker publishes backend on port 8000.

### Want to see what's inside the container?

```bash
# Access container shell
docker compose exec frontend sh

# Check node_modules
/app $ ls -la node_modules/oxc-parser
/app $ ls -la node_modules/@oxc-parser

# You should see linux-x64-musl bindings, NOT darwin-arm64!
/app $ cat node_modules/@oxc-parser/binding-linux-x64-musl/package.json
```

---

## 📊 Architecture: How the Volumes Work

```
Your Mac (darwin-arm64)
  ↓
  ├─ ./frontend/          → Your source code (mounted to container)
  │   ├─ pages/
  │   ├─ components/
  │   ├─ stores/
  │   └─ package.json
  │
Docker Container (linux-x64/alpine)
  ↓
  ├─ /app/                → Mounted from Mac
  │   ├─ pages/           → Same as Mac (live updates)
  │   ├─ components/      → Same as Mac (live updates)
  │   └─ package.json     → Same as Mac
  │
  ├─ /app/node_modules/   → Docker volume (NOT on Mac)
  │   └─ oxc-parser/
  │       └─ linux-x64-musl/  ← Linux bindings (works!)
  │
  └─ /app/.nuxt/          → Docker volume (cache)
```

**Key Points**:
- Your code is on Mac, mounted to container
- node_modules are in a Docker volume (Linux binaries)
- Changes to code are instant (volume mount)
- npm install happens in Linux (no macOS issues)

---

## ✅ Benefits of Docker Approach

1. **No macOS npm bugs** - runs Linux, no darwin-arm64 issues
2. **Same environment everywhere** - Mac, Windows, Linux devs use same container
3. **Same as production** - what works in dev works in prod
4. **Isolated dependencies** - doesn't pollute your Mac
5. **Easy onboarding** - new devs just run `docker compose up`
6. **No "works on my machine"** - everyone has identical setup

---

## 🎯 Summary: What You Should Do

### ❌ DON'T:
- Run `npm install` / `pnpm install` on your Mac
- Try to fix oxc-parser on macOS
- Install Node.js packages globally on Mac
- Fight with npm/pnpm on darwin-arm64

### ✅ DO:
- Use `docker compose up -d frontend` to start development
- Edit code on Mac (auto-reloads via HMR)
- Run `docker compose exec frontend npm install` for new packages
- Use `docker compose logs -f frontend` to debug
- Access frontend at http://localhost:3000

---

## 🚀 Getting Started NOW

```bash
# 1. Go to project root
cd /home/user/didonehousing

# 2. Clean up any Mac artifacts
cd frontend
rm -rf node_modules package-lock.json pnpm-lock.yaml .nuxt .output
cd ..

# 3. Start the frontend (and backend if not running)
docker compose up -d frontend backend

# 4. Watch the logs
docker compose logs -f frontend

# 5. Wait for:
# ✔ Vite server built in XXXms
# ✔ Nuxt built in XXXms
# ➜ Local:   http://localhost:3000/

# 6. Open browser
open http://localhost:3000
```

**That's it! No more npm bugs on Mac! 🎉**

---

## 📞 Still Have Issues?

If you have problems with the Docker approach:

1. Check Docker is running: `docker --version`
2. Check docker-compose: `docker compose version`
3. Post the output of: `docker compose logs frontend`
4. Check if port 3000 is free: `lsof -ti:3000`

This Docker approach is the **correct** way to develop with Nuxt 4 on macOS. Stop fighting with npm/pnpm on Mac and let Docker handle it! 🐳
