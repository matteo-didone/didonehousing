<template>
  <div class="flex flex-col min-h-screen">
    <Navbar />
    <main class="flex-grow">
      <slot />
    </main>
    <Footer />
  </div>
</template>

<script setup lang="ts">
// Import components
import Navbar from '~/components/common/Navbar.vue'
import Footer from '~/components/common/Footer.vue'
</script>
