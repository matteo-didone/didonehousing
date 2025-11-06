# Frontend Docker Rebuild - Fix oxc-parser Errors

## 🐛 Il Problema

Anche nel container Docker Linux, stai vedendo errori oxc-parser. Questo è inaspettato perché Docker dovrebbe usare i bindings Linux corretti.

**Root Cause**: Il volume Docker `frontend_node_modules` è stato contaminato durante i primi tentativi quando c'erano problemi di struttura file (app.vue nella posizione sbagliata, main.css commentato).

## ✅ Correzioni Applicate

Ho già corretto:
1. ✅ Spostato `app.vue` da `frontend/app/app.vue` → `frontend/app.vue` (posizione corretta Nuxt)
2. ✅ Decommentato `main.css` in `nuxt.config.ts`

Questi fix sono stati committati e pushati.

## 🔧 Soluzione: Rebuild Completo con Volumi Puliti

Devi **ricostruire completamente il container frontend** con volumi freschi:

```bash
# 1. Ferma il frontend container
docker compose stop frontend

# 2. Rimuovi il container
docker compose rm -f frontend

# 3. Rimuovi il volume node_modules contaminato
docker volume rm didonehousing_frontend_node_modules

# 4. Rimuovi anche il volume .nuxt cache (opzionale ma raccomandato)
docker volume rm didonehousing_frontend_nuxt

# 5. Ricostruisci e riavvia il frontend
docker compose up -d --build frontend

# 6. Guarda i log per verificare che funzioni
docker compose logs -f frontend
```

## 📊 Cosa Aspettarsi

Dopo il rebuild, dovresti vedere:

```
aviano_frontend  | [nuxi] Nuxt 4.2.0 (with Nitro 2.12.9, Vite 7.1.12 and Vue 3.5.22)
aviano_frontend  | ✔ Vite client built in XXms
aviano_frontend  | ✔ Vite server built in XXms
aviano_frontend  | [nitro] ✔ Nuxt Nitro server built in XXXXms
aviano_frontend  |
aviano_frontend  |   ➜ Local:    http://0.0.0.0:3000/
aviano_frontend  |   ➜ Network:  http://172.18.0.X:3000/
```

**NO errori oxc-parser!**
**NO errori "Cannot find module main.css"!**

## 🚀 Verifica

Una volta che il container è running senza errori:

```bash
# 1. Apri il browser
open http://localhost:3000

# 2. Dovresti vedere la homepage Nuxt con:
   - Login/Register links
   - Layouts funzionanti
   - Nessun errore 500
```

## 🔍 Se Ancora Non Funziona

Se dopo il rebuild ANCORA vedi errori:

### Opzione 1: Rebuild Totale di Tutti i Volumi

```bash
# Stop tutto
docker compose down

# Rimuovi TUTTI i volumi
docker volume ls | grep didonehousing
docker volume rm didonehousing_frontend_node_modules
docker volume rm didonehousing_frontend_nuxt

# Rebuild completo
docker compose build --no-cache frontend
docker compose up -d
```

### Opzione 2: Rebuild Senza Cache Docker

```bash
docker compose down
docker compose build --no-cache --pull frontend
docker compose up -d frontend
```

### Opzione 3: Verifica Dockerfile

Controlla che il Dockerfile del frontend usi npm install correttamente:

```dockerfile
# In frontend/Dockerfile linea 23
RUN npm install --legacy-peer-deps
```

## ⚠️ Importante

**NON:**
- ❌ Non fare `npm install` sul Mac
- ❌ Non copiare node_modules dal Mac al container
- ❌ Non montare node_modules dal Mac

**SEMPRE:**
- ✅ Usa Docker volumes per node_modules
- ✅ Lascia che npm install giri nel container Linux
- ✅ Rebuilda con volumi puliti se ci sono problemi

## 📝 Spiegazione Tecnica

### Perché il Volume era Contaminato?

1. Primo avvio del container con struttura file sbagliata:
   - `app.vue` era in `frontend/app/app.vue` invece di `frontend/app.vue`
   - `main.css` era commentato in `nuxt.config.ts`

2. Nuxt/Vite hanno provato a installare e buildare con questa configurazione rotta

3. Il volume `frontend_node_modules` ha memorizzato uno stato inconsistente:
   - Alcuni moduli installati parzialmente
   - Cache Vite corrotta
   - Bindings oxc-parser incompleti o wrong path

4. Anche dopo aver fixato i file, il container riusava il volume contaminato

### Come il Rebuild Risolve

```
Rebuild con volumi puliti:

  ┌──────────────────────────────────────┐
  │ 1. docker volume rm                  │  ← Cancella stato contaminato
  └──────────────────────────────────────┘
             ↓
  ┌──────────────────────────────────────┐
  │ 2. docker compose build --no-cache   │  ← Rebuild immagine da zero
  └──────────────────────────────────────┘
             ↓
  ┌──────────────────────────────────────┐
  │ 3. Container starts                  │
  │    - Fresh node_modules volume       │
  │    - Runs npm install in Linux       │
  │    - Downloads correct bindings      │
  │    - oxc-parser gets linux-x64-musl  │  ← Questo funziona!
  └──────────────────────────────────────┘
             ↓
  ┌──────────────────────────────────────┐
  │ 4. Nuxt builds successfully           │  ✅
  │    - All files in correct locations  │  ✅
  │    - All dependencies resolved       │  ✅
  │    - No oxc-parser errors            │  ✅
  └──────────────────────────────────────┘
```

## ✨ Risultato Finale

Dopo il rebuild:
- ✅ Frontend accessible at http://localhost:3000
- ✅ No oxc-parser errors
- ✅ No missing CSS errors
- ✅ Pages load correctly
- ✅ Layouts work
- ✅ Auth system functional

---

**Prossimo Passo**: Dopo che il frontend funziona, possiamo continuare con lo sviluppo delle feature (Property Management UI, HO Dashboard, etc.)!
