<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-8">
      Landlord Dashboard
    </h1>

    <!-- Welcome Message -->
    <div class="card mb-8">
      <h2 class="text-xl font-semibold mb-2">
        Welcome back, {{ user?.first_name }}!
      </h2>
      <p class="text-gray-600 dark:text-gray-400">
        Manage your properties and listings from here.
      </p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
      <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <NuxtLink to="/landlord/properties/create" class="card hover:shadow-lg transition-shadow">
          <div class="text-3xl mb-2">➕</div>
          <h3 class="font-semibold">Add New Property</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            List a new property for rental
          </p>
        </NuxtLink>

        <NuxtLink to="/landlord/properties" class="card hover:shadow-lg transition-shadow">
          <div class="text-3xl mb-2">🏠</div>
          <h3 class="font-semibold">My Properties</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            View and manage all properties
          </p>
        </NuxtLink>

        <NuxtLink to="/landlord/listings" class="card hover:shadow-lg transition-shadow">
          <div class="text-3xl mb-2">📋</div>
          <h3 class="font-semibold">My Listings</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            View active listings
          </p>
        </NuxtLink>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="mb-8">
      <h2 class="text-lg font-semibold mb-4">Overview</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card">
          <p class="text-sm text-gray-600 dark:text-gray-400">Total Properties</p>
          <p class="text-3xl font-bold mt-2">0</p>
        </div>

        <div class="card">
          <p class="text-sm text-gray-600 dark:text-gray-400">Published Listings</p>
          <p class="text-3xl font-bold mt-2">0</p>
        </div>

        <div class="card">
          <p class="text-sm text-gray-600 dark:text-gray-400">Pending Review</p>
          <p class="text-3xl font-bold mt-2 text-yellow-600">0</p>
        </div>

        <div class="card">
          <p class="text-sm text-gray-600 dark:text-gray-400">Active Leases</p>
          <p class="text-3xl font-bold mt-2 text-green-600">0</p>
        </div>
      </div>
    </div>

    <!-- Recent Activity -->
    <div>
      <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
      <div class="card">
        <p class="text-gray-600 dark:text-gray-400 text-center py-8">
          No recent activity
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
definePageMeta({
  layout: 'default',
  middleware: 'auth',
})

const { user, isLandlord } = useAuth()

// Redirect if not landlord
if (!isLandlord.value) {
  navigateTo('/')
}
</script>
