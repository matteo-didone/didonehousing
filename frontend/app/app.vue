<template>
  <div>
    <NuxtRouteAnnouncer />
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup lang="ts">
// Restore auth on app load
const { restoreAuth } = useAuth()

onMounted(async () => {
  await restoreAuth()
})
</script>
