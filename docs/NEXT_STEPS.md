# Next Steps - Aviano Housing Platform MVP

## ✅ Completed Setup

The foundation of the Aviano Housing Platform has been successfully set up:

### Infrastructure
- ✅ Monorepo structure (backend + frontend + infra)
- ✅ Docker Compose with all services (PostgreSQL+PostGIS, Redis, MinIO, Typesense, MailHog, Traefik)
- ✅ Laravel 11 backend with core packages installed
- ✅ Nuxt 3 frontend with TypeScript and all modules configured
- ✅ Development scripts (Makefile, setup.sh)
- ✅ GitHub Actions CI/CD workflow
- ✅ Full bilingual support (IT/EN) configured

### Packages & Libraries

**Backend (Laravel 11):**
- Laravel Sanctum & Passport (Auth)
- Spatie packages (Permission, Data, MediaLibrary, Query Builder, ActivityLog)
- Laravel Horizon (Queue monitoring)
- Doctrine DBAL

**Frontend (Nuxt 3):**
- Pinia (State management)
- Vue I18n (Internationalization)
- TailwindCSS + plugins
- VueUse (Utilities)
- Vee-Validate + Zod (Forms & validation)
- Radix-Vue & HeadlessUI (UI components)
- MapLibre GL (Maps)
- DayJS (Dates)

---

## 🎯 MVP Implementation Roadmap (Next 8-12 Weeks)

### Phase 1: Core Authentication & RBAC (Week 1-2)

#### Backend Tasks
1. **Database Schema - Users & Roles**
   - [ ] Create migrations for `users`, `roles`, `permissions`, `model_has_roles`, `model_has_permissions`
   - [ ] Add profile tables: `tenant_profiles`, `landlord_profiles`, `ho_profiles`, `vendor_profiles`
   - [ ] Seed default roles (Admin, Tenant, Landlord, HO, Vendor)
   - [ ] Implement 2FA/TOTP support

2. **Authentication API**
   - [ ] Implement Sanctum SPA authentication endpoints
   - [ ] Add Passport for third-party API access
   - [ ] Create registration/login/logout controllers
   - [ ] Add password reset functionality
   - [ ] Implement 2FA enforcement for HO/Admin roles

3. **RBAC & Policies**
   - [ ] Define permissions matrix (view/create/edit/approve/export per entity)
   - [ ] Implement Laravel policies for each model
   - [ ] Add middleware for role/permission checks
   - [ ] Create permission seeder

#### Frontend Tasks
1. **Auth Pages**
   - [ ] Login page with form validation
   - [ ] Registration page with role selection
   - [ ] Password reset flow
   - [ ] 2FA setup and verification pages

2. **Auth Store & Composables**
   - [ ] Create Pinia auth store
   - [ ] Implement useAuth composable
   - [ ] Add auth middleware for protected routes
   - [ ] Handle token refresh

3. **Layout & Navigation**
   - [ ] Main app layout with sidebar
   - [ ] Role-based navigation menu
   - [ ] User profile dropdown
   - [ ] Language switcher (IT/EN)

**Deliverable:** Working authentication system with role-based access control

---

### Phase 2: Property Management & HO Workflow (Week 3-4)

#### Backend Tasks
1. **Database Schema - Properties**
   - [ ] `properties` table (address, geo coordinates, APE, amenities)
   - [ ] `property_documents` (files, versions, HO approval status)
   - [ ] `listings` (pricing, policies, availability)
   - [ ] `property_features` (bedrooms, bathrooms, square meters)
   - [ ] Add PostGIS functions for distance calculations

2. **Property API**
   - [ ] CRUD endpoints for properties (landlord-scoped)
   - [ ] Property document upload with versioning
   - [ ] HO approval workflow endpoints
   - [ ] Listing status management
   - [ ] Search & filtering with Spatie Query Builder

3. **HO Review System**
   - [ ] Approval checklist model & migrations
   - [ ] Review comments system
   - [ ] Status transitions (draft → pending → approved/rejected)
   - [ ] Email notifications for status changes

#### Frontend Tasks
1. **Property CRUD**
   - [ ] Property listing page (landlord view)
   - [ ] Add/edit property form with wizard
   - [ ] Photo upload with preview
   - [ ] Document management interface
   - [ ] Map integration for property location

