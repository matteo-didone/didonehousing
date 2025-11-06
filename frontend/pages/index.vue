<template>
  <div class="text-center">
    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl">
      Welcome to Aviano Housing Platform
    </h1>

    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
      All-in-one platform for off-base housing management at Aviano Air Base
    </p>

    <div class="mt-10 flex items-center justify-center gap-x-6">
      <NuxtLink
        v-if="!isAuthenticated"
        to="/register"
        class="btn btn-primary btn-lg"
      >
        Get started
      </NuxtLink>

      <NuxtLink
        v-if="!isAuthenticated"
        to="/login"
        class="btn btn-secondary btn-lg"
      >
        Sign in
      </NuxtLink>

      <NuxtLink
        v-if="isAuthenticated"
        :to="getDefaultRoute()"
        class="btn btn-primary btn-lg"
      >
        Go to Dashboard
      </NuxtLink>
    </div>

    <!-- Features -->
    <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
      <div class="card text-left">
        <div class="text-2xl mb-2">🏠</div>
        <h3 class="font-semibold text-lg mb-2">Property Management</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Complete listing lifecycle with HO approval workflows
        </p>
      </div>

      <div class="card text-left">
        <div class="text-2xl mb-2">📄</div>
        <h3 class="font-semibold text-lg mb-2">Bilingual Contracts</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          IT/EN templates with eIDAS e-signature
        </p>
      </div>

      <div class="card text-left">
        <div class="text-2xl mb-2">💰</div>
        <h3 class="font-semibold text-lg mb-2">Payments</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          SEPA DD/CT, cards (Stripe), deposit escrow
        </p>
      </div>

      <div class="card text-left">
        <div class="text-2xl mb-2">🔧</div>
        <h3 class="font-semibold text-lg mb-2">Maintenance</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Ticketing system with SLA, vendor management
        </p>
      </div>

      <div class="card text-left">
        <div class="text-2xl mb-2">🌍</div>
        <h3 class="font-semibold text-lg mb-2">Full I18n</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          Complete Italian/English support
        </p>
      </div>

      <div class="card text-left">
        <div class="text-2xl mb-2">🔐</div>
        <h3 class="font-semibold text-lg mb-2">Security</h3>
        <p class="text-gray-600 dark:text-gray-400 text-sm">
          RBAC, 2FA, GDPR-compliant, full audit trail
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
})

const { isAuthenticated, getDefaultRoute } = useAuth()
</script>
