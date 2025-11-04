# Aviano Rental Platform MVP

**All-in-one platform for off-base housing management at Aviano Air Base**

## 🎯 Overview

Enterprise-grade rental management platform for US military housing at Aviano AB, handling listings, bilingual contracts (IT/EN), payments, maintenance, compliance, and Housing Office workflows.

### Key Features

- 🏠 **Property Management**: Complete listing lifecycle with HO approval workflows
- 📄 **Bilingual Contracts**: IT/EN templates with eIDAS e-signature
- 💰 **Payments**: SEPA DD/CT, cards (Stripe/GoCardless), deposit escrow
- 🔧 **Maintenance**: Ticketing system with SLA, vendor management
- 📊 **Accounting**: Double-entry ledger, invoicing, fiscal reports
- 🌍 **I18n**: Full Italian/English support with professional translations
- 🔐 **Security**: RBAC, 2FA, GDPR-compliant, full audit trail

## 🏗️ Architecture

**Monorepo structure:**

```
├── backend/          # Laravel 11 API (PHP 8.3)
├── frontend/         # Nuxt 3 SPA (Vue 3 + TypeScript)
├── infra/           # Docker, Terraform, deployment scripts
├── docs/            # Technical documentation
└── .github/         # CI/CD workflows
```

## 🚀 Tech Stack

### Backend
- **Framework**: Laravel 11 (PHP 8.3)
- **Database**: PostgreSQL 16 + PostGIS
- **Cache/Queue**: Redis + Horizon
- **Storage**: S3-compatible (MinIO dev, S3/Wasabi prod)
- **Search**: Typesense + PostgreSQL full-text
- **Auth**: Sanctum (SPA) + Passport (API) + 2FA TOTP
- **RBAC**: spatie/laravel-permission
- **Payments**: Stripe, GoCardless
- **E-signature**: Namirial/DocuSign EU (eIDAS)
- **PDF**: Browsershot (Puppeteer)
- **Translations**: DeepL API + Azure fallback

### Frontend
- **Framework**: Nuxt 3 + Vue 3 + TypeScript
- **State**: Pinia
- **I18n**: Vue I18n (namespace-based)
- **UI**: TailwindCSS + Headless UI + Radix-Vue
- **Forms**: VeeValidate
- **Maps**: MapLibre GL + OpenRouteService
- **PWA**: Nuxt PWA + Capacitor (iOS/Android)

### Infrastructure
- **Containers**: Docker + Docker Compose
- **Proxy**: Traefik (TLS/ACME)
- **Observability**: OpenTelemetry, Sentry, ELK/Grafana
- **CI/CD**: GitHub Actions
- **IaC**: Terraform (Hetzner/OVH EU)

## 📋 Prerequisites

- Docker & Docker Compose (v2.x)
- Node.js 20.x LTS
- PHP 8.3 + Composer 2.x
- Make (optional, for shortcuts)

## 🛠️ Quick Start

### 1. Clone and Setup

```bash
git clone <repository-url>
cd didonehousing
```

### 2. Environment Configuration

```bash
# Copy environment files
cp backend/.env.example backend/.env
cp frontend/.env.example frontend/.env

# Generate application keys
docker-compose run --rm backend php artisan key:generate
```

### 3. Start Services

```bash
# Start all services
docker-compose up -d

# Install dependencies
docker-compose exec backend composer install
docker-compose exec frontend npm install

# Run migrations
docker-compose exec backend php artisan migrate --seed
```

### 4. Access Applications

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost:8000
- **API Docs**: http://localhost:8000/docs
- **Horizon**: http://localhost:8000/horizon
- **MinIO Console**: http://localhost:9001
- **MailHog**: http://localhost:8025

### Default Credentials (Dev)

```
Admin:    admin@aviano.local / password
Landlord: landlord@test.local / password
Tenant:   tenant@test.local / password
HO:       ho@aviano.local / password
```

## 🏃 Development

### Backend (Laravel)

