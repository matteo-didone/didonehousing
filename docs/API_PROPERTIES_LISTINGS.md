# Properties & Listings API Testing Guide

## 🚀 Quick Setup

### 1. Start Services & Login

```bash
# Get auth token as landlord
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "landlord@avianohousing.local",
    "password": "password"
  }' | jq -r '.token')

echo "Token: $TOKEN"
```

---

## 📦 Properties API

### Base URL
```
http://localhost:8000/api/properties
```

---

### 1. List Properties (GET /properties)

**Landlord - See only own properties:**
```bash
curl http://localhost:8000/api/properties \
  -H "Authorization: Bearer $TOKEN" | jq
```

**With filters:**
```bash
# Filter by status
curl "http://localhost:8000/api/properties?filter[status]=approved" \
  -H "Authorization: Bearer $TOKEN" | jq

# Filter by city
curl "http://localhost:8000/api/properties?filter[city]=Aviano" \
  -H "Authorization: Bearer $TOKEN" | jq

# Pagination
curl "http://localhost:8000/api/properties?page=1&per_page=10" \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 2. Create Property (POST /properties)

**Landlord creates a new property:**
```bash
curl -X POST http://localhost:8000/api/properties \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "street_name": "Via Roma",
    "house_number": "42",
    "apt_number": "3A",
    "city": "Aviano",
    "province": "PN",
    "postal_code": "33081",
    "country": "IT",
    "bedrooms": 3,
    "bathrooms": 2,
    "living_rooms": 1,
    "kitchen": 1,
    "furnished": true,
    "pets_allowed": true,
    "heating_type": "city_gas",
    "cooling_type": "air_conditioning",
    "basement": false,
    "attic": false,
    "garage": true,
    "yard": true
  }' | jq
```

**Response:**
```json
{
  "message": "Property created successfully",
  "property": {
    "id": 1,
    "landlord_id": 3,
    "street_name": "Via Roma",
    "house_number": "42",
    "city": "Aviano",
    "province": "PN",
    "bedrooms": 3,
    "bathrooms": 2,
    "status": "draft",
    "created_at": "2025-11-06T10:00:00.000000Z",
    "landlord": {
      "id": 3,
      "first_name": "Marco",
      "last_name": "Rossi"
    }
  }
}
```

---

### 3. View Property (GET /properties/{id})

```bash
curl http://localhost:8000/api/properties/1 \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 4. Update Property (PATCH /properties/{id})

**Landlord updates their property:**
```bash
curl -X PATCH http://localhost:8000/api/properties/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "bedrooms": 4,
    "monthly_rent": 1500.00
  }' | jq
```

---

### 5. Submit Property for HO Review (POST /properties/{id}/submit)

**Change status: draft → pending_review**

```bash
curl -X POST http://localhost:8000/api/properties/1/submit \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response:**
```json
{
  "message": "Property submitted for review",
  "property": {
    "id": 1,
    "status": "pending_review",
    ...
  }
}
```

---

### 6. Approve Property (POST /properties/{id}/approve) - HO Only

**Login as HO first:**
```bash
HO_TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "ho@avianohousing.local",
    "password": "password"
  }' | jq -r '.token')
```

**Approve property:**
```bash
curl -X POST http://localhost:8000/api/properties/1/approve \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "comments": "Property meets all requirements. Approved."
  }' | jq
```

**Response:**
```json
{
  "message": "Property approved successfully",
  "property": {
    "id": 1,
    "status": "approved",
    "ho_reviewer_id": 4,
    "ho_reviewed_at": "2025-11-06T10:15:00.000000Z",
    "ho_comments": "Property meets all requirements. Approved."
  }
}
```

---

### 7. Reject Property (POST /properties/{id}/reject) - HO Only

```bash
curl -X POST http://localhost:8000/api/properties/1/reject \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "comments": "Missing cadastral documentation. Please resubmit with complete documents."
  }' | jq
```

---

### 8. Delete Property (DELETE /properties/{id})

**Only draft properties can be deleted:**
```bash
curl -X DELETE http://localhost:8000/api/properties/1 \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

## 📋 Listings API

### Base URL
```
http://localhost:8000/api/listings
```

---

### 1. List Listings (GET /listings)

**Landlord - See only own property listings:**
```bash
curl http://localhost:8000/api/listings \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Tenant - See only published listings:**
```bash
TENANT_TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "tenant@avianohousing.local",
    "password": "password"
  }' | jq -r '.token')

