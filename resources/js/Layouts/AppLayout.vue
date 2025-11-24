<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo & Brand Name -->
          <div class="flex-shrink-0">
            <InertiaLink :href="route('dashboard')" class="flex items-center space-x-2">
              <!-- You can place an SVG logo here if you have one -->
              <span class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ appName }}</span>
            </InertiaLink>
          </div>

          <!-- Desktop Navigation Links -->
          <div class="hidden md:flex md:items-center md:space-x-8">
            <InertiaLink :href="route('dashboard')" class="nav-link" :class="{ 'active': $page.component === 'Dashboard' }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
                Dashboard
            </InertiaLink>
            <InertiaLink :href="route('jobs.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('Jobs') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Posting
            </InertiaLink>
            <InertiaLink :href="route('talent-pool.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('TalentPool') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Talent Pool
            </InertiaLink>
            <InertiaLink v-if="$page.props.auth.role === 'superadmin'" :href="route('clients.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('Clients') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-1 4h1M3 21h18"/></svg>
                Clients
            </InertiaLink>
            
            <!-- Master Data Dropdown (Superadmin only) -->
            <div v-if="$page.props.auth.role === 'superadmin'" class="relative" @mouseleave="showingMasterData = false">
              <button @mouseover="showingMasterData = true" class="nav-link inline-flex items-center" :class="{ 'active': $page.component.startsWith('MasterData') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Master Data</span>
                <svg class="ml-1 h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
              </button>
              <transition name="fade">
                <div v-show="showingMasterData" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-20">
                  <InertiaLink :href="route('master-data.document-types.index')" class="dropdown-link">
                    <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 0h2a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2h2m0 0l-4 4-4-4"/></svg>
                    Document Types
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.users.index')" class="dropdown-link">
                    <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Users & Roles
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.job-categories.index')" class="dropdown-link">
                    <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h.01M7 21h.01M17 7h.01M17 3h.01M17 21h.01M12 7h.01M12 3h.01M12 21h.01M12 12h.01M7 12h.01M17 12h.01M12 17h.01M7 17h.01M17 17h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Job Categories
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.skills.index')" class="dropdown-link">
                    <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16v4m-2-2h4M13 17v4m-2-2h4M21 12h-4M17 5h2M17 19h2"/></svg>
                    Skills
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.locations.index')" class="dropdown-link">
                    <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Locations
                  </InertiaLink>
                </div>
              </transition>
            </div>
          </div>

          <!-- Right side: User menu, theme toggle -->
          <div class="flex items-center space-x-3">
            <button @click="toggleTheme" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
              <svg v-if="!darkMode" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
              <svg v-else class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9.66l-.71.71M4.05 19.95l-.71-.71M21 12h-1M4 12H3m15.66 5.66l-.71-.71M4.05 4.05l-.71.71" /></svg>
            </button>
            
            <!-- User Dropdown -->
            <div class="relative user-menu-container">
              <button @click="showUserMenu = !showUserMenu" class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <img :src="user?.profile_photo_url || `https://ui-avatars.com/api/?name=${user?.name}&background=random`" alt="Avatar" class="h-8 w-8 rounded-full" />
                <span class="hidden lg:inline text-gray-800 dark:text-gray-200 text-sm">{{ user?.name }}</span>
              </button>
              <transition name="fade">
                <div v-if="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-20">
                  <InertiaLink :href="route('profile.edit')" class="dropdown-link">Profile</InertiaLink>
                  <form @submit.prevent="logout" class="block">
                    <button type="submit" class="w-full text-left dropdown-link">Logout</button>
                  </form>
                </div>
              </transition>
            </div>
          </div>

          <!-- Hamburger Menu Button -->
          <div class="md:hidden flex items-center">
            <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
              <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Menu -->
      <div v-if="isMobileMenuOpen" class="md:hidden">
        <div class="pt-2 pb-3 space-y-1 px-2">
          <InertiaLink :href="route('dashboard')" class="mobile-nav-link" :class="{ 'active': $page.component === 'Dashboard' }">
            <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6"/></svg>
            Dashboard
          </InertiaLink>
          <InertiaLink :href="route('jobs.index')" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('Jobs') }">
            <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            Job Posting
          </InertiaLink>
          <InertiaLink :href="route('talent-pool.index')" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('TalentPool') }">
            <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            Talent Pool
          </InertiaLink>
          <InertiaLink v-if="$page.props.auth.role === 'superadmin'" :href="route('clients.index')" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('Clients') }">
            <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-1 4h1M3 21h18"/></svg>
            Clients
          </InertiaLink>
          
          <!-- Mobile Master Data Links -->
          <div v-if="$page.props.auth.role === 'superadmin'">
            <h3 class="px-3 pt-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master Data</h3>
            <div class="mt-1 space-y-1">
              <InertiaLink :href="route('master-data.document-types.index')" class="mobile-nav-link pl-6">
                <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 0h2a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2h2m0 0l-4 4-4-4"/></svg>
                Document Types
              </InertiaLink>
              <InertiaLink :href="route('master-data.users.index')" class="mobile-nav-link pl-6">
                <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Users & Roles
              </InertiaLink>
              <InertiaLink :href="route('master-data.job-categories.index')" class="mobile-nav-link pl-6">
                <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h.01M7 21h.01M17 7h.01M17 3h.01M17 21h.01M12 7h.01M12 3h.01M12 21h.01M12 12h.01M7 12h.01M17 12h.01M12 17h.01M7 17h.01M17 17h.01M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Categories
              </InertiaLink>
              <InertiaLink :href="route('master-data.skills.index')" class="mobile-nav-link pl-6">
                <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16v4m-2-2h4M13 17v4m-2-2h4M21 12h-4M17 5h2M17 19h2"/></svg>
                Skills
              </InertiaLink>
              <InertiaLink :href="route('master-data.locations.index')" class="mobile-nav-link pl-6">
                <svg class="h-5 w-5 mr-1 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Locations
              </InertiaLink>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main class="py-10">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        <div v-if="flashSuccess && showFlash" class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md" role="alert">
            <p>{{ flashSuccess }}</p>
        </div>
        <div v-if="flashError && showFlash" class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
            <p>{{ flashError }}</p>
        </div>

        <!-- Slot for page content -->
        <slot />
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ new Date().getFullYear() }} {{ appName }}. All rights reserved.
            </p>
        </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { usePage, Link as InertiaLink, router } from '@inertiajs/vue3'