2. **HO Dashboard**
   - [ ] Pending approvals queue
   - [ ] Property review interface
   - [ ] Checklist verification UI
   - [ ] Comment/feedback system
   - [ ] Approval/rejection actions

**Deliverable:** Landlords can add properties; HO can review and approve listings

---

### Phase 3: Search, Matching & Applications (Week 5-6)

#### Backend Tasks
1. **Database Schema - Applications**
   - [ ] `applications` table (tenant, property, status, documents)
   - [ ] `saved_searches` (filters, notifications)
   - [ ] `property_visits` (scheduling)

2. **Search API**
   - [ ] Advanced search endpoint with filters
   - [ ] OHA calculator integration (EUR to USD)
   - [ ] Distance calculation to gates
   - [ ] Typesense integration for full-text search
   - [ ] Saved searches & alerts

3. **Application Flow**
   - [ ] Application submission endpoint
   - [ ] Document collection
   - [ ] Pre-screening questions
   - [ ] Landlord review & acceptance

#### Frontend Tasks
1. **Search Interface**
   - [ ] Property search page with filters
   - [ ] Map view with property markers
   - [ ] List/grid toggle
   - [ ] Save search functionality
   - [ ] OHA calculator widget

2. **Property Details**
   - [ ] Detailed property view
   - [ ] Photo gallery
   - [ ] Amenities list
   - [ ] Distance to gates display
   - [ ] Apply button & modal

3. **Application Management**
   - [ ] Application form
   - [ ] Document upload
   - [ ] Application status tracking
   - [ ] Landlord review interface

**Deliverable:** Tenants can search, filter, and apply for properties

---

### Phase 4: Contracts & E-Signature (Week 7-8)

#### Backend Tasks
1. **Database Schema - Leases**
   - [ ] `leases` table (property, tenant, dates, rent, currency)
   - [ ] `lease_documents` (contracts, annexes, inventory)
   - [ ] `contract_templates` (IT/EN bilingual)
   - [ ] `signatures` (tracking e-signature status)

2. **Contract Generation**
   - [ ] Template engine for bilingual contracts
   - [ ] Merge landlord/tenant/property data
   - [ ] Generate PDF with Browsershot
   - [ ] Store in S3/MinIO with signed URLs

3. **E-Signature Integration**
   - [ ] Namirial/DocuSign SDK integration
   - [ ] Send contract for signature
   - [ ] Webhook for signature completion
   - [ ] Store signed documents

4. **Registration Tracking**
   - [ ] Agenzia delle Entrate registration checklist
   - [ ] Document tracking (F23, F24)
   - [ ] Reminder notifications

#### Frontend Tasks
1. **Contract Workflow**
   - [ ] Contract preview before sending
   - [ ] Select clauses from library
   - [ ] Add custom clauses
   - [ ] Send for signature button

2. **Signing Interface**
   - [ ] Embedded e-signature flow
   - [ ] Signature status tracking
   - [ ] Download signed contract
   - [ ] Registration checklist UI

**Deliverable:** Generate bilingual contracts and collect e-signatures

---

### Phase 5: Payments & Invoicing (Week 9-10)

#### Backend Tasks
1. **Database Schema - Payments**
   - [ ] `invoices` & `invoice_items`
   - [ ] `payments` (method, amount, status, provider_ref)
   - [ ] `payment_methods` (stored cards, SEPA mandates)
   - [ ] `deposits` (escrow tracking)
   - [ ] `ledger_entries` (double-entry bookkeeping)
   - [ ] `exchange_rates` (EUR/USD)

2. **Payment Processing**
   - [ ] Stripe integration (one-time payments)
   - [ ] Invoice generation & PDF
   - [ ] Payment recording & reconciliation
   - [ ] Deposit management
   - [ ] Refund handling

3. **Accounting**
   - [ ] Automatic ledger entries
   - [ ] Landlord payout tracking
   - [ ] Tenant payment history
   - [ ] Fiscal reports (CSV/Excel export)

#### Frontend Tasks
1. **Payment Interface**
   - [ ] Invoice display
   - [ ] Payment method selection
   - [ ] Stripe checkout integration
   - [ ] Payment confirmation
   - [ ] Receipt download