curl http://localhost:8000/api/listings \
  -H "Authorization: Bearer $TENANT_TOKEN" | jq
```

**With filters:**
```bash
# Filter by status
curl "http://localhost:8000/api/listings?filter[status]=published" \
  -H "Authorization: Bearer $TOKEN" | jq

# Sort by rent
curl "http://localhost:8000/api/listings?sort=monthly_rent" \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 2. Create Listing (POST /listings)

**Property must be approved first!**

```bash
curl -X POST http://localhost:8000/api/listings \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "property_id": 1,
    "monthly_rent": 1500.00,
    "security_deposit": 3000.00,
    "condo_fees": 150.00,
    "duration_years": 4,
    "checklist_data": {
      "has_photos": true,
      "has_floor_plan": true,
      "has_energy_certificate": true
    }
  }' | jq
```

**Response:**
```json
{
  "message": "Listing created successfully",
  "listing": {
    "id": 1,
    "property_id": 1,
    "monthly_rent": "1500.00",
    "security_deposit": "3000.00",
    "condo_fees": "150.00",
    "duration_years": 4,
    "status": "draft",
    "property": {
      "id": 1,
      "street_name": "Via Roma",
      "city": "Aviano"
    }
  }
}
```

---

### 3. View Listing (GET /listings/{id})

```bash
curl http://localhost:8000/api/listings/1 \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 4. Update Listing (PATCH /listings/{id})

**Only draft or rejected listings can be updated:**
```bash
curl -X PATCH http://localhost:8000/api/listings/1 \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "monthly_rent": 1600.00,
    "security_deposit": 3200.00
  }' | jq
```

---

### 5. Submit Listing for Review (POST /listings/{id}/submit)

**Change status: draft → submitted**

```bash
curl -X POST http://localhost:8000/api/listings/1/submit \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 6. Start Review (POST /listings/{id}/start-review) - HO Only

**Change status: submitted → in_review**

```bash
curl -X POST http://localhost:8000/api/listings/1/start-review \
  -H "Authorization: Bearer $HO_TOKEN" | jq
```

**Response:**
```json
{
  "message": "Review started",
  "listing": {
    "id": 1,
    "status": "in_review",
    "ho_reviewer_id": 4,
    "reviewed_at": "2025-11-06T10:30:00.000000Z"
  }
}
```

---

### 7. Approve Listing (POST /listings/{id}/approve) - HO Only

**Change status: in_review → approved**

```bash
curl -X POST http://localhost:8000/api/listings/1/approve \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "comments": "Listing approved. Pricing is reasonable."
  }' | jq
```

---

### 8. Reject Listing (POST /listings/{id}/reject) - HO Only

```bash
curl -X POST http://localhost:8000/api/listings/1/reject \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "comments": "Monthly rent exceeds OHA limits for this property type."
  }' | jq
```

---

### 9. Publish Listing (POST /listings/{id}/publish) - Landlord

**Can only publish approved listings**

```bash
curl -X POST http://localhost:8000/api/listings/1/publish \
  -H "Authorization: Bearer $TOKEN" | jq
```

**Response:**
```json
{
  "message": "Listing published successfully",
  "listing": {
    "id": 1,
    "status": "published",
    "published_at": "2025-11-06T10:45:00.000000Z"
  }
}
```

---

### 10. Unpublish Listing (POST /listings/{id}/unpublish) - Landlord

```bash
curl -X POST http://localhost:8000/api/listings/1/unpublish \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

### 11. Delete Listing (DELETE /listings/{id})

**Only draft listings can be deleted:**
```bash
curl -X DELETE http://localhost:8000/api/listings/1 \
  -H "Authorization: Bearer $TOKEN" | jq
```

---

## 🔄 Complete Workflow Test

### Property & Listing Complete Flow

```bash
# 1. Login as landlord
TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "landlord@avianohousing.local", "password": "password"}' \
  | jq -r '.token')

# 2. Create property
PROPERTY=$(curl -s -X POST http://localhost:8000/api/properties \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "street_name": "Via Pordenone",
    "house_number": "15",
    "city": "Aviano",
    "province": "PN",
    "postal_code": "33081",
    "country": "IT",
    "bedrooms": 3,
    "bathrooms": 2,
    "furnished": true,
    "pets_allowed": true,
    "heating_type": "city_gas"
  }')

