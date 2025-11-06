# 🎖️ Military Modern Theme - Navy + Gold

## Design Inspiration

Tema ispirato all'USAF (United States Air Force) con palette **Navy + Gold** per trasmettere professionalità, autorità e il legame con Aviano Air Base.

---

## 🎨 Palette Colori

### Light Mode

| Color | HSL | Hex | Usage |
|-------|-----|-----|-------|
| **Primary (Navy)** | `222 47% 33%` | `#1E3A8A` | Bottoni primari, CTA, headings |
| **Secondary (Sky Blue)** | `199 89% 51%` | `#0EA5E9` | Accenti, link secondari |
| **Accent (Military Gold)** | `38 92% 50%` | `#F59E0B` | Highlights, badges premium |
| **Success** | `142 71% 45%` | `#22C55E` | Stati approved, conferme |
| **Warning** | `38 92% 50%` | `#F59E0B` | Pending, attenzione |
| **Destructive** | `0 84% 60%` | `#EF4444` | Errori, elimina, rejected |

### Dark Mode

| Color | HSL | Hex | Usage |
|-------|-----|-----|-------|
| **Background** | `222 47% 11%` | `#0F172A` | Sfondo principale (very dark navy) |
| **Primary** | `217 91% 60%` | `#3B82F6` | Bottoni (lighter blue per contrasto) |
| **Accent** | `38 92% 60%` | `#FBBF24` | Gold più brillante |
| **Card** | `222 47% 15%` | `#1E293B` | Card, panel |

---

## 🏗️ Cosa È Stato Installato

### Nuove Dipendenze

```json
{
  "shadcn-nuxt": "^0.10.4",
  "class-variance-authority": "^0.7.1",
  "clsx": "^2.1.1",
  "tailwind-merge": "^2.5.5",
  "tailwindcss-animate": "^1.0.7",
  "lucide-vue-next": "^0.469.0"
}
```

### File Creati/Modificati

1. **`components.json`** - Configurazione shadcn-vue
2. **`plugins/ssr-width.ts`** - Plugin per SSR width
3. **`lib/utils.ts`** - Utility per combinare classi CSS
4. **`assets/css/main.css`** - Tema completo con CSS variables
5. **`tailwind.config.ts`** - Configurazione Tailwind aggiornata
6. **`nuxt.config.ts`** - Aggiunto modulo shadcn-nuxt
7. **`package.json`** - Dipendenze aggiornate

---

## 🚀 Come Rebuilda il Container

Dopo il commit, devi rebuilda il frontend Docker per installare i nuovi pacchetti:

```bash
# Stop frontend
docker compose stop frontend

# Rimuovi container
docker compose rm -f frontend

# Rebuild con nuove dipendenze
docker compose up -d --build frontend

# Watch logs
docker compose logs -f frontend
```

Dovresti vedere:
```
✔ Vite client built in XXms
✔ Nuxt Nitro server built in XXXms
➜ Local: http://localhost:3000/
```

---

## 🎨 Utility CSS Disponibili

### Gradienti

```vue
<!-- Navy to Sky Blue gradient -->
<div class="bg-military-gradient">...</div>

<!-- Gold gradient -->
<div class="bg-gold-gradient">...</div>
```

### Status Badges

```vue
<span class="badge-approved">Approved</span>
<span class="badge-pending">Pending</span>
<span class="badge-rejected">Rejected</span>
```

### Button Variants

```vue
<!-- Primary Navy button -->
<button class="btn btn-primary">Submit</button>

<!-- Secondary Sky Blue -->
<button class="btn btn-secondary">Cancel</button>

<!-- Gold Accent -->
<button class="bg-accent text-accent-foreground">Premium</button>

<!-- Sizes -->
<button class="btn btn-primary btn-sm">Small</button>
<button class="btn btn-primary btn-md">Medium</button>
<button class="btn btn-primary btn-lg">Large</button>
```

### Form Elements

```vue
<template>
  <div>
    <label class="form-label">Email</label>
    <input type="email" class="form-input" />
    <p class="form-error">Error message</p>
  </div>
</template>
```

---

## 🌓 Dark Mode Toggle

Il dark mode è già configurato con `@nuxtjs/color-mode`. Per toggleare:

```vue
<script setup>
const colorMode = useColorMode()

const toggleDark = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}
</script>

<template>
  <button @click="toggleDark">
    {{ colorMode.value === 'dark' ? '☀️' : '🌙' }}
  </button>
</template>
```

---

## 📦 Prossimi Passi

Dopo il rebuild:

1. **Verifica il tema**
   - Apri http://localhost:3000
   - Testa light/dark mode toggle
   - Verifica colori Navy + Gold

2. **Aggiungi componenti shadcn**
   ```bash
   docker compose exec frontend npx shadcn-vue@latest add button
   docker compose exec frontend npx shadcn-vue@latest add input
   docker compose exec frontend npx shadcn-vue@latest add card
   docker compose exec frontend npx shadcn-vue@latest add badge
   ```

3. **Rebuild pagina login**
   - Sostituisci testi hardcoded con i18n
   - Usa nuovi componenti shadcn
   - Applica tema Military Modern

---

## 🎯 Esempio: Login Page Moderna

Dopo aver aggiunto i componenti shadcn, la login page potrebbe diventare:

```vue
<template>
  <div class="flex min-h-full items-center justify-center">
    <Card class="w-full max-w-md">
      <CardHeader>
        <CardTitle>{{ $t('auth.login') }}</CardTitle>
        <CardDescription>
          {{ $t('auth.loginSubtitle') }}
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <Label for="email">{{ $t('auth.email') }}</Label>
            <Input
              id="email"
              v-model="form.email"
              type="email"
            />
          </div>

          <div>
            <Label for="password">{{ $t('auth.password') }}</Label>
            <Input
              id="password"
              v-model="form.password"
              type="password"
            />
          </div>

          <Button type="submit" class="w-full">
            {{ $t('auth.login') }}
          </Button>
        </form>
      </CardContent>
    </Card>
  </div>
</template>
```

---

## 🎨 Design Principles

1. **Professional & Trustworthy** - Navy communica affidabilità
2. **Military Connection** - Legame visivo con USAF
3. **Modern & Clean** - Design minimale, funzionale
4. **Accessible** - Contrasti WCAG AA compliant
5. **Consistent** - Design system unificato

---

## 🔗 Risorse

- [shadcn-vue Documentation](https://www.shadcn-vue.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Radix Vue](https://www.radix-vue.com/)
- [Lucide Icons](https://lucide.dev/)

---

**Built with ❤️ for Aviano Air Base** 🎖️
