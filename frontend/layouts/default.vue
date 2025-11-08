<template>
  <div class="min-h-screen bg-background">
    <!-- Navbar -->
    <nav class="sticky top-0 z-50 border-b border-border bg-card shadow-sm">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
          <!-- Logo & Primary Nav -->
          <div class="flex items-center gap-8">
            <!-- Logo -->
            <NuxtLink to="/" class="flex items-center gap-2">
              <div class="text-2xl font-bold text-primary">
                Aviano Housing
              </div>
            </NuxtLink>

            <!-- Primary Navigation (Desktop) -->
            <div v-if="isAuthenticated" class="hidden md:flex items-center gap-1">
              <NuxtLink
                :to="getDefaultRoute()"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.dashboard') }}
              </NuxtLink>

              <NuxtLink
                v-if="isLandlord"
                to="/properties"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.myProperties') }}
              </NuxtLink>

              <NuxtLink
                v-if="isTenant"
                to="/properties"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.searchProperties') }}
              </NuxtLink>

              <NuxtLink
                v-if="isHousingOffice"
                to="/ho/pending-review"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.pendingReview') }}
              </NuxtLink>

              <NuxtLink
                to="/maintenance"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.maintenance') }}
              </NuxtLink>

              <NuxtLink
                to="/documents"
                class="px-3 py-2 rounded-md text-sm font-medium transition-colors hover:bg-accent hover:text-accent-foreground"
                active-class="bg-accent text-accent-foreground"
              >
                {{ t('navigation.documents') }}
              </NuxtLink>
            </div>
          </div>

          <!-- Right Side: Actions -->
          <div class="flex items-center gap-3">
            <!-- Dark Mode Toggle -->
            <button
              @click="toggleColorMode"
              class="p-2 rounded-md hover:bg-accent transition-colors"
              :aria-label="colorMode === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'"
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
              class="px-3 py-2 rounded-md hover:bg-accent transition-colors text-sm font-medium flex items-center gap-2"
            >
              <span>{{ locale === 'en' ? '🇺🇸' : '🇮🇹' }}</span>
              <span class="hidden sm:inline text-foreground">{{ locale === 'en' ? 'EN' : 'IT' }}</span>
            </button>

            <!-- User Menu (Authenticated) -->
            <div v-if="isAuthenticated" class="relative" ref="userMenuContainer">
              <button
                @click="toggleUserMenu"
                class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-accent transition-colors"
              >
                <div class="w-8 h-8 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-sm font-semibold">
                  {{ user?.first_name?.[0] }}{{ user?.last_name?.[0] }}
                </div>
                <span class="hidden sm:inline text-sm font-medium text-foreground">
                  {{ user?.full_name }}
                </span>
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="16"
                  height="16"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  class="text-foreground"
                >
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </button>

              <!-- Dropdown Menu -->
              <div
                v-if="showUserMenu"
                class="absolute right-0 mt-2 w-56 rounded-md border border-border bg-card shadow-lg"
              >
                <div class="p-2">
                  <NuxtLink
                    to="/profile"
                    class="block px-3 py-2 rounded-md text-sm hover:bg-accent transition-colors"
                    @click="showUserMenu = false"
                  >
                    {{ t('navigation.profile') }}
                  </NuxtLink>
                  <NuxtLink
                    to="/settings"
                    class="block px-3 py-2 rounded-md text-sm hover:bg-accent transition-colors"
                    @click="showUserMenu = false"
                  >
                    {{ t('navigation.settings') }}
                  </NuxtLink>
                  <div class="my-1 border-t border-border"></div>
                  <button
                    @click="handleLogout"
                    class="w-full text-left px-3 py-2 rounded-md text-sm text-destructive hover:bg-destructive/10 transition-colors"
                  >
                    {{ t('auth.logout') }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Login Button (Guest) -->
            <NuxtLink
              v-else
              to="/login"
            >
              <Button>{{ t('auth.login') }}</Button>
            </NuxtLink>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-border bg-card">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <div class="text-center text-sm text-muted-foreground">
          <p>&copy; {{ new Date().getFullYear() }} Aviano Housing Platform. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/ui/Button.vue'

const { user, isAuthenticated, isLandlord, isTenant, isHousingOffice, logout, getDefaultRoute } = useAuth()
const { locale, setLocale, t } = useI18n()
const colorMode = useColorMode()

const showUserMenu = ref(false)
const userMenuContainer = ref<HTMLElement | null>(null)

const toggleLocale = () => {
  const newLocale = locale.value === 'en' ? 'it' : 'en'
  setLocale(newLocale)
}

const toggleColorMode = () => {
  colorMode.preference = colorMode.value === 'dark' ? 'light' : 'dark'
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  showUserMenu.value = false
  await logout()
}

// Close menu when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  if (userMenuContainer.value && !userMenuContainer.value.contains(event.target as Node)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>
