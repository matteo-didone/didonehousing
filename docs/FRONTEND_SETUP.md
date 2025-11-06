# Frontend Setup & Testing Guide

## 🚀 Frontend Implementation Complete!

The Nuxt 3 frontend with full authentication system, layouts, and base UI has been implemented.

---

## ✅ What's Been Implemented

### Authentication System
- ✅ **Pinia Auth Store** - Complete state management
- ✅ **Login Page** - `/login`
- ✅ **Registration Page** - `/register` (Tenant, Landlord, Vendor)
- ✅ **Token Management** - Cookie-based, SSR-compatible
- ✅ **Auto-restore Auth** - Persists across page reloads
- ✅ **Role-based Redirects** - After login, redirects based on role

### Layouts
- ✅ **Default Layout** - Main app with navbar
  - Role-based navigation
  - Language switcher (EN/IT)
  - User menu with logout
  - Responsive design
- ✅ **Auth Layout** - Minimal for login/register
  - Centered card design
  - Language switcher

### Middleware
- ✅ **auth.ts** - Protects authenticated routes
- ✅ **guest.ts** - Redirects authenticated users from auth pages

### Pages
- ✅ **Homepage** (`/`) - Features overview
- ✅ **Login** (`/login`)
- ✅ **Register** (`/register`)
- ✅ **Landlord Dashboard** (`/landlord/dashboard`)

### TypeScript Support
- ✅ Full type definitions for User, Property, Listing
- ✅ Type-safe API calls
- ✅ Auto-complete in IDE

### Utilities
- ✅ **useAuth() composable** - Easy access to auth state
- ✅ **Permission helpers** - `can()`, `canAny()`, `canAll()`
- ✅ **Role checks** - `isLandlord`, `isTenant`, etc.

---

## 🧪 How to Test the Frontend

### ⚠️ Important: Use Docker!

**Don't run `npm install` on macOS!** Use Docker to avoid oxc-parser errors.

See **[QUICK_START_DOCKER.md](../QUICK_START_DOCKER.md)** for full guide.

### 1. Start Backend & Frontend with Docker

```bash
# In the root directory
docker compose up -d

# Setup database
docker compose exec backend php artisan migrate:fresh --seed

# Watch frontend logs
docker compose logs -f frontend
```

Verify backend is accessible:
```bash
curl http://localhost:8000/api/health
```

### 2. Access the Frontend

The frontend should now be available at: **http://localhost:3000**

