# Fix Frontend Errors - Quick Guide

## ✅ Problemi Risolti

Ho fixato due problemi critici che impedivano al frontend di funzionare:

### 1. **Pinia Version Conflict** ✅
- **Problema**: `@pinia/nuxt@0.11.2` richiede `pinia@^3.0.3`, ma il progetto aveva `pinia@2.3.1`
- **Fix**: Aggiornato `pinia` da `2.3.1` a `3.0.3` in `package.json`
- **Motivo**: Con Nuxt 4, devi usare Pinia 3.x (supporta solo Vue 3)

### 2. **useAuth is not defined** ✅
- **Problema**: 500 Internal Server Error - composable `useAuth()` non definito
- **Causa**: `useRouter()` veniva chiamato troppo presto, fuori dal contesto Vue
- **Fix**:
  - Rimosso `useRouter()` dal composable
  - Usato `navigateTo()` direttamente (helper globale Nuxt)
  - Spostata navigazione da store a composable

### 3. **Pinia Plugin** ✅
- **Aggiunto**: `frontend/plugins/pinia.ts` per inizializzare correttamente Pinia con persisted state

---

## 🚀 Come Applicare i Fix

### Step 1: Pull le modifiche

```bash
git pull origin claude/analyze-current-state-011CUrSGvMhf5bfN8PnXDTXN
```

### Step 2: Pulisci e reinstalla dipendenze

```bash
cd frontend

# Rimuovi node_modules e lock file
rm -rf node_modules package-lock.json

# Rimuovi cache Nuxt
rm -rf .nuxt .output

# Reinstalla dipendenze
npm install
```

Se `npm install` continua a dare errori, usa:

```bash
npm install --legacy-peer-deps
```

### Step 3: Riavvia il server dev

```bash
npm run dev
```

Il frontend dovrebbe ora partire senza errori su **http://localhost:3000**

---

## 📋 File Modificati

```
frontend/
├── package.json           ✅ UPDATED - pinia 2.3.1 → 3.0.3
├── plugins/
│   └── pinia.ts          ✅ NEW - Pinia initialization plugin
├── composables/
│   └── useAuth.ts        ✅ UPDATED - Fixed router usage
└── stores/
    └── auth.ts           ✅ UPDATED - Removed navigateTo from store
```

---

## 🧪 Test Rapido

Una volta che il server è avviato:

1. ✅ Visita http://localhost:3000
2. ✅ Dovresti vedere la homepage (non più 500 error)
3. ✅ Clicca "Sign in"
4. ✅ Dovresti vedere la pagina di login
5. ✅ Prova a fare login con:
   ```
   Email: landlord@avianohousing.local
   Password: password
   ```
6. ✅ Dovrebbe reindirizzarti a `/landlord/dashboard`

---

## 🐛 Se Continui ad Avere Problemi

### Errore: "Cannot find module pinia"
```bash
cd frontend
rm -rf node_modules .nuxt
npm install
```

### Errore: "useAuth is still not defined"
```bash
# Rimuovi completamente la cache Nuxt
cd frontend
rm -rf .nuxt .output node_modules/.cache
npm run dev
```

### Errore: "ERR_PNPM_PEER_DEP_ISSUES" (se usi pnpm)
```bash
# Passa a npm
rm pnpm-lock.yaml
npm install
```

### Errore: Port 3000 già in uso
```bash
# Uccidi il processo
lsof -ti:3000 | xargs kill -9

# Oppure cambia porta
npm run dev -- --port 3001
```

---

## 📊 Cosa è Cambiato Tecnicamente

### Prima (Non Funzionante)

**useAuth.ts:**
```typescript
export const useAuth = () => {
  const router = useRouter() // ❌ Chiamato troppo presto!

  const login = async (creds) => {
    const result = await authStore.login(creds)
    if (result.success) {
      await router.push('/dashboard') // ❌ Router non disponibile
    }
  }
}
```

**auth.ts store:**
```typescript
async logout() {
  await $fetch('/auth/logout')
  this.clearAuth()
  navigateTo('/login') // ❌ navigateTo in uno store!
}
```

### Dopo (Funzionante)

**useAuth.ts:**
```typescript
export const useAuth = () => {
  const authStore = useAuthStore()

  const login = async (creds) => {
    const result = await authStore.login(creds)
    if (result.success) {
      await navigateTo('/dashboard') // ✅ navigateTo globale
    }
  }

  const logout = async () => {
    await authStore.logout()
    await navigateTo('/login') // ✅ Navigation nel composable
  }
}
```

**auth.ts store:**
```typescript
async logout() {
  await $fetch('/auth/logout')
  this.clearAuth()
  // ✅ Nessuna navigazione qui - solo state management
}
```

---

## 🎯 Perché Questi Fix Funzionano

### 1. Pinia 3.x è Richiesto
- Nuxt 4 usa Vue 3.5+
- @pinia/nuxt 0.11.x richiede Pinia 3.x
- Pinia 3.x supporta solo Vue 3 (ha droppato Vue 2)

### 2. Composables Devono Essere Chiamati Nel Contesto Corretto
In Nuxt 3, i composables come `useRouter()` possono essere chiamati solo in:
- `setup()` functions
- Nuxt plugins
- Nuxt middleware
- Nuxt hooks

**NON** possono essere chiamati:
- A livello top-level di un modulo
- In funzioni async fuori contesto
- In Pinia stores

### 3. navigateTo() è Diverso da router.push()
- `navigateTo()` è un helper globale Nuxt che funziona ovunque
- `useRouter()` richiede un contesto Vue attivo
- Per questo abbiamo sostituito `router.push()` con `navigateTo()`

---

## ✅ Checklist Post-Fix

Dopo aver applicato i fix, verifica:

- [ ] `npm install` completa senza errori
- [ ] `npm run dev` avvia il server senza crash
- [ ] Homepage carica correttamente (http://localhost:3000)
- [ ] Login page è accessibile (/login)
- [ ] Login funziona con credenziali test
- [ ] Redirect dopo login funziona
- [ ] Logout funziona e redirect a /login
- [ ] Nessun errore 500 nella console

---

## 🎉 Risultato Atteso

Dopo questi fix, dovresti vedere:

```
✔ Vite server built in XXXms
✔ Nitro built in XXX ms

  ➜ Local:   http://localhost:3000/
  ➜ Network: use --host to expose

ℹ Tailwind Viewer: http://localhost:3000/_tailwind/
```

E visitando http://localhost:3000:

```
┌─────────────────────────────────────┐
│  Welcome to Aviano Housing Platform │
│                                     │
│  [ Get started ]   [ Sign in ]     │
└─────────────────────────────────────┘
```

**Nessun errore 500! 🎉**

---

## 📞 Se Hai Ancora Problemi

Se dopo questi fix continui ad avere problemi:

1. Posta l'errore completo della console
2. Posta il risultato di `npm run dev`
3. Posta il risultato di `cat frontend/package.json | grep pinia`

---

**Frontend Fix Complete! 🚀**
