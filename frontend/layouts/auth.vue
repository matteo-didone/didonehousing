<template>
  <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-50 dark:bg-gray-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <NuxtLink to="/" class="flex justify-center">
        <h1 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
          Aviano Housing
        </h1>
      </NuxtLink>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <slot />
      </div>
    </div>

    <!-- Language Switcher -->
    <div class="mt-6 text-center">
      <button
        @click="toggleLocale"
        class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
      >
        {{ locale === 'en' ? '🇮🇹 Italiano' : '🇬🇧 English' }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
const { locale, setLocale } = useI18n()

const toggleLocale = () => {
  const newLocale = locale.value === 'en' ? 'it' : 'en'
  setLocale(newLocale)
}
</script>
