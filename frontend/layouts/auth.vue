<template>
  <div class="min-h-screen flex flex-col bg-background">
    <!-- Header with Theme & Language Controls -->
    <div class="absolute top-4 right-4 flex items-center gap-3">
      <!-- Dark Mode Toggle -->
      <button
        @click="toggleColorMode"
        class="p-2 rounded-md bg-card border border-border hover:bg-accent transition-colors"
        :aria-label="colorMode === 'dark' ? t('accessibility.switchToLightMode') : t('accessibility.switchToDarkMode')"
      >
        <!-- Sun Icon (Light Mode) -->
        <svg
          v-if="colorMode === 'dark'"
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="text-foreground"
        >
          <circle cx="12" cy="12" r="4" />
          <path d="M12 2v2" />
          <path d="M12 20v2" />
          <path d="m4.93 4.93 1.41 1.41" />
          <path d="m17.66 17.66 1.41 1.41" />
          <path d="M2 12h2" />
          <path d="M20 12h2" />
          <path d="m6.34 17.66-1.41 1.41" />
          <path d="m19.07 4.93-1.41 1.41" />
        </svg>

        <!-- Moon Icon (Dark Mode) -->
        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          width="20"
          height="20"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="text-foreground"
        >
          <path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z" />
        </svg>
      </button>

      <!-- Language Switcher -->
      <button
        @click="toggleLocale"
        class="px-3 py-2 rounded-md bg-card border border-border hover:bg-accent transition-colors text-sm font-medium flex items-center gap-2"
      >
        <span>{{ locale === 'en' ? '🇺🇸' : '🇮🇹' }}</span>
        <span class="text-foreground">{{ locale === 'en' ? t('language.english') : t('language.italian') }}</span>
      </button>
    </div>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
      <!-- Logo/Brand -->
      <div class="sm:mx-auto sm:w-full sm:max-w-md mb-8">
        <NuxtLink to="/" class="flex justify-center">
          <h1 class="text-3xl font-bold text-primary">
            {{ t('branding.appName') }}
          </h1>
        </NuxtLink>
      </div>

      <!-- Content Slot -->
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <slot />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const { locale, setLocale, t } = useI18n()
const colorMode = useColorMode()

const toggleLocale = () => {
  const newLocale = locale.value === 'en' ? 'it' : 'en'
  setLocale(newLocale)
}

const toggleColorMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}
</script>