**Why Docker?**
- ✅ No oxc-parser native binding errors (npm bug #4828 on macOS)
- ✅ Frontend runs in Linux container with correct bindings
- ✅ Same environment as production
- ✅ No need to install Node.js packages on Mac

<details>
<summary>Old approach (run locally on Mac) - May fail with oxc-parser errors</summary>

```bash
cd frontend
npm install  # ⚠️ May fail on macOS with oxc-parser error
npm run dev
```

If you get oxc-parser errors, use Docker instead!
</details>

---

## 📋 Testing Checklist

### Test 1: Homepage
1. ✅ Visit http://localhost:3000
2. ✅ You should see the homepage with features
3. ✅ Click "Get started" → Should redirect to `/register`
4. ✅ Click "Sign in" → Should redirect to `/login`

### Test 2: Registration Flow
1. ✅ Go to http://localhost:3000/register
2. ✅ Fill in the form:
   - First Name: Test
   - Last Name: User
   - Email: test@example.com
   - Phone: +39 123 456 789 (optional)
   - Role: Select "Landlord (Property Owner)"
   - Password: password123
   - Confirm Password: password123
   - Language: English
3. ✅ Click "Create account"
4. ✅ Should redirect to `/landlord/dashboard` after successful registration
5. ✅ You should see "Welcome back, Test!" on the dashboard

### Test 3: Logout
1. ✅ Click "Logout" in the navbar
2. ✅ Should redirect to `/login`
3. ✅ Auth state should be cleared

### Test 4: Login Flow
1. ✅ Go to http://localhost:3000/login
2. ✅ Enter credentials from API testing:
   - Email: `landlord@avianohousing.local`
   - Password: `password`
3. ✅ Click "Sign in"
4. ✅ Should redirect to `/landlord/dashboard`
5. ✅ Navbar should show "Marco Rossi" (landlord name)

### Test 5: Login with Different Roles

**Login as Tenant:**
```
Email: tenant@avianohousing.local
Password: password
```
- Should redirect to `/tenant/dashboard` (not implemented yet, will redirect to `/tenant/search`)

**Login as Housing Office:**
```
Email: ho@avianohousing.local
Password: password
```
- Should redirect to `/ho/dashboard` (not implemented yet)

### Test 6: Protected Routes
1. ✅ Logout if logged in
2. ✅ Try to access http://localhost:3000/landlord/dashboard
3. ✅ Should redirect to `/login` (middleware protection working)
4. ✅ Login again
5. ✅ Should now be able to access the dashboard

### Test 7: Guest Routes
1. ✅ Login as any user
2. ✅ Try to access http://localhost:3000/login
3. ✅ Should redirect to dashboard (guest middleware working)

### Test 8: Language Switcher
1. ✅ Click the language button in the navbar (🇬🇧 EN / 🇮🇹 IT)
2. ✅ Language should toggle
3. ✅ Texts should update (if translations are available)

### Test 9: Auth Persistence
1. ✅ Login as landlord
2. ✅ Refresh the page
3. ✅ Should remain logged in (auth restored from cookie)
4. ✅ Close the browser and reopen
5. ✅ Should still be logged in

### Test 10: Form Validation
1. ✅ Go to `/login`
2. ✅ Try to submit without filling fields
3. ✅ Browser validation should prevent submission
4. ✅ Try invalid email
5. ✅ Browser should show validation error
6. ✅ Try with wrong credentials
7. ✅ Should show error message: "The provided credentials are incorrect."

---

## 🎨 What You Should See

### Homepage
```
┌─────────────────────────────────────────────────┐
│  Aviano Housing                    🇬🇧 EN Logout│
├─────────────────────────────────────────────────┤
│                                                 │
│     Welcome to Aviano Housing Platform          │
│                                                 │
│     All-in-one platform for off-base housing   │
│     management at Aviano Air Base               │
│                                                 │
│     [ Get started ]   [ Sign in ]               │
│                                                 │
│     Features (6 cards):                         │
│     🏠 Property Management                      │
│     📄 Bilingual Contracts                      │
│     💰 Payments                                 │
│     🔧 Maintenance                              │
│     🌍 Full I18n                                │
│     🔐 Security                                 │
└─────────────────────────────────────────────────┘
```

### Login Page
```
┌─────────────────────────────────────────────────┐
│              Aviano Housing                     │
│                                                 │
│      ┌─────────────────────────┐                │
│      │ Sign in to your account │                │
│      │                         │                │
│      │ Email address           │                │
│      │ [input field]           │                │
│      │                         │                │
│      │ Password                │                │
│      │ [input field]           │                │
│      │                         │                │
│      │    [ Sign in ]          │                │
│      │                         │                │
│      │ Don't have an account?  │                │
│      │ Sign up                 │                │
│      └─────────────────────────┘                │
│                                                 │
│           🇮🇹 Italiano                          │
└─────────────────────────────────────────────────┘
```

### Landlord Dashboard
```
┌─────────────────────────────────────────────────┐
│  Aviano Housing  Dashboard  My Properties       │
│                           Marco Rossi [ Logout ]│
├─────────────────────────────────────────────────┤
│                                                 │
│  Landlord Dashboard                             │
│                                                 │
│  Welcome back, Marco!                           │
│  Manage your properties and listings from here. │
│                                                 │
│  Quick Actions                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐        │
│  │    ➕    │ │    🏠    │ │    📋    │        │
│  │ Add New  │ │    My    │ │    My    │        │
│  │ Property │ │Properties│ │ Listings │        │
│  └──────────┘ └──────────┘ └──────────┘        │
│                                                 │
│  Overview                                       │
│  ┌────┐ ┌────┐ ┌────┐ ┌────┐                  │
│  │ 0  │ │ 0  │ │ 0  │ │ 0  │                  │
│  │Tot │ │Pub │ │Pen │ │Act │                  │
│  └────┘ └────┘ └────┘ └────┘                  │
│                                                 │
│  Recent Activity                                │
│  No recent activity                             │
└─────────────────────────────────────────────────┘
```

---

## 🐛 Common Issues & Solutions

### Issue: "Cannot find module '@nuxtjs/i18n'"
**Solution:**
```bash
cd frontend
npm install
```

### Issue: "401 Unauthorized" when logging in
**Solution:** Make sure backend is running:
```bash
docker-compose up -d
curl http://localhost:8000/api/health
```

### Issue: "CORS error"
**Solution:** Backend should already have CORS configured. If not, check `backend/config/cors.php`

### Issue: Page doesn't redirect after login
**Solution:** Check browser console for errors. Make sure auth store is working:
- Open DevTools → Vue DevTools
- Check Pinia store → auth
- Verify `isAuthenticated: true` after login

### Issue: "hydration mismatch" warning
**Solution:** This can happen with SSR. Usually harmless in development. Check:
- Make sure auth state is restored correctly
- Clear browser localStorage and cookies
- Restart dev server

---

## 📊 Browser DevTools Debugging

### Check Auth State (Pinia)
1. Open Vue DevTools
2. Go to Pinia tab
3. Look at `auth` store
4. Should see:
   ```json
   {
     "user": { ... },
     "token": "1|abc123...",
     "isAuthenticated": true,
     "loading": false
   }
   ```

### Check Cookies
1. Open DevTools → Application → Cookies
2. Look for `auth_token` cookie
3. Should have the auth token value

### Check LocalStorage
1. Open DevTools → Application → Local Storage
2. Look for `auth` key (Pinia persistence)
3. Should contain serialized auth state

### Network Tab
1. Watch API calls to `/api/auth/login`
2. Should return 200 with user data and token
3. Subsequent requests should include `Authorization: Bearer ...` header

---

## 🚀 Next Steps

Now that authentication is working, you can:

1. **Add Property Management Pages**
   - Create Property form
   - Edit Property form
   - Property list view

2. **Add Listing Management**
   - Create Listing form
   - Listing workflow UI

3. **Add Housing Office Dashboard**
   - Pending reviews queue
   - Approve/reject interface

4. **Add Tenant Search**
   - Search listings
   - Filters (bedrooms, price, location)
   - Map view

5. **Add Document Upload**
   - Photo gallery
   - PDF upload
   - Document viewer

---

## 📝 Code Examples

### Using Auth in Components

```vue
<script setup>
const { user, isLandlord, can, logout } = useAuth()

// Check role
if (isLandlord.value) {
  // Show landlord-specific UI
}

// Check permission
if (can('properties.create')) {
  // Show create button
}

// Logout
const handleLogout = async () => {
  await logout()
}
</script>
```

### Protected Page

```vue
<script setup>
definePageMeta({
  middleware: 'auth',  // Require authentication
})
</script>
```

### Making API Calls

```vue
<script setup>
const config = useRuntimeConfig()
const { token } = useAuth()

const fetchProperties = async () => {
  const data = await $fetch('/properties', {
    baseURL: config.public.apiBase,
    headers: {
      Authorization: `Bearer ${token.value}`
    }
  })

  return data
}
</script>
```

---

**Frontend is ready for testing! 🎉**

Pull the latest changes and start the dev server to see it in action!
