<template>
  <div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary via-primary/90 to-primary/80 text-primary-foreground">
      <div class="container mx-auto px-4 py-24 lg:py-32">
        <div class="mx-auto max-w-3xl text-center">
          <h1 class="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
            {{ translations.heroTitle }}
          </h1>
          <p class="mt-6 text-lg leading-8 opacity-90 lg:text-xl">
            {{ translations.heroSubtitle }}
          </p>
          <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <NuxtLink
              v-if="!isAuthenticated"
              to="/register"
            >
              <Button size="lg" variant="secondary" class="w-full sm:w-auto min-w-[160px]">
                {{ translations.getStarted }}
              </Button>
            </NuxtLink>

            <NuxtLink
              v-if="isAuthenticated"
              :to="getDefaultRoute()"
            >
              <Button size="lg" variant="secondary" class="w-full sm:w-auto min-w-[160px]">
                {{ translations.goToDashboard }}
              </Button>
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- Decorative wave -->
      <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto">
          <path d="M0 0L60 10C120 20 240 40 360 46.7C480 53 600 47 720 43.3C840 40 960 40 1080 46.7C1200 53 1320 67 1380 73.3L1440 80V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V0Z" fill="hsl(var(--background))" />
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="container mx-auto px-4 py-16 lg:py-24">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Property Management -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                <polyline points="9 22 9 12 15 12 15 22" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.propertyManagement.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.propertyManagement.description }}
            </CardDescription>
          </CardHeader>
        </Card>

        <!-- Bilingual Contracts -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" x2="8" y1="13" y2="13" />
                <line x1="16" x2="8" y1="17" y2="17" />
                <line x1="10" x2="8" y1="9" y2="9" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.bilingualContracts.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.bilingualContracts.description }}
            </CardDescription>
          </CardHeader>
        </Card>

        <!-- Payments -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <rect width="20" height="14" x="2" y="5" rx="2" />
                <line x1="2" x2="22" y1="10" y2="10" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.payments.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.payments.description }}
            </CardDescription>
          </CardHeader>
        </Card>

        <!-- Maintenance -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.maintenance.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.maintenance.description }}
            </CardDescription>
          </CardHeader>
        </Card>

        <!-- I18n -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <circle cx="12" cy="12" r="10" />
                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                <path d="M2 12h20" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.i18n.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.i18n.description }}
            </CardDescription>
          </CardHeader>
        </Card>

        <!-- Security -->
        <Card class="transition-all hover:shadow-lg">
          <CardHeader>
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-primary/10">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
              </svg>
            </div>
            <CardTitle>{{ translations.features.security.title }}</CardTitle>
            <CardDescription>
              {{ translations.features.security.description }}
            </CardDescription>
          </CardHeader>
        </Card>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import Button from '@/components/ui/Button.vue'

definePageMeta({
  layout: 'default',
})

const { isAuthenticated, getDefaultRoute } = useAuth()
const { t, locale } = useI18n()

const translations = ref({
  heroTitle: '',
  heroSubtitle: '',
  getStarted: '',
  goToDashboard: '',
  features: {
    propertyManagement: { title: '', description: '' },
    bilingualContracts: { title: '', description: '' },
    payments: { title: '', description: '' },
    maintenance: { title: '', description: '' },
    i18n: { title: '', description: '' },
    security: { title: '', description: '' },
  },
})

const loadTranslations = () => {
  translations.value = {
    heroTitle: t('home.heroTitle'),
    heroSubtitle: t('home.heroSubtitle'),
    getStarted: t('home.getStarted'),
    goToDashboard: t('home.goToDashboard'),
    features: {
      propertyManagement: {
        title: t('home.features.propertyManagement.title'),
        description: t('home.features.propertyManagement.description'),
      },
      bilingualContracts: {
        title: t('home.features.bilingualContracts.title'),
        description: t('home.features.bilingualContracts.description'),
      },
      payments: {
        title: t('home.features.payments.title'),
        description: t('home.features.payments.description'),
      },
      maintenance: {
        title: t('home.features.maintenance.title'),
        description: t('home.features.maintenance.description'),
      },
      i18n: {
        title: t('home.features.i18n.title'),
        description: t('home.features.i18n.description'),
      },
      security: {
        title: t('home.features.security.title'),
        description: t('home.features.security.description'),
      },
    },
  }
}

onMounted(() => {
  loadTranslations()
})

watch(locale, () => {
  loadTranslations()
})
</script>
