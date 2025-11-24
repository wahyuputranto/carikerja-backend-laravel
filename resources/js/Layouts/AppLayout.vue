
<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans antialiased">
    <!-- Flash Messages -->
    <div v-if="flashSuccess && showFlash" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
        <p>{{ flashSuccess }}</p>
    </div>
    <div v-if="flashError && showFlash" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
        <p>{{ flashError }}</p>
    </div>

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
          <div class="relative user-menu-container">
            <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
              <img :src="user?.profile_photo_url || '/default-avatar.png'" alt="Avatar" class="h-8 w-8 rounded-full" />
              <span class="text-gray-800 dark:text-gray-200">{{ user?.name }}</span>
              <svg class="h-4 w-4 text-gray-600 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </button>
            <transition name="fade">
              <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-20">
                <InertiaLink :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Profile</InertiaLink>
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
    <div class="flex relative overflow-hidden">
      <!-- Sidebar -->
      <nav class="glass dark:glass-dark w-64 min-h-screen border-r border-gray-200/50 dark:border-gray-700/50
                  transition-transform duration-300 z-10"
           :class="{ '-translate-x-64': !sidebarOpen }">
        <div class="p-4">
          <ul class="space-y-2">
            <li>
              <inertia-link :href="route('dashboard')"
                            class="flex items-center px-3 py-2 rounded-xl transition-all duration-200 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 group"
                            :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium': $page.component === 'Dashboard' }">
                <svg class="h-5 w-5 mr-3 transition-transform group-hover:scale-110"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
                Dashboard
              </inertia-link>
            </li>

            <li v-if="$page.props.auth.role === 'superadmin'">
              <button @click="showingMasterData = !showingMasterData"
                      class="w-full flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-200 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 group"
                      :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium': $page.component.startsWith('MasterData') }">
                <div class="flex items-center">
                  <svg class="h-5 w-5 mr-3 transition-transform group-hover:scale-110"
                       fill="none" stroke="currentColor" viewBox="0 0 24 24"
                       xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round"
                       stroke-linejoin="round" stroke-width="2"
                       d="M9 12h6m2 0a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2h2"/></svg>
                  Master Data
                </div>
                <svg class="h-4 w-4 transition-transform duration-200"
                     :class="{ 'rotate-180': showingMasterData }"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              
              <transition name="fade">
                <ul v-show="showingMasterData" class="mt-2 ml-8 space-y-1">
                  <li>
                    <inertia-link :href="route('master-data.document-types.index')"
                                  class="block px-3 py-2 rounded-lg text-sm transition-colors hover:text-primary-600 dark:hover:text-primary-400"
                                  :class="{ 'text-primary-600 dark:text-primary-400 font-medium': $page.component.startsWith('MasterData/DocumentTypes') }">
                      Document Types
                    </inertia-link>
                  </li>
                  <li>
                    <inertia-link :href="route('master-data.users.index')"
                                  class="block px-3 py-2 rounded-lg text-sm transition-colors hover:text-primary-600 dark:hover:text-primary-400"
                                  :class="{ 'text-primary-600 dark:text-primary-400 font-medium': $page.component.startsWith('MasterData/Users') }">
                      Users & Roles
                    </inertia-link>
                  </li>
                </ul>
              </transition>
            </li>

            <li>
              <inertia-link :href="route('jobs.index')"
                            class="flex items-center px-3 py-2 rounded-xl transition-all duration-200 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 group"
                            :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium': $page.component.startsWith('Jobs') }">
                <svg class="h-5 w-5 mr-3 transition-transform group-hover:scale-110"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Posting
              </inertia-link>
            </li>

            <li>
              <inertia-link :href="route('talent-pool.index')"
                            class="flex items-center px-3 py-2 rounded-xl transition-all duration-200 hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:text-primary-600 dark:hover:text-primary-400 group"
                            :class="{ 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium': $page.component.startsWith('TalentPool') }">
                <svg class="h-5 w-5 mr-3 transition-transform group-hover:scale-110"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round"
                     stroke-linejoin="round" stroke-width="2"
                     d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Talent Pool
              </inertia-link>
            </li>
          </ul>
        </div>
      </nav>

      <!-- Main content -->
      <main class="flex-1 p-6 overflow-y-auto h-screen scroll-smooth">
        <transition name="slide-up" mode="out-in">
          <div :key="$page.url">
            <slot />
          </div>
        </transition>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, Link as InertiaLink, router } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js'

const { props } = usePage()
const user = computed(() => props.auth?.user)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const flashSuccess = computed(() => usePage().props.flash?.success);
const flashError = computed(() => usePage().props.flash?.error);
const showFlash = ref(false);

watch([flashSuccess, flashError], () => {
    showFlash.value = true;
    if (flashSuccess.value || flashError.value) {
        setTimeout(() => {
            showFlash.value = false;
        }, 3000);
    }
}, { deep: true });

// Theme handling (light / dark)
const darkMode = ref(localStorage.getItem('darkMode') === 'true')
const toggleTheme = () => {
  darkMode.value = !darkMode.value
  document.documentElement.classList.toggle('dark', darkMode.value)
  localStorage.setItem('darkMode', darkMode.value)
}

// Sidebar toggle (mobile)
const sidebarOpen = ref(true)
const showUserMenu = ref(false)
const showingMasterData = ref(false)

// Close user menu when clicking outside
const closeUserMenu = (e) => {
  if (showUserMenu.value && !e.target.closest('.user-menu-container')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.documentElement.classList.toggle('dark', darkMode.value)
  document.addEventListener('click', closeUserMenu)
})

onUnmounted(() => {
  document.removeEventListener('click', closeUserMenu)
})

// Logout helper – uses Inertia post
const logout = () => {
  router.post(route('logout'))
}
</script>

<style scoped>
/* Fade transition for dropdown */
.fade-enter-active,
.fade-leave-active {
  transition: opacity .2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
