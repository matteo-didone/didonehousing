# API Testing Guide - Aviano Housing Platform

## 🚀 Quick Start

### 1. Start the Development Environment

```bash
# Start all Docker services
docker-compose up -d

# Check services are running
docker-compose ps
```

### 2. Run Migrations and Seed Database

```bash
# Fresh migration with seeders
docker-compose exec backend php artisan migrate:fresh --seed
```

Expected output:
```
✅ Created roles: admin, tenant, landlord, ho, vendor
✅ Created 95+ permissions
✅ Created 5 development users
```

---

## 👥 Test Users

All users have password: `password`

| Email | Role | Profile |
|-------|------|---------|
| `admin@avianohousing.local` | Admin | Full system access |
| `tenant@avianohousing.local` | Tenant | SSgt John Smith, Air Force, 31st FW |
| `landlord@avianohousing.local` | Landlord | Marco Rossi, Rossi Immobiliare SRL |
| `ho@avianohousing.local` | Housing Office | Sarah Johnson, AHO-001 |
| `vendor@avianohousing.local` | Vendor | Giuseppe Bianchi, Plumbing & Heating |

---

## 📡 API Endpoints

### Base URL
```
http://localhost:8000/api
```

---

## Authentication Endpoints

### 1. Register (Public)

**POST** `/auth/register`

**Request:**
```json
{
  "first_name": "Mario",
  "last_name": "Bianchi",
  "email": "mario@example.com",
  "phone": "+39 333 1234567",
  "password": "SecurePass123!",
  "password_confirmation": "SecurePass123!",
  "locale": "it",
  "role": "landlord",
  "profile": {
    "company_name": "Bianchi Properties",
    "tax_id": "IT12345678901",
    "business_type": "company",
    "city": "Aviano",
    "province": "PN"
  }
}
```

**Response (201):**
```json
{
  "message": "Registration successful",
  "user": {
    "id": 6,
    "first_name": "Mario",
    "last_name": "Bianchi",
    "full_name": "Mario Bianchi",
    "email": "mario@example.com",
    "locale": "it",
    "roles": ["landlord"],
    "permissions": ["properties.create", "..."],
    "profile": { ... }
  },
  "token": "1|abc123..."
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "first_name": "Mario",
    "last_name": "Bianchi",
    "email": "mario@example.com",
    "password": "SecurePass123!",
    "password_confirmation": "SecurePass123!",
    "locale": "it",
    "role": "landlord"
  }'
```

---

### 2. Login (Public)

**POST** `/auth/login`

**Request:**
```json
{
  "email": "tenant@avianohousing.local",
  "password": "password"
}
```

**Response (200):**
```json
{
  "message": "Login successful",
  "user": {
    "id": 2,
    "first_name": "John",
    "last_name": "Smith",
    "full_name": "John Smith",
    "email": "tenant@avianohousing.local",
    "phone": "+1 555 0100",
    "locale": "en",
    "two_factor_enabled": false,
    "last_login_at": "2025-11-04T22:30:15.000000Z",
    "roles": ["tenant"],
    "permissions": ["properties.view", "applications.create", "..."],
    "profile": {
      "id": 1,
      "rank": "SSgt",
      "branch": "Air Force",
      "unit": "31st Fighter Wing",
      "family_size": 4,
      "has_pets": true,
      "pet_details": "1 dog (Labrador, 30kg)",
      "oha_amount": "2500.00",
      "oha_currency": "USD"
    }
  },
  "token": "2|xyz789..."
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tenant@avianohousing.local",
    "password": "password"
  }'
```

**Save the token:**
```bash
export TOKEN="your_token_here"
```

---

### 3. Get Authenticated User (Protected)

**GET** `/auth/me`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
  "user": {
    "id": 2,
    "first_name": "John",
    "last_name": "Smith",
    "full_name": "John Smith",
    "email": "tenant@avianohousing.local",
    "roles": ["tenant"],
    "permissions": ["properties.view", "..."],
    "profile": { ... }
  }
}
```

**cURL:**
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

---

### 4. Logout (Protected)

**POST** `/auth/logout`

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
  "message": "Logged out successfully"
}
```

**cURL:**
```bash
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $TOKEN"
```

---

## Profile Endpoints (All Protected)

### 5. Get Profile

**GET** `/profile`

**cURL:**
```bash
curl http://localhost:8000/api/profile \
  -H "Authorization: Bearer $TOKEN"
```

---

### 6. Update Profile

**PATCH** `/profile`

