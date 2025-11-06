<template>
  <div class="min-h-full">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
          <div class="flex">
            <!-- Logo -->
            <div class="flex flex-shrink-0 items-center">
              <NuxtLink to="/" class="text-xl font-bold text-blue-600 dark:text-blue-400">
                Aviano Housing
              </NuxtLink>
            </div>

            <!-- Navigation Links (authenticated) -->
            <div v-if="isAuthenticated" class="hidden sm:ml-6 sm:flex sm:space-x-8">
              <NuxtLink
                v-if="isLandlord"
                to="/landlord/dashboard"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-900 dark:text-gray-100 hover:border-gray-300"
                active-class="border-blue-500"
              >
                Dashboard
              </NuxtLink>

              <NuxtLink
                v-if="isLandlord"
                to="/landlord/properties"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-900 dark:text-gray-100 hover:border-gray-300"
                active-class="border-blue-500"
              >
                My Properties
              </NuxtLink>

              <NuxtLink
                v-if="isTenant"
                to="/tenant/search"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-900 dark:text-gray-100 hover:border-gray-300"
                active-class="border-blue-500"
              >
                Search Properties
              </NuxtLink>

              <NuxtLink
                v-if="isHousingOffice"
                to="/ho/pending-review"
                class="inline-flex items-center border-b-2 border-transparent px-1 pt-1 text-sm font-medium text-gray-900 dark:text-gray-100 hover:border-gray-300"
                active-class="border-blue-500"
              >
                Pending Review
              </NuxtLink>
            </div>
          </div>

          <!-- Right side -->
          <div class="flex items-center">
            <!-- Language Switcher -->
            <button
              @click="toggleLocale"
              class="mr-4 rounded-md px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
            >
              {{ locale === 'en' ? '🇬🇧 EN' : '🇮🇹 IT' }}
            </button>

            <!-- User menu (authenticated) -->
            <div v-if="isAuthenticated" class="ml-3 relative">
              <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-700 dark:text-gray-300">
                  {{ user?.full_name }}
                </span>
                <button
                  @click="handleLogout"
                  class="btn btn-secondary btn-sm"
                >
                  Logout
                </button>
              </div>
            </div>

            <!-- Login button (guest) -->
            <div v-else>
              <NuxtLink to="/login" class="btn btn-primary btn-sm">
                Login
              </NuxtLink>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
const { user, isAuthenticated, isLandlord, isTenant, isHousingOffice, logout } = useAuth()
const { locale, setLocale } = useI18n()

const toggleLocale = () => {
  const newLocale = locale.value === 'en' ? 'it' : 'en'
  setLocale(newLocale)
}

const handleLogout = async () => {
  await logout()
}
</script>
