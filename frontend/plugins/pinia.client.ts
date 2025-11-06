import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'

export default defineNuxtPlugin((nuxtApp) => {
  // Get the existing Pinia instance created by @pinia/nuxt
  const pinia = nuxtApp.$pinia

  // Add the persistedstate plugin
  pinia.use(piniaPluginPersistedstate)
})