**Request:**
```json
{
  "first_name": "John",
  "last_name": "Smith Jr",
  "phone": "+1 555 0200",
  "locale": "en"
}
```

**cURL:**
```bash
curl -X PATCH http://localhost:8000/api/profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "+1 555 0200"
  }'
```

---

### 7. Update Password

**PATCH** `/profile/password`

**Request:**
```json
{
  "current_password": "password",
  "password": "NewSecurePass123!",
  "password_confirmation": "NewSecurePass123!"
}
```

**cURL:**
```bash
curl -X PATCH http://localhost:8000/api/profile/password \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "current_password": "password",
    "password": "NewSecurePass123!",
    "password_confirmation": "NewSecurePass123!"
  }'
```

---

### 8. Update Role-Specific Profile

**PATCH** `/profile/role-profile`

**Request (Tenant):**
```json
{
  "rank": "TSgt",
  "family_size": 5,
  "has_pets": false,
  "oha_amount": 2800.00
}
```

**Request (Landlord):**
```json
{
  "company_name": "Updated Properties SRL",
  "cedolare_secca": true,
  "bank_name": "UniCredit",
  "iban": "IT60X..."
}
```

**cURL:**
```bash
curl -X PATCH http://localhost:8000/api/profile/role-profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "family_size": 5,
    "has_pets": false
  }'
```

---

## Health Check

**GET** `/health`

**Response:**
```json
{
  "status": "ok",
  "timestamp": "2025-11-04T22:35:00.000000Z"
}
```

**cURL:**
```bash
curl http://localhost:8000/api/health
```

---

## 🧪 Complete Test Flow

### Test Scenario: Tenant Login & Profile Update

```bash
# 1. Login as tenant
RESPONSE=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tenant@avianohousing.local",
    "password": "password"
  }')

# Extract token (requires jq)
TOKEN=$(echo $RESPONSE | jq -r '.token')

echo "Token: $TOKEN"

# 2. Get current user
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN" | jq

# 3. Update profile
curl -X PATCH http://localhost:8000/api/profile/role-profile \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "family_size": 5,
    "special_requirements": "Ground floor preferred"
  }' | jq

# 4. Logout
curl -X POST http://localhost:8000/api/auth/logout \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

## 🔒 Testing Permissions

### Admin Access (All Permissions)

```bash
# Login as admin
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@avianohousing.local",
    "password": "password"
  }'
```

Admin has all 95+ permissions and can access everything.

### Tenant Permissions

Tenants can:
- ✅ View properties and listings
- ✅ Create applications
- ✅ View their own leases and payments
- ✅ Create maintenance tickets
- ❌ Cannot manage properties
- ❌ Cannot approve anything

### Landlord Permissions

Landlords can:
- ✅ Manage their own properties
- ✅ Review and approve tenant applications
- ✅ Create leases and invoices
- ❌ Cannot view all properties (only their own)
- ❌ Cannot approve leases (HO only)

### Housing Office Permissions

HO staff can:
- ✅ View all properties and listings
- ✅ Approve/reject properties
- ✅ Approve/reject leases
- ✅ View all payments and disputes
- ❌ Cannot create properties
- ❌ Cannot process payments directly

---

## 🐛 Troubleshooting

### Issue: "could not translate host name postgres"

**Solution:** Start Docker services first:
```bash
docker-compose up -d
```

### Issue: "Token not found" or "Unauthenticated"

**Solution:** Check token is valid:
```bash
curl http://localhost:8000/api/auth/me \
  -H "Authorization: Bearer $TOKEN"
```

### Issue: "The provided credentials are incorrect"

**Solution:** Use test user credentials from table above. All passwords are: `password`

### Issue: Database not seeded

**Solution:** Run migrations with seed:
```bash
docker-compose exec backend php artisan migrate:fresh --seed
```

---

## 📊 Database Inspection

### Check created users:

```bash
docker-compose exec backend php artisan tinker
```

```php
User::with('roles')->get();
User::find(2)->isTenant(); // true
User::find(2)->getAllPermissions()->pluck('name');
```

### Check roles and permissions:

```php
Role::with('permissions')->get();
Permission::all()->pluck('name');
```

---

## 🎯 Next Steps

Once authentication is working:

1. ✅ Test all 5 user roles
2. ✅ Verify permissions are enforced
3. ✅ Test profile updates for each role
4. ✅ Implement 2FA (next feature)
5. ✅ Build frontend auth pages
6. ✅ Start Property Management module

---

**Authentication System Fully Operational! 🔐**
