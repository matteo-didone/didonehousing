# Quick Start Guide - Aviano Housing Platform

## 🚀 Get Started in 2 Minutes

### Prerequisites

- Docker Desktop installed and running
- Git (for cloning the repo)

### One-Command Setup

```bash
./test-setup.sh
```

This script will:
1. ✅ Check Docker is running
2. ✅ Build all containers
3. ✅ Start all services
4. ✅ Run database migrations
5. ✅ Seed test data
6. ✅ Show you all access URLs

---

## 📋 Manual Setup (Step by Step)

### 1. Clone & Navigate

```bash
git clone <repository-url>
cd didonehousing
```

### 2. Copy Environment Files

```bash
cp .env.example backend/.env
cp frontend/.env.example frontend/.env
```

### 3. Start Services

```bash
docker-compose up -d
```

This will start:
- PostgreSQL + PostGIS
- Redis
- MinIO (S3 storage)
- Typesense (search)
- MailHog (email testing)
- Laravel Backend API
- Nuxt Frontend
- Laravel Horizon (queue worker)

### 4. Run Migrations & Seed Database

```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

Expected output:
```
✅ Created roles: admin, tenant, landlord, ho, vendor
✅ Created 95+ permissions
✅ Created 5 development users
```

---

## 🌐 Access Your Application

### URLs

| Service | URL | Description |
|---------|-----|-------------|
| **Frontend** | http://localhost:3000 | Nuxt 3 SPA |
| **Backend API** | http://localhost:8000 | Laravel 11 API |
| **API Health** | http://localhost:8000/up | Health check |
| **API Docs** | http://localhost:8000/api/health | API status |
| **MailHog** | http://localhost:8025 | Email testing UI |
| **MinIO Console** | http://localhost:9001 | S3 storage admin |
| **Horizon Dashboard** | http://localhost:8000/horizon | Queue monitoring |

### Test Users

All users have password: `password`

| Email | Role | Profile |
|-------|------|---------|
| `admin@avianohousing.local` | Admin | Full system access |
| `tenant@avianohousing.local` | Tenant | SSgt John Smith, 31st FW |
| `landlord@avianohousing.local` | Landlord | Marco Rossi, Rossi Immobiliare |
| `ho@avianohousing.local` | Housing Office | Sarah Johnson |
| `vendor@avianohousing.local` | Vendor | Giuseppe Bianchi |

---

## 🧪 Quick API Test

### Test Login

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tenant@avianohousing.local",
    "password": "password"
  }'
```

You should get a response with a `token`. Save it:

```bash
export TOKEN="your_token_here"
```

### Test Protected Route

```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

You should see the tenant's profile with:
- User details
- Role: `["tenant"]`
- Permissions: `["properties.view", "applications.create", ...]`
- Profile: `{ rank: "SSgt", branch: "Air Force", ... }`

---

## 📊 View Logs

### All services

```bash
docker-compose logs -f
```

### Specific service

```bash
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f horizon
```

---

## 🛠️ Common Commands

### Restart a Service

```bash
docker-compose restart backend
```

### Run Artisan Commands

```bash
docker-compose exec backend php artisan <command>
```

Examples:
```bash
# Run migrations
docker-compose exec backend php artisan migrate

# Create a new model
docker-compose exec backend php artisan make:model Property

# Clear cache
docker-compose exec backend php artisan cache:clear

# Inspect database
docker-compose exec backend php artisan tinker
```

### Run NPM Commands (Frontend)

```bash
docker-compose exec frontend npm <command>
```

Examples:
```bash
# Install a package
docker-compose exec frontend npm install axios

# Run tests
docker-compose exec frontend npm run test
```

### Access Database

```bash
docker-compose exec postgres psql -U aviano -d aviano_housing
```

Useful SQL commands:
```sql
-- List all users
SELECT id, first_name, last_name, email FROM users;

-- Check roles
SELECT * FROM roles;

-- See user roles
SELECT u.email, r.name as role
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON r.id = mhr.role_id;
```

---

## 🔄 Rebuild Containers

If you make changes to Dockerfile:

```bash
docker-compose up -d --build
```

---

## 🧹 Clean Up

### Stop services

```bash
docker-compose down
```

### Stop and remove volumes (⚠️ deletes data)

```bash
docker-compose down -v
```

### Remove everything and start fresh

```bash
docker-compose down -v
docker-compose up -d --build
docker-compose exec backend php artisan migrate:fresh --seed
```

---

## 🐛 Troubleshooting

### Issue: "Cannot connect to database"

**Solution:** Make sure PostgreSQL is ready:
```bash
docker-compose exec postgres pg_isready -U aviano
```

If not ready, wait a few seconds and try again.

### Issue: "Port already in use"

**Solution:** Stop other services using the same ports:
- 3000 (Frontend)
- 8000 (Backend)
- 5432 (PostgreSQL)
- 6379 (Redis)

Or change the ports in `docker-compose.yml`

### Issue: "Permission denied" on Mac

**Solution:** The mounted volumes need correct permissions:
```bash
docker-compose exec backend chown -R www-data:www-data /var/www/storage
docker-compose exec backend chown -R www-data:www-data /var/www/bootstrap/cache
```

### Issue: Frontend not updating

**Solution:** Clear Nuxt cache:
```bash
docker-compose exec frontend rm -rf .nuxt
docker-compose restart frontend
```

### Issue: "Composer autoload errors"

**Solution:** Regenerate autoload:
```bash
docker-compose exec backend composer dump-autoload
```

---

## 📚 Next Steps

1. ✅ **Verify Authentication** - Test all API endpoints (see `docs/API_TESTING.md`)
2. ✅ **Explore Database** - Check created users and permissions
3. ✅ **Build Frontend Auth Pages** - Login, Register, Profile
4. ✅ **Start Property Module** - Implement properties CRUD
5. ✅ **Add Tests** - Write PHPUnit and Playwright tests

---

## 📖 Documentation

- **Full API Testing Guide**: [docs/API_TESTING.md](docs/API_TESTING.md)
- **Development Roadmap**: [docs/NEXT_STEPS.md](docs/NEXT_STEPS.md)
- **Main README**: [README.md](README.md)

---

## 🆘 Need Help?

- Check the logs: `docker-compose logs -f`
- View running containers: `docker-compose ps`
- Inspect a container: `docker-compose exec backend sh`
- Read the docs: `docs/` folder

---

**Happy Coding! 🚀**

Authentication system is fully operational and ready for testing!
