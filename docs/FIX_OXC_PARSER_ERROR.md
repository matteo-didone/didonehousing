# oxc-parser Error Fix Guide - macOS

## 🐛 Il Problema

```
ERROR  Cannot find native binding. npm has a bug related to optional dependencies
```

Questo è causato dal **npm bug #4828** che affligge le optional dependencies, specialmente su macOS con architetture ARM (M1/M2/M3).

---

## ✅ SOLUZIONE RACCOMANDATA: Usa pnpm

**pnpm non ha questo bug ed è più affidabile!**

### Step-by-Step con pnpm

```bash
# 1. Installa pnpm globalmente
npm install -g pnpm

# 2. Vai nella directory frontend
cd frontend

# 3. Pulisci tutto
rm -rf node_modules package-lock.json .nuxt .output

# 4. Installa con pnpm (creerà pnpm-lock.yaml)
pnpm install

# 5. Avvia
pnpm dev
```

### Comandi pnpm vs npm

| npm | pnpm |
|-----|------|
| `npm install` | `pnpm install` |
| `npm run dev` | `pnpm dev` |
| `npm run build` | `pnpm build` |
| `npm add package` | `pnpm add package` |

---

## 🔄 Alternative se Non Vuoi Usare pnpm

### Opzione 1: Pulisci TUTTO e Reinstalla

```bash
cd frontend

# Cancella tutto
rm -rf node_modules package-lock.json .nuxt .output

# Pulisci cache npm
npm cache clean --force

# Reinstalla
npm install

# Prova
npm run dev
```

### Opzione 2: Usa --force

```bash
cd frontend
rm -rf node_modules package-lock.json
npm install --force
npm run dev
```

### Opzione 3: Usa --legacy-peer-deps

```bash
cd frontend
rm -rf node_modules package-lock.json
npm install --legacy-peer-deps
npm run dev
```

### Opzione 4: Aggiorna Node.js

```bash
# Controlla versione
node --version

# Se < 22, aggiorna con nvm
nvm install 22
nvm use 22

# Poi reinstalla
cd frontend
rm -rf node_modules package-lock.json
npm install
```

---

## 🆘 Se NIENTE Funziona

### Ultimo Resort: Downgrade a Nuxt 3

Se oxc-parser continua a dare problemi, possiamo downgrade a Nuxt 3.17.5:

1. Modifica `frontend/package.json`:
   ```json
   {
     "dependencies": {
       "nuxt": "^3.17.5"  // era 4.2.0
     }
   }
   ```

2. Reinstalla:
   ```bash
   rm -rf node_modules package-lock.json
   npm install
   ```

---

## 🔍 Perché Succede Questo?

### npm Bug #4828

npm ha un bug con **platform-specific optional dependencies**:

1. Quando installi su Mac (darwin/arm64), npm crea `package-lock.json` con solo le dependencies per Mac
2. oxc-parser ha diverse versioni native per ogni OS/CPU
3. npm non include tutte le versioni nel lockfile
4. Quando qualcosa cambia, npm non trova la versione giusta

### Architetture Supportate da oxc-parser

```
oxc-parser-darwin-arm64      ← Mac M1/M2/M3
oxc-parser-darwin-x64        ← Mac Intel
oxc-parser-linux-arm64-gnu   ← Linux ARM
oxc-parser-linux-arm64-musl  ← Alpine Linux ARM
oxc-parser-linux-x64-gnu     ← Linux Intel
oxc-parser-linux-x64-musl    ← Alpine Linux Intel
oxc-parser-win32-arm64-msvc  ← Windows ARM
oxc-parser-win32-x64-msvc    ← Windows Intel
```

npm dovrebbe scaricarle tutte, ma il bug fa sì che ne scarichi solo una.

---

## 📊 Confronto Package Managers

### pnpm (CONSIGLIATO) ✅

**Pro:**
- ✅ Non ha il bug delle optional dependencies
- ✅ 2-3x più veloce di npm
- ✅ Risparmia ~50% spazio disco
- ✅ Lock file più deterministico
- ✅ Migliore gestione peer dependencies

**Contro:**
- ❌ Bisogna installarlo separatamente

### npm ⚠️

**Pro:**
- ✅ Incluso con Node.js

**Contro:**
- ❌ Bug #4828 con optional dependencies
- ❌ Più lento
- ❌ Spreca spazio disco (duplica node_modules)

### yarn

**Pro:**
- ✅ Non ha il bug npm
- ✅ Abbastanza veloce

**Contro:**
- ❌ Versione 1.x obsoleta
- ❌ Yarn 2+ (Berry) breaking changes

---

## ✅ Verifica che Funzioni

Dopo aver usato una delle soluzioni, verifica:

```bash
# 1. Controlla che oxc-parser sia installato
ls node_modules/.pnpm/oxc-parser*/node_modules/@oxc-parser  # con pnpm
# oppure
ls node_modules/oxc-parser  # con npm

# 2. Dovrebbe contenere:
# - bindings.js
# - darwin-arm64/ (se su Mac M1/M2/M3)
# - index.js
# - package.json

# 3. Prova ad avviare
pnpm dev  # o npm run dev
```

Dovresti vedere:
```
✔ Vite server built in XXXms
✔ Nitro built in XXX ms

  ➜ Local:   http://localhost:3000/
```

**SENZA** l'errore oxc-parser! ✅

---

## 🎯 Raccomandazione Finale

**Usa pnpm.** È la soluzione più pulita e affidabile.

1. Installa pnpm: `npm install -g pnpm`
2. Rimuovi npm artifacts: `rm -rf node_modules package-lock.json`
3. Installa con pnpm: `pnpm install`
4. Usa pnpm d'ora in poi: `pnpm dev`, `pnpm build`, etc.

Il tuo team può continuare ad usare npm se vuole, ma pnpm funzionerà sempre meglio per il frontend.

---

## 📞 Se Hai Ancora Problemi

Se dopo tutte queste soluzioni continui ad avere l'errore:

1. Posta l'output completo di:
   ```bash
   node --version
   npm --version  # o pnpm --version
   uname -m  # architettura CPU
   ```

2. Posta l'output di:
   ```bash
   ls -la node_modules/oxc-parser 2>&1
   ls -la node_modules/.pnpm/oxc-parser* 2>&1  # se usi pnpm
   ```

3. Considera il downgrade a Nuxt 3.17.5

---

**Fix with pnpm! 🚀**
