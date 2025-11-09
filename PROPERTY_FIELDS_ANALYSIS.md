# Property Fields Analysis - Complete Requirements

Analisi completa dei campi necessari per la gestione delle proprietà basata sui contratti ufficiali della base di Aviano.

## Fonte Documenti
- `02_Rental_Agreement.pdf` - Contratto di locazione ufficiale
- `05_Inventory_Form.pdf` - Form inventario dettagliato
- Requisiti utente per Google Maps integration

---

## 1. INDIRIZZO E LOCALIZZAZIONE

### Campi Attuali (da mantenere)
- `street_name` - VARCHAR(255) - Via/Strada
- `house_number` - VARCHAR(20) - Numero civico
- `apt_number` - VARCHAR(20) NULLABLE - Numero appartamento
- `city` - VARCHAR(100) - Città
- `province` - VARCHAR(50) - Provincia
- `postal_code` - VARCHAR(10) - CAP
- `country` - VARCHAR(100) - Paese (default: Italy)

### Nuovi Campi Richiesti
- `google_place_id` - VARCHAR(255) NULLABLE - Google Places ID per autocomplete
- `latitude` - DECIMAL(10, 8) NULLABLE - Latitudine (per calcolo distanza)
- `longitude` - DECIMAL(11, 8) NULLABLE - Longitudine (per calcolo distanza)
- `distance_from_base_km` - DECIMAL(5, 2) NULLABLE - Distanza in km dalla base (auto-calcolato)
- `formatted_address` - TEXT NULLABLE - Indirizzo formattato da Google Maps

**Note:** Coordinate base Aviano: 46.031389, 12.596667

---

## 2. DATI CATASTALI (ESTATE OFFICIAL DATA)

### Campi da Aggiungere
- `cadastral_sheet` - VARCHAR(20) NULLABLE - Foglio catastale
- `cadastral_plot` - VARCHAR(20) NULLABLE - Particella/Mappale
- `cadastral_unit` - VARCHAR(20) NULLABLE - Subalterno
- `cadastral_category` - VARCHAR(10) NULLABLE - Categoria catastale (A/1, A/2, etc.)
- `cadastral_income` - DECIMAL(10, 2) NULLABLE - Rendita catastale in €

**Fonte:** Sezione 3.14 del contratto - "ESTATE OFFICIAL DATA"

---

## 3. STANZE E SPAZI (ROOMS AND SPACES)

### Campi Attuali (da mantenere)
- `bedrooms` - INTEGER - Camere da letto
- `bathrooms` - INTEGER - Numero totale bagni

### Campi da Aggiungere
- `living_rooms` - INTEGER DEFAULT 0 - Soggiorni
- `dining_rooms` - INTEGER DEFAULT 0 - Sale da pranzo
- `kitchens` - INTEGER DEFAULT 1 - Cucine

### Campi Esistenti (già nel DB ma optional)
- `basement` - BOOLEAN DEFAULT false - Cantina
- `attic` - BOOLEAN DEFAULT false - Soffitta
- `garage` - BOOLEAN DEFAULT false - Garage/Box
- `yard` - BOOLEAN DEFAULT false - Giardino/Cortile

### Bagni - Dettaglio Tipologia
**PROBLEMA IDENTIFICATO:** Il contratto prevede solo numero bagni, ma l'utente ha richiesto distinzione tra:
- Bagno completo (full bathroom: toilet, sink, shower/tub)
- Mezzo bagno (half bathroom: toilet, sink only)

**Soluzione:**
- `full_bathrooms` - INTEGER DEFAULT 0 - Bagni completi
- `half_bathrooms` - INTEGER DEFAULT 0 - Mezzi bagni
- RIMUOVERE campo `bathrooms` o renderlo computed (full + half)

**Fonte:** Sezione 3.5-3.13 del contratto

---

## 4. ARREDAMENTO (FURNISHING)

### Campo Attuale
- `furnished` - BOOLEAN - Solo sì/no

### Modifica Richiesta
Cambiare da BOOLEAN a ENUM per supportare:
- `furnishing_status` - ENUM('unfurnished', 'partially_furnished', 'fully_furnished')

**Mapping:**
- `unfurnished` = Non ammobiliato
- `partially_furnished` = Parzialmente ammobiliato
- `fully_furnished` = Completamente ammobiliato

**Fonte:** Sezione 3.16 del contratto + richiesta utente

---

## 5. ANIMALI DOMESTICI (PETS)

