<template>
  <footer class="bg-gray-800 text-white mt-auto">
    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Company Info -->
        <div class="col-span-1 md:col-span-2">
          <h3 class="text-xl font-bold mb-4">Aviano Housing</h3>
          <p class="text-gray-400 mb-4">
            La piattaforma di riferimento per affitti residenziali ad Aviano e dintorni.
            Connettendo proprietari e inquilini in modo semplice e sicuro.
          </p>
        </div>

        <!-- Quick Links -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Link Rapidi</h4>
          <ul class="space-y-2">
            <li>
              <NuxtLink to="/" class="text-gray-400 hover:text-white transition-colors">
                Home
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/properties" class="text-gray-400 hover:text-white transition-colors">
                Proprietà
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/about" class="text-gray-400 hover:text-white transition-colors">
                Chi Siamo
              </NuxtLink>
            </li>
          </ul>
        </div>

        <!-- Support -->
        <div>
          <h4 class="text-lg font-semibold mb-4">Supporto</h4>
          <ul class="space-y-2">
            <li>
              <NuxtLink to="/contact" class="text-gray-400 hover:text-white transition-colors">
                Contatti
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/faq" class="text-gray-400 hover:text-white transition-colors">
                FAQ
              </NuxtLink>
            </li>
            <li>
              <NuxtLink to="/privacy" class="text-gray-400 hover:text-white transition-colors">
                Privacy Policy
              </NuxtLink>
            </li>
          </ul>
        </div>
      </div>

      <div class="mt-8 pt-8 border-t border-gray-700">
        <p class="text-center text-gray-400 text-sm">
          &copy; {{ currentYear }} Aviano Housing. Tutti i diritti riservati.
        </p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const currentYear = new Date().getFullYear()
</script>