PROPERTY_ID=$(echo $PROPERTY | jq -r '.property.id')
echo "Created Property ID: $PROPERTY_ID"

# 3. Submit property for review
curl -s -X POST "http://localhost:8000/api/properties/$PROPERTY_ID/submit" \
  -H "Authorization: Bearer $TOKEN" | jq '.message'

# 4. Login as HO
HO_TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "ho@avianohousing.local", "password": "password"}' \
  | jq -r '.token')

# 5. Approve property
curl -s -X POST "http://localhost:8000/api/properties/$PROPERTY_ID/approve" \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"comments": "Approved"}' | jq '.message'

# 6. Create listing (as landlord)
LISTING=$(curl -s -X POST http://localhost:8000/api/listings \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d "{
    \"property_id\": $PROPERTY_ID,
    \"monthly_rent\": 1500.00,
    \"security_deposit\": 3000.00,
    \"condo_fees\": 120.00,
    \"duration_years\": 4
  }")

LISTING_ID=$(echo $LISTING | jq -r '.listing.id')
echo "Created Listing ID: $LISTING_ID"

# 7. Submit listing for review
curl -s -X POST "http://localhost:8000/api/listings/$LISTING_ID/submit" \
  -H "Authorization: Bearer $TOKEN" | jq '.message'

# 8. HO starts review
curl -s -X POST "http://localhost:8000/api/listings/$LISTING_ID/start-review" \
  -H "Authorization: Bearer $HO_TOKEN" | jq '.message'

# 9. HO approves listing
curl -s -X POST "http://localhost:8000/api/listings/$LISTING_ID/approve" \
  -H "Authorization: Bearer $HO_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"comments": "Approved"}' | jq '.message'

# 10. Landlord publishes listing
curl -s -X POST "http://localhost:8000/api/listings/$LISTING_ID/publish" \
  -H "Authorization: Bearer $TOKEN" | jq '.message'

# 11. Tenant views published listing
TENANT_TOKEN=$(curl -s -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email": "tenant@avianohousing.local", "password": "password"}' \
  | jq -r '.token')

curl -s "http://localhost:8000/api/listings/$LISTING_ID" \
  -H "Authorization: Bearer $TENANT_TOKEN" | jq

echo "✅ Complete workflow executed successfully!"
```

---

## 📊 Status Flow Diagrams

### Property Status Flow
```
draft → pending_review → approved/rejected
                ↓
        (if rejected, back to draft for edits)
```

### Listing Status Flow
```
draft → submitted → in_review → approved/rejected
                                    ↓
                    (if approved) → published/unpublished
                    (if rejected) → back to draft
```

---

## 🔒 Permission Matrix

| Action | Landlord | Tenant | HO | Admin |
|--------|----------|--------|-----|-------|
| Create Property | ✅ (own) | ❌ | ❌ | ✅ |
| View Property | ✅ (own) | ❌ | ✅ (all) | ✅ (all) |
| Update Property | ✅ (own) | ❌ | ❌ | ✅ |
| Submit Property | ✅ (own) | ❌ | ❌ | ✅ |
| Approve/Reject Property | ❌ | ❌ | ✅ | ✅ |
| Create Listing | ✅ (own properties) | ❌ | ❌ | ✅ |
| View Listing | ✅ (own) | ✅ (published only) | ✅ (all) | ✅ (all) |
| Update Listing | ✅ (own) | ❌ | ❌ | ✅ |
| Submit Listing | ✅ (own) | ❌ | ❌ | ✅ |
| Approve/Reject Listing | ❌ | ❌ | ✅ | ✅ |
| Publish Listing | ✅ (own, if approved) | ❌ | ❌ | ✅ |

---

## 🐛 Troubleshooting

### "Property must be approved before creating a listing"
**Solution:** Submit property for HO review and get it approved first.

### "Unauthorized to view this property"
**Solution:** Landlords can only view their own properties. Use HO account to view all.

### "Only draft properties can be deleted"
**Solution:** Properties in review or approved cannot be deleted.

### "Can only update draft or rejected listings"
**Solution:** Withdraw listing (unpublish) before making changes.

---

## 🎯 Next Steps

Once properties and listings are working:
1. ✅ Test document uploads
2. ✅ Implement search & filtering
3. ✅ Add photo galleries
4. ✅ Create tenant application flow
5. ✅ Build frontend UI

---

**Properties & Listings API Ready for Testing! 🏠**
