<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans antialiased">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-md transition-colors duration-300">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center space-x-4">
          <router-link :href="route('dashboard')" class="text-xl font-bold text-gray-800 dark:text-gray-200">
            {{ appName }}
          </router-link>
        </div>
        <div class="flex items-center space-x-4">
          <button @click="toggleTheme" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
            <svg v-if="!darkMode" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-9.66l-.71.71M4.05 19.95l-.71-.71M21 12h-1M4 12H3m15.66 5.66l-.71-.71M4.05 4.05l-.71.71" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3c.132 0 .263.005.393.015a9 9 0 018.592 8.592c.01.13.015.261.015.393 0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9z" />
            </svg>
          </button>
          <div class="relative" @click.away="showUserMenu = false">
            <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
              <img :src="user?.profile_photo_url || '/default-avatar.png'" alt="Avatar" class="h-8 w-8 rounded-full" />
              <span class="text-gray-800 dark:text-gray-200">{{ user?.name }}</span>
              <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </button>
            <transition name="fade">
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-20">
                <inertia-link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</inertia-link>
                <form @submit.prevent="logout" class="block">
                  <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Logout</button>
                </form>
              </div>
            </transition>
          </div>
        </div>
      </div>
    </header>

    <!-- Sidebar + Content -->
    <div class="flex">
      <!-- Sidebar -->
      <nav class="bg-white dark:bg-gray-800 w-64 min-h-screen border-r border-gray-200 dark:border-gray-700 transition-transform duration-300" :class="{ '-translate-x-64': !sidebarOpen }">
        <div class="p-4">
          <ul class="space-y-2">
            <li>
              <inertia-link :href="route('dashboard')" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" :class="{ 'bg-gray-200 dark:bg-gray-700': $page.component === 'Dashboard' }">
                <svg class="h-5 w-5 mr-2 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" /></svg>
                Dashboard
              </inertia-link>
            </li>
            <li>
              <inertia-link :href="route('master-data.index')" class="flex items-center px-3 py-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors" :class="{ 'bg-gray-200 dark:bg-gray-700': $page.component.startsWith('MasterData') }">
                <svg class="h-5 w-5 mr-2 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2h2" /></svg>
                Master Data
              </inertia-link>
            </li>
            <!-- Add more menu items here -->
          </ul>
        </div>
      </nav>

      <!-- Main content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, Link as InertiaLink } from '@inertiajs/vue3'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

const { props } = usePage()
const user = computed(() => props.auth?.user)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

// Theme handling (light / dark)
const darkMode = ref(localStorage.getItem('darkMode') === 'true')
const toggleTheme = () => {
  darkMode.value = !darkMode.value
  document.documentElement.classList.toggle('dark', darkMode.value)
  localStorage.setItem('darkMode', darkMode.value)
}
onMounted(() => {
  document.documentElement.classList.toggle('dark', darkMode.value)
})

// Sidebar toggle for mobile (optional)
const sidebarOpen = ref(true)

// Logout helper – uses Inertia post
const logout = () => {
  // Inertia post request to Laravel logout route
  // Assuming route name 'logout' exists (POST)
  // eslint-disable-next-line no-undef
  Inertia.post(route('logout'))
}
</script>

<style scoped>
/* Glassmorphism effect for sidebar */
nav {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: saturate(180%) blur(12px);
}
.dark nav {
  background: rgba(31, 41, 55, 0.8);
}

/* Fade transition for dropdown */
.fade-enter-active, .fade-leave-active {
  transition: opacity .2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