### Campo Attuale
- `pets_allowed` - BOOLEAN

### Campi da Aggiungere
- `pets_notes` - TEXT NULLABLE - Note specifiche sugli animali permessi
  * Es: "Solo cani di piccola taglia"
  * Es: "Massimo 2 gatti"
  * Es: "Con supplemento mensile di €50"

**Fonte:** Sezione 3.15 del contratto + Articolo 17 + richiesta utente

---

## 6. RISCALDAMENTO E RAFFRESCAMENTO (HEATING/COOLING)

### Campi da Aggiungere

**Tipo di Riscaldamento:**
- `heating_type` - ENUM NULLABLE:
  * 'city_gas' - Metano/Gas di rete
  * 'lpg_with_coupons' - GPL con buoni
  * 'lpg_without_coupons' - GPL senza buoni
  * 'fuel_oil' - Gasolio
  * 'electric' - Elettrico
  * 'heat_pump' - Pompa di calore
  * 'other' - Altro

**Sistema:**
- `heating_system` - ENUM NULLABLE:
  * 'centralized' - Centralizzato condominiale
  * 'autonomous' - Autonomo
  * 'shared_with_us' - Condiviso con altri americani autorizzati
  * 'shared_with_italians' - Condiviso con italiani

**Contabilizzazione:**
- `has_heat_meter` - BOOLEAN DEFAULT false - Contacalorie individuale

**Note:**
- `heating_notes` - TEXT NULLABLE - Note aggiuntive sul riscaldamento

**Fonte:** Sezione 3.18 del contratto

---

## 7. PITTURA E RISTRUTTURAZIONI (PAINT/REDECORATION)

### Campi da Aggiungere
- `redecoration_fees_required` - BOOLEAN DEFAULT false
- `redecoration_fees_amount` - DECIMAL(10, 2) NULLABLE - Importo richiesto per imbiancatura
- `redecoration_date` - DATE NULLABLE - Data ultimo intervento

**Fonte:** Sezione 3.17 e Articolo 9 del contratto

---

## 8. CONTRATTO E CONDIZIONI ECONOMICHE (CONTRACT & FEES)

### Campi Listing (alcuni già esistono)
Questi vanno nella tabella `listings` (relazione con properties):

- `monthly_rent` - DECIMAL(10, 2) - Canone mensile
- `currency` - VARCHAR(3) DEFAULT 'EUR'
- `security_deposit` - DECIMAL(10, 2) - Deposito cauzionale (di solito = 1 mese)
- `condo_fees_monthly` - DECIMAL(10, 2) NULLABLE - Spese condominiali fisse mensili
- `available_from` - DATE - Disponibile da

**Nuovi campi per gestione spese:**
- `utilities_included` - BOOLEAN DEFAULT false - Utenze incluse nel canone
- `utilities_notes` - TEXT NULLABLE - Dettaglio utenze
- `contract_duration_years` - INTEGER DEFAULT 4 - Durata contratto (default 4+4)

---

## 9. ALTRE CARATTERISTICHE

### Da Valutare/Aggiungere
- `floor_number` - INTEGER NULLABLE - Piano (es: 2° piano, -1 per interrato)
- `total_floors` - INTEGER NULLABLE - Piani totali edificio
- `elevator` - BOOLEAN DEFAULT false - Presenza ascensore
- `balcony` - BOOLEAN DEFAULT false - Balcone
- `terrace` - BOOLEAN DEFAULT false - Terrazzo
- `total_sqm` - DECIMAL(8, 2) NULLABLE - Superficie totale in m²
- `energy_class` - VARCHAR(5) NULLABLE - Classe energetica (A+, A, B, C, etc.)
- `year_built` - INTEGER NULLABLE - Anno di costruzione

---

## 10. RIEPILOGO MODIFICHE DATABASE