```bash
# Enter backend container
docker-compose exec backend bash

# Run migrations
php artisan migrate

# Run tests
php artisan test

# Run code style fixer
./vendor/bin/pint

# Generate OpenAPI docs
php artisan l5-swagger:generate
```

### Frontend (Nuxt)

```bash
# Enter frontend container
docker-compose exec frontend sh

# Run dev server
npm run dev

# Build for production
npm run build

# Run tests
npm run test

# Type check
npm run typecheck

# Lint
npm run lint
```

## 🗄️ Database Schema

Key entities:
- **Users & Profiles**: Multi-role authentication
- **Properties & Listings**: Property catalog with HO approval
- **Leases & Contracts**: Bilingual contracts with e-signature
- **Invoices & Payments**: Full accounting with ledger
- **Tickets & Work Orders**: Maintenance management
- **Documents**: Versioned, encrypted storage
- **Messages & Notifications**: Multi-channel communication

See [docs/database-schema.md](docs/database-schema.md) for details.

## 🔐 Security

- ✅ 2FA/TOTP mandatory for HO/Admin roles
- ✅ Row-level security with policies
- ✅ Encrypted file storage with signed URLs
- ✅ CSP headers, rate limiting, WAF-ready
- ✅ GDPR-compliant (consent, DPA, right to be forgotten)
- ✅ Immutable audit log for all operations

## 🌍 Localization

Full bilingual support (IT/EN):
- UI strings with Vue I18n
- Database content (contracts, documents)
- Automatic translation with DeepL API
- HO approval workflow for legal translations

## 📊 Observability

- **Logs**: Structured JSON → ELK/EFK stack
- **Metrics**: Prometheus + Grafana dashboards
- **Tracing**: OpenTelemetry → Tempo
- **Errors**: Sentry integration
- **Uptime**: Status page with UptimeRobot/Freshping

## 🚢 Deployment

### Staging/Production

```bash
# Build images
docker-compose -f docker-compose.prod.yml build

# Deploy with zero-downtime
./infra/scripts/deploy.sh production
```

See [docs/deployment.md](docs/deployment.md) for full guide.

## 📚 Documentation

- [API Documentation](docs/api.md) - OpenAPI/Swagger specs
- [Database Schema](docs/database-schema.md) - Entity models
- [User Roles & Permissions](docs/rbac.md) - RBAC matrix
- [Payment Flows](docs/payments.md) - Stripe/GoCardless integration
- [E-signature Integration](docs/esignature.md) - Namirial/DocuSign
- [Deployment Guide](docs/deployment.md) - Infrastructure setup
- [Contributing](docs/contributing.md) - Development guidelines

## 🎯 Roadmap

### MVP (Current - 12 weeks)
- ✅ Auth + RBAC + 2FA
- ✅ Property CRUD + HO approval workflow
- ✅ Search & matching with OHA calculator
- ✅ Bilingual contracts + e-signature
- ✅ Document management
- ✅ One-time payments (Stripe)
- ✅ Basic ticketing system
- ✅ Role-based dashboards

### Phase 2
- 🔄 Recurring payments (SEPA DD via GoCardless)
- 🔄 Deposit escrow with movements
- 🔄 SLA tracking + vendor marketplace
- 🔄 Advanced check-in/check-out
- 🔄 Contract registration assistance
- 🔄 Fiscal reports for landlords
- 🔄 PWA + mobile apps

### Phase 3
- 📅 TLA/LQA reimbursement workflows
- 📅 ML-based property recommendations
- 📅 Utility switch automation
- 📅 Direct HO integrations
- 📅 Advanced analytics & BI

## 🤝 Contributing

Please read [CONTRIBUTING.md](docs/contributing.md) for development guidelines.

## 📄 License

Proprietary - All Rights Reserved

## 🆘 Support

- **Issues**: GitHub Issues
- **Email**: dev@avianohousing.local
- **Docs**: https://docs.avianohousing.local

---

**Built with ❤️ for Aviano Air Base community**