const { props } = usePage()
const user = computed(() => props.auth?.user)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const flashSuccess = computed(() => usePage().props.flash?.success);
const flashError = computed(() => usePage().props.flash?.error);
const showFlash = ref(false);

const isMobileMenuOpen = ref(false);

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

// User menu toggle
const showUserMenu = ref(false)
const showingMasterData = ref(false)

// Close user menu when clicking outside
const closeUserMenu = (e) => {
  if (showUserMenu.value && !e.target.closest('.user-menu-container')) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  // Force default to light mode
  document.documentElement.classList.remove('dark');
  localStorage.setItem('darkMode', 'false');
  darkMode.value = false;

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
.nav-link {
  @apply text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition duration-150 ease-in-out text-sm font-medium;
  display: inline-flex; /* To align icon and text */
  align-items: center;
}
.nav-link.active {
  @apply text-primary-600 dark:text-primary-400;
}
.mobile-nav-link {
  @apply block py-2 px-3 text-base font-medium text-gray-500 dark:text-gray-400 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-800 dark:hover:text-gray-200;
  display: inline-flex; /* To align icon and text */
  align-items: center;
}
.mobile-nav-link.active {
  @apply bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300;
}
.dropdown-link {
  @apply block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600;
  display: inline-flex; /* To align icon and text */
  align-items: center;
}
.fade-enter-active,
.fade-leave-active {
  transition: opacity .2s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>