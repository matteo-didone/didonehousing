# Setup Guide - Comprehensive Property Fields

Questa guida ti aiuta a configurare tutti i nuovi campi per la gestione completa delle proprietà basati sui contratti ufficiali di Aviano.

## 📋 Cosa è Stato Fatto

### Backend ✅
1. **Migration** con 30+ nuovi campi (Google Maps, dati catastali, bagni dettagliati, etc.)
2. **Property Model** aggiornato con auto-calcolo distanza dalla base
3. **Form Requests** con validazione completa
4. **PropertyController** semplificato

### Frontend ✅
1. **useGoogleMaps composable** per autocomplete indirizzi
2. **Property interface** TypeScript aggiornata
3. **Runtime config** preparato per Google Maps API key

### Documentazione ✅
- `PROPERTY_FIELDS_ANALYSIS.md` - Analisi completa campi richiesti

---

## 🚀 Setup Obbligatorio

### 1. Google Maps API Key

**Perché serve?**
- Autocomplete indirizzi durante creazione proprietà
- Calcolo automatico coordinate (lat/lng)
- Calcolo distanza dalla base Aviano

**Come ottenerla:**

1. Vai su [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuovo progetto o seleziona uno esistente
3. Abilita queste API:
   - **Maps JavaScript API**
   - **Places API**
   - **Geocoding API** (opzionale, già inclusa in Places)

4. Vai su **Credentials** → **Create Credentials** → **API Key**

5. **IMPORTANTE - Restrizioni chiave:**
   ```
   Application restrictions:
   - HTTP referrers (web sites)

   Website restrictions:
   - http://localhost:3000/*
   - http://localhost:8000/*
   - https://tuodominio.com/* (se in produzione)

   API restrictions:
   - Restrict key
   - Select: Maps JavaScript API, Places API
   ```

6. Copia la tua API key

**Aggiungi al Frontend:**

Crea/modifica `.env` nella cartella `frontend/`:

```bash
# Frontend .env
NUXT_PUBLIC_GOOGLE_MAPS_API_KEY=AIzaSy... # La tua chiave qui
```

---

### 2. Database Migration

Esegui la migration per aggiungere i nuovi campi:

```bash
cd backend

# Con Docker
docker exec -it aviano_backend php artisan migrate

# Senza Docker (se backend locale)
php artisan migrate
```

**Cosa fa:**
- Aggiunge 30+ colonne alla tabella `properties`
- Migra i dati esistenti (`bathrooms` → `full_bathrooms`)
- Cambia `furnished` da boolean a enum
- Aggiunge enum constraint a `heating_type`

---

### 3. Restart Applicazioni

**Backend:**
```bash
docker-compose restart backend

# Oppure se locale:
php artisan serve
```

**Frontend:**
```bash
cd frontend
npm run dev
```

---

## 📝 Prossimi Step (TODO)

Il backend e i composable sono pronti. Manca solo l'**UI del form**:

### Form di Creazione Proprietà (`/properties/create`)

Il form attuale è molto semplice. Va completamente rifatto con:

1. **Sezione 1: Indirizzo** (con Google autocomplete)
   - Input con autocomplete Google Maps
   - Badge con distanza dalla base auto-calcolata
   - Campi auto-popolati (via, numero, città, provincia, CAP)

2. **Sezione 2: Dati Catastali** (opzionale, ma consigliato)
   - Foglio, Particella, Subalterno
   - Categoria, Rendita

3. **Sezione 3: Stanze**
   - Living rooms, Dining rooms, Bedrooms
   - **Bagni dettagliati:**
     - Full bathrooms (con doccia/vasca)
     - Half bathrooms (solo WC + lavandino)
   - Kitchen count
   - Checkbox: Basement, Attic, Garage, Yard

4. **Sezione 4: Arredamento**
   - Radio buttons: Unfurnished / Partially / Fully furnished

5. **Sezione 5: Animali**
   - Checkbox: Pets allowed
   - Textarea: Note specifiche (es. "Solo cani piccoli", "Max 2 gatti")

6. **Sezione 6: Riscaldamento** (dettagliato come da contratto)
   - Select: Tipo (City gas, LPG, Fuel, Electric, Heat pump, Wood, Other)
   - Select: Sistema (Centralized, Autonomous, Shared with US, Shared with Italians)
   - Checkbox: Has heat meter (contacalorie)
   - Textarea: Note riscaldamento

7. **Sezione 7: Pittura/Ristrutturazione**
   - Checkbox: Redecoration fees required
   - Input number: Amount (se checked)
   - Date picker: When painted

8. **Sezione 8: Dettagli Extra**
   - Floor number, Total floors
   - Checkboxes: Elevator, Balcony, Terrace
   - Total square meters
   - Select: Energy class (A4 → G)
   - Year built

---

## 🎨 UI/UX Suggerimenti

### Google Autocomplete Input
```vue
<template>
  <div>
    <Label>Indirizzo Proprietà</Label>
    <Input
      ref="addressInput"
      v-model="addressSearch"
      placeholder="Inizia a digitare l'indirizzo..."
      @focus="initializeAutocomplete"
    />

    <!-- Badge con distanza -->
    <div v-if="formData.distance_from_base_km" class="mt-2">
      <Badge variant="secondary">
        📍 {{ formData.distance_from_base_km }} km dalla base
      </Badge>
    </div>
  </div>
</template>
```

### Bagni (esempio UI chiara)
```vue
<div class="grid grid-cols-2 gap-4">
  <div>
    <Label>Bagni Completi</Label>
    <p class="text-sm text-muted-foreground mb-2">
      Con doccia/vasca
    </p>
    <Input type="number" v-model.number="formData.full_bathrooms" min="0" />
  </div>

  <div>
    <Label>Mezzi Bagni</Label>
    <p class="text-sm text-muted-foreground mb-2">
      Solo WC + lavandino
    </p>
    <Input type="number" v-model.number="formData.half_bathrooms" min="0" />
  </div>
</div>

<p class="text-sm mt-2">
  Totale bagni: <strong>{{ totalBathrooms }}</strong>
</p>
```

### Arredamento (Radio buttons chiari)
```vue
<RadioGroup v-model="formData.furnishing_status">
  <div class="flex items-center space-x-2">
    <RadioGroupItem value="unfurnished" id="unfurnished" />
    <Label for="unfurnished" class="cursor-pointer">
      🏚️ Non ammobiliato
    </Label>
  </div>
  <div class="flex items-center space-x-2">
    <RadioGroupItem value="partially_furnished" id="partially" />
    <Label for="partially" class="cursor-pointer">
      🛋️ Parzialmente ammobiliato
    </Label>
  </div>
  <div class="flex items-center space-x-2">
    <RadioGroupItem value="fully_furnished" id="fully" />
    <Label for="fully" class="cursor-pointer">
      🏡 Completamente ammobiliato
    </Label>
  </div>
</RadioGroup>
```

---

## ⚠️ Note Importanti

### Validazione Frontend
Il backend valida tutto, ma è consigliato validare anche frontend:

```typescript
// Esempio validazione custom
const validateForm = () => {
  const errors = []

  // Almeno 1 bagno totale
  if (formData.full_bathrooms + formData.half_bathrooms < 1) {
    errors.push('Serve almeno un bagno')
  }

  // Floor <= total floors
  if (formData.floor_number && formData.total_floors) {
    if (formData.floor_number > formData.total_floors) {
      errors.push('Piano non può superare piani totali')
    }
  }

  // Redecoration amount se required
  if (formData.redecoration_fees_required && !formData.redecoration_fees_amount) {
    errors.push('Specifica importo imbiancatura')
  }

  return errors
}
```

### Campi Minimi Obbligatori
Per creare una proprietà serve:
- ✅ Indirizzo completo (via, numero, città, provincia, CAP)
- ✅ Almeno 1 camera da letto
- ✅ Almeno 1 bagno (full o half)
- ✅ Furnishing status
- ✅ Pets allowed (boolean)

Tutto il resto è opzionale ma consigliato.

---

## 📦 File Modificati

**Backend:**
- `database/migrations/2025_11_09_100000_add_comprehensive_property_fields.php`
- `app/Models/Property.php`
- `app/Http/Requests/StorePropertyRequest.php`
- `app/Http/Requests/UpdatePropertyRequest.php`
- `app/Http/Controllers/Api/PropertyController.php`

**Frontend:**
- `composables/useGoogleMaps.ts` (nuovo)
- `composables/useProperty.ts` (interfaccia aggiornata)
- `nuxt.config.ts` (Google Maps key)

**Documentazione:**
- `PROPERTY_FIELDS_ANALYSIS.md`

---

## 🆘 Troubleshooting

### Google Maps non carica
```
Error: Failed to load Google Maps
```

**Soluzioni:**
1. Verifica che `NUXT_PUBLIC_GOOGLE_MAPS_API_KEY` sia settata in `.env`
2. Verifica che le API siano abilitate in Google Cloud Console
3. Controlla le restrizioni della chiave (deve permettere localhost)
4. Verifica quota giornaliera (Google Maps ha free tier limitato)

### Migration fallisce
```
SQLSTATE[42S21]: Column already exists
```

**Soluzione:**
```bash
# Rollback migration
php artisan migrate:rollback --step=1

# Poi riprova
php artisan migrate
```

### Tipo di dati non riconosciuto
```
TypeError: furnishing_status is not assignable to type boolean
```

**Soluzione:**
- Pulisci cache TypeScript: riavvia VS Code
- Verifica che l'interfaccia Property sia aggiornata
- Hard reload frontend (Ctrl+Shift+R)

---

## ✅ Checklist Setup

- [ ] Google Maps API Key ottenuta
- [ ] API key aggiunta a `frontend/.env`
- [ ] API Maps JavaScript e Places abilitate
- [ ] Restrizioni API key configurate
- [ ] Migration backend eseguita (`php artisan migrate`)
- [ ] Backend riavviato
- [ ] Frontend riavviato
- [ ] Testato autocomplete su pagina create property (quando implementata)
- [ ] Verificato calcolo distanza dalla base

---

## 🎯 Prossima Sessione

Nella prossima sessione di sviluppo:
1. Implementeremo il **nuovo form di creazione proprietà** con tutti i campi
2. Aggiungeremo **Google autocomplete** funzionante
3. Creeremo una **UI user-friendly** con sezioni collassabili
4. Testeremo il flow completo end-to-end

---

Fine guida. Per domande o problemi, consulta `PROPERTY_FIELDS_ANALYSIS.md` per i dettagli tecnici di ogni campo.
