import enUS from '~/locales/en-US.json'
import itIT from '~/locales/it-IT.json'

export default defineNuxtPlugin((nuxtApp) => {
  const i18n = nuxtApp.$i18n as any

  // Manually set messages for each locale
  i18n.setLocaleMessage('en', enUS)
  i18n.setLocaleMessage('it', itIT)
})