### Campi da AGGIUNGERE alla tabella `properties`:
```sql
-- Localizzazione Google Maps
google_place_id VARCHAR(255) NULLABLE
latitude DECIMAL(10, 8) NULLABLE
longitude DECIMAL(11, 8) NULLABLE
distance_from_base_km DECIMAL(5, 2) NULLABLE
formatted_address TEXT NULLABLE

-- Dati Catastali
cadastral_sheet VARCHAR(20) NULLABLE
cadastral_plot VARCHAR(20) NULLABLE
cadastral_unit VARCHAR(20) NULLABLE
cadastral_category VARCHAR(10) NULLABLE
cadastral_income DECIMAL(10, 2) NULLABLE

-- Stanze dettagliate
living_rooms INTEGER DEFAULT 0
dining_rooms INTEGER DEFAULT 0
kitchens INTEGER DEFAULT 1
full_bathrooms INTEGER DEFAULT 0
half_bathrooms INTEGER DEFAULT 0

-- Animali
pets_notes TEXT NULLABLE

-- Riscaldamento
heating_type ENUM(...) NULLABLE
heating_system ENUM(...) NULLABLE
has_heat_meter BOOLEAN DEFAULT false
heating_notes TEXT NULLABLE

-- Pittura
redecoration_fees_required BOOLEAN DEFAULT false
redecoration_fees_amount DECIMAL(10, 2) NULLABLE
redecoration_date DATE NULLABLE

-- Altre caratteristiche
floor_number INTEGER NULLABLE
total_floors INTEGER NULLABLE
elevator BOOLEAN DEFAULT false
balcony BOOLEAN DEFAULT false
terrace BOOLEAN DEFAULT false
total_sqm DECIMAL(8, 2) NULLABLE
energy_class VARCHAR(5) NULLABLE
year_built INTEGER NULLABLE
```

### Campi da MODIFICARE:
```sql
-- Da BOOLEAN a ENUM
ALTER TABLE properties MODIFY COLUMN furnished -> furnishing_status ENUM(...)

-- Da INTEGER a computed/split
bathrooms -> eliminare, sostituire con full_bathrooms + half_bathrooms
```

### Campi da aggiungere/modificare in `listings`:
```sql
condo_fees_monthly DECIMAL(10, 2) NULLABLE
security_deposit DECIMAL(10, 2) NULLABLE
utilities_included BOOLEAN DEFAULT false
utilities_notes TEXT NULLABLE
contract_duration_years INTEGER DEFAULT 4
```

---

## 11. PRIORITÀ IMPLEMENTAZIONE

### FASE 1 - Essenziali (richiesti dall'utente)
1. ✅ Google Maps integration (place_id, lat, lng, formatted_address)
2. ✅ Distance calculation (distance_from_base_km)
3. ✅ Bathroom types (full_bathrooms, half_bathrooms)
4. ✅ Furnishing levels (furnishing_status enum)
5. ✅ Pet notes (pets_notes)
6. ✅ Stanze dettagliate (living_rooms, dining_rooms, kitchens)

### FASE 2 - Contratto compliance
7. ✅ Dati catastali completi
8. ✅ Heating system dettagliato
9. ✅ Redecoration fees

### FASE 3 - Nice to have
10. Floor/elevator/balcony
11. Energy class
12. Total sqm

---

## 12. GOOGLE MAPS INTEGRATION

### API Keys Necessarie
- **Google Places API** - Per autocomplete indirizzi
- **Google Maps JavaScript API** - Per mappa interattiva
- **Google Distance Matrix API** - Per calcolo distanza dalla base

### Flusso Autocomplete:
1. Utente digita indirizzo
2. Google Places Autocomplete suggerisce
3. Utente seleziona → retrieve place details
4. Sistema popola automaticamente:
   - street_name, house_number, city, province, postal_code
   - latitude, longitude
   - google_place_id, formatted_address
5. Sistema calcola distance_from_base_km usando Distance Matrix API

### Coordinate Base Aviano
```javascript
const AVIANO_BASE_COORDS = {
  lat: 46.031389,
  lng: 12.596667,
  address: "Aviano Air Base, 33081 Aviano PN, Italy"
}
```

---

## 13. VALIDAZIONE

### Regole Backend (Laravel)
```php
'street_name' => 'required|string|max:255',
'house_number' => 'required|string|max:20',
'city' => 'required|string|max:100',
'province' => 'required|string|max:50',
'postal_code' => 'required|string|max:10',

// Catastali - tutti optional ma consigliati
'cadastral_sheet' => 'nullable|string|max:20',
'cadastral_plot' => 'nullable|string|max:20',

// Stanze - almeno 1 camera da letto
'bedrooms' => 'required|integer|min:1',
'full_bathrooms' => 'required|integer|min:0',
'half_bathrooms' => 'required|integer|min:0',

// Validazione computed: almeno 1 bagno totale
// full_bathrooms + half_bathrooms >= 1

'furnishing_status' => 'required|in:unfurnished,partially_furnished,fully_furnished',
'heating_type' => 'nullable|in:city_gas,lpg_with_coupons,...',
```

---

Fine analisi. Prossimo step: Implementazione backend.