2. **Landlord Dashboard**
   - [ ] Earnings overview
   - [ ] Pending/paid invoices
   - [ ] Payout history
   - [ ] Fiscal reports download

3. **Tenant Dashboard**
   - [ ] Payment history
   - [ ] Upcoming payments
   - [ ] Receipts download
   - [ ] Payment methods management

**Deliverable:** Accept one-time payments via Stripe; track deposits & invoices

---

### Phase 6: Maintenance Ticketing (Week 11)

#### Backend Tasks
1. **Database Schema - Maintenance**
   - [ ] `tickets` (category, priority, SLA, status)
   - [ ] `work_orders` (vendor, quote, invoice)
   - [ ] `ticket_messages` (communication thread)
   - [ ] `ticket_media` (photos/videos)

2. **Ticketing API**
   - [ ] Create ticket endpoint
   - [ ] SLA calculation
   - [ ] Assign to vendor
   - [ ] Status updates
   - [ ] Close ticket with resolution

3. **Notifications**
   - [ ] Email notifications for new tickets
   - [ ] SLA breach alerts
   - [ ] Status change notifications

#### Frontend Tasks
1. **Ticket Management**
   - [ ] Create ticket form with photos
   - [ ] Ticket list with filters
   - [ ] Ticket detail view
   - [ ] Status updates
   - [ ] Communication thread

2. **Vendor Interface**
   - [ ] Assigned tickets list
   - [ ] Accept/reject work order
   - [ ] Submit quote
   - [ ] Mark as completed
   - [ ] Upload invoice

**Deliverable:** Tenants can create maintenance tickets; landlords can assign vendors

---

### Phase 7: Documents & Dashboards (Week 12)

#### Backend Tasks
1. **Document Management**
   - [ ] Folder structure per property/lease
   - [ ] Version control
   - [ ] Access permissions
   - [ ] Expiry tracking
   - [ ] Watermarking

2. **Reporting API**
   - [ ] Landlord KPIs (occupancy, days on market, earnings)
   - [ ] Tenant dashboard data
   - [ ] HO quality metrics
   - [ ] Export endpoints

#### Frontend Tasks
1. **Document Center**
   - [ ] Document browser
   - [ ] Upload/download
   - [ ] Version history
   - [ ] Sharing controls

2. **Dashboards**
   - [ ] Landlord: occupancy, revenue, tickets
   - [ ] Tenant: payments, documents, tickets
   - [ ] HO: approvals pipeline, quality metrics
   - [ ] Admin: system stats

**Deliverable:** Complete MVP with dashboards and document management

---

## 🔄 Post-MVP (Phase 2) - Weeks 13-20

### Recurring Payments
- [ ] GoCardless integration for SEPA Direct Debit
- [ ] Recurring invoice generation
- [ ] Failed payment handling

### Advanced Features
- [ ] Check-in/check-out workflows with condition reports
- [ ] Contract registration assistance
- [ ] Fiscal reports for landlords
- [ ] PWA for mobile

### Integrations
- [ ] DeepL/Azure Translator for automatic translations
- [ ] OpenRouteService for travel time calculations
- [ ] SMS notifications via Twilio

---

## 📋 Immediate Next Steps

### 1. Run Initial Setup
```bash
./infra/scripts/setup.sh
# or
make setup && make install
```

### 2. Start Development
```bash
make up
```

### 3. Begin with Phase 1
Start implementing authentication and RBAC as outlined above.

### 4. Create Database Migrations
```bash
cd backend
php artisan make:migration create_users_and_profiles_tables
php artisan make:migration create_roles_and_permissions_tables
```

### 5. Build First API Endpoints
Create controllers:
```bash
php artisan make:controller Api/AuthController
php artisan make:controller Api/UserController
```

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Nuxt 3 Documentation](https://nuxt.com/docs)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission)
- [Vue I18n](https://vue-i18n.intlify.dev/)
- [TailwindCSS](https://tailwindcss.com/docs)

---

## 🆘 Need Help?

- Check the main [README.md](../README.md) for setup instructions
- Review [docker-compose.yml](../docker-compose.yml) for service configuration
- See [Makefile](../Makefile) for available commands
- Consult [API documentation](./api.md) once endpoints are implemented

---

**Happy Coding! 🚀**
