<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- Navigation Bar -->
    <nav class="bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Left side: Hamburger + Logo -->
          <div class="flex items-center space-x-3">
            <!-- Hamburger Menu Button (Mobile) -->
            <div class="md:hidden">
              <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 transition-colors">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path :class="{'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                  <path :class="{'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>

            <!-- Logo & Brand Name -->
            <div class="flex-shrink-0">
              <InertiaLink :href="route('dashboard')" class="flex items-center">
                <img src="/images/zmijobs-logo.png" alt="Logo" class="h-10 w-auto" />
              </InertiaLink>
            </div>
          </div>

          <!-- Desktop Navigation Links -->
          <div class="hidden md:flex md:items-center md:space-x-8">
            <InertiaLink :href="route('dashboard')" class="nav-link" :class="{ 'active': $page.component === 'Dashboard' }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </InertiaLink>
            <InertiaLink v-if="hasPermission('menu.job_posting')" :href="route('jobs.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('Jobs') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                Job Posting
            </InertiaLink>
            <InertiaLink :href="route('talent-pool.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('TalentPool') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Talent Pool
            </InertiaLink>
            <InertiaLink v-if="hasPermission('menu.clients')" :href="route('clients.index')" class="nav-link" :class="{ 'active': $page.component.startsWith('Clients') }">
                <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-1 4h1M3 21h18"/></svg>
                Clients
            </InertiaLink>
            
            <!-- Master Data Dropdown (Superadmin only) -->
            <div v-if="hasPermission('menu.master_data')" class="relative">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="nav-link inline-flex items-center cursor-pointer" :class="{ 'active': $page.component.startsWith('MasterData') }">
                            <svg class="h-5 w-5 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor"><path d="M228.25,63.07l-4.66-2.69a23.6,23.6,0,0,0,0-8.76l4.66-2.69a8,8,0,0,0-8-13.86l-4.67,2.7A23.92,23.92,0,0,0,208,33.38V28a8,8,0,0,0-16,0v5.38a23.92,23.92,0,0,0-7.58,4.39l-4.67-2.7a8,8,0,1,0-8,13.86l4.66,2.69a23.6,23.6,0,0,0,0,8.76l-4.66,2.69a8,8,0,0,0,4,14.93,7.92,7.92,0,0,0,4-1.07l4.67-2.7A23.92,23.92,0,0,0,192,78.62V84a8,8,0,0,0,16,0V78.62a23.92,23.92,0,0,0,7.58-4.39l4.67,2.7a7.92,7.92,0,0,0,4,1.07,8,8,0,0,0,4-14.93ZM192,56a8,8,0,1,1,8,8A8,8,0,0,1,192,56Zm29.35,48.11a8,8,0,0,0-6.57,9.21A88.85,88.85,0,0,1,216,128a87.62,87.62,0,0,1-22.24,58.41,79.66,79.66,0,0,0-36.06-28.75,48,48,0,1,0-59.4,0,79.66,79.66,0,0,0-36.06,28.75A88,88,0,0,1,128,40a88.76,88.76,0,0,1,14.68,1.22,8,8,0,0,0,2.64-15.78,103.92,103.92,0,1,0,85.24,85.24A8,8,0,0,0,221.35,104.11ZM96,120a32,32,0,1,1,32,32A32,32,0,0,1,96,120ZM74.08,197.5a64,64,0,0,1,107.84,0,87.83,87.83,0,0,1-107.84,0Z"/></svg>
                            <span>Master Data</span>
                            <svg class="ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('master-data.document-types.index')">
                            Document Types
                        </DropdownLink>
                        <DropdownLink :href="route('master-data.users.index')">
                            Users
                        </DropdownLink>
                        <DropdownLink :href="route('master-data.roles.index')">
                            Roles
                        </DropdownLink>
                        <DropdownLink :href="route('master-data.job-categories.index')">
                            Job Categories
                        </DropdownLink>
                        <DropdownLink :href="route('master-data.audit-trails.index')" v-if="false">
                            <!-- Removed from here -->
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>
          </div>

          <!-- Right side: Notification, User menu, theme toggle -->
          <div class="flex items-center space-x-3">
            <!-- Notification Bell -->
            <NotificationBell />
            
            <button @click="toggleTheme" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
              <svg v-if="!darkMode" class="h-6 w-6 text-gray-800 dark:text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
              <svg v-else class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9.66l-.71.71M4.05 19.95l-.71-.71M21 12h-1M4 12H3m15.66 5.66l-.71-.71M4.05 4.05l-.71.71" /></svg>
            </button>
            
            <!-- User Dropdown -->
            <div class="relative">
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <button class="flex items-center space-x-2 p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors focus:outline-none">
                            <img :src="user?.profile_photo_url || `https://ui-avatars.com/api/?name=${user?.name}&background=random`" alt="Avatar" class="h-8 w-8 rounded-full object-cover" />
                            <span class="hidden lg:inline text-gray-800 dark:text-gray-200 text-sm font-medium">{{ user?.name }}</span>
                            <svg class="ml-1 h-4 w-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                        </button>
                    </template>

                    <template #content>
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            Manage Account
                        </div>
                        <DropdownLink :href="route('profile.edit')">
                            Profile
                        </DropdownLink>
                        <DropdownLink :href="route('admin-notifications.inbox')">
                            Inbox Notifikasi
                        </DropdownLink>
                        <DropdownLink v-if="hasPermission('menu.master_data')" :href="route('master-data.audit-trails.index')">
                            Audit Trail
                        </DropdownLink>
                        <div class="border-t border-gray-100 dark:border-gray-600"></div>
                        <form @submit.prevent="logout">
                            <DropdownLink as="button">
                                Logout
                            </DropdownLink>
                        </form>
                    </template>
                </Dropdown>
            </div>
          </div>
        </div>
      </div>

      <!-- Mobile Navigation Sidebar -->
      <!-- Backdrop Overlay -->
      <Transition
        enter-active-class="transition-opacity ease-linear duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-linear duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="isMobileMenuOpen" @click="isMobileMenuOpen = false" class="fixed inset-0 bg-gray-600 bg-opacity-75 z-40 md:hidden"></div>
      </Transition>

      <!-- Sidebar Drawer -->
      <Transition
        enter-active-class="transition ease-in-out duration-300 transform"
        enter-from-class="-translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition ease-in-out duration-300 transform"
        leave-from-class="translate-x-0"
        leave-to-class="-translate-x-full"
      >
        <div v-if="isMobileMenuOpen" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-xl md:hidden overflow-y-auto">
          <!-- Sidebar Header -->
          <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <span class="text-xl font-bold text-gray-800 dark:text-white">Menu</span>
            <button @click="isMobileMenuOpen = false" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Sidebar Navigation -->
          <div class="pt-2 pb-3 space-y-1 px-2">
            <InertiaLink :href="route('dashboard')" @click="isMobileMenuOpen = false" class="mobile-nav-link" :class="{ 'active': $page.component === 'Dashboard' }">
              <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
              Dashboard
            </InertiaLink>
            <InertiaLink v-if="hasPermission('menu.job_posting')" :href="route('jobs.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('Jobs') }">
              <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
              Job Posting
            </InertiaLink>
            <InertiaLink :href="route('talent-pool.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('TalentPool') }">
              <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
              Talent Pool
            </InertiaLink>
            <InertiaLink v-if="hasPermission('menu.clients')" :href="route('clients.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link" :class="{ 'active': $page.component.startsWith('Clients') }">
              <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
              Clients
            </InertiaLink>

            <!-- Master Data Dropdown in Mobile -->
            <div v-if="hasPermission('menu.master_data')" class="space-y-1">
              <button @click="isMasterDataOpen = !isMasterDataOpen" class="mobile-nav-link w-full text-left flex items-center justify-between" :class="{ 'active': $page.component.startsWith('MasterData') }">
                <div class="flex items-center">
                  <svg class="h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor"><path d="M228.25,63.07l-4.66-2.69a23.6,23.6,0,0,0,0-8.76l4.66-2.69a8,8,0,0,0-8-13.86l-4.67,2.7A23.92,23.92,0,0,0,208,33.38V28a8,8,0,0,0-16,0v5.38a23.92,23.92,0,0,0-7.58,4.39l-4.67-2.7a8,8,0,1,0-8,13.86l4.66,2.69a23.6,23.6,0,0,0,0,8.76l-4.66,2.69a8,8,0,0,0,4,14.93,7.92,7.92,0,0,0,4-1.07l4.67-2.7A23.92,23.92,0,0,0,192,78.62V84a8,8,0,0,0,16,0V78.62a23.92,23.92,0,0,0,7.58-4.39l4.67,2.7a7.92,7.92,0,0,0,4,1.07,8,8,0,0,0,4-14.93ZM192,56a8,8,0,1,1,8,8A8,8,0,0,1,192,56Zm29.35,48.11a8,8,0,0,0-6.57,9.21A88.85,88.85,0,0,1,216,128a87.62,87.62,0,0,1-22.24,58.41,79.66,79.66,0,0,0-36.06-28.75,48,48,0,1,0-59.4,0,79.66,79.66,0,0,0-36.06,28.75A88,88,0,0,1,128,40a88.76,88.76,0,0,1,14.68,1.22,8,8,0,0,0,2.64-15.78,103.92,103.92,0,1,0,85.24,85.24A8,8,0,0,0,221.35,104.11ZM96,120a32,32,0,1,1,32,32A32,32,0,0,1,96,120ZM74.08,197.5a64,64,0,0,1,107.84,0,87.83,87.83,0,0,1-107.84,0Z"/></svg>
                  Master Data
                </div>
                <svg class="h-5 w-5 transition-transform" :class="{ 'rotate-180': isMasterDataOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              
              <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95"
              >
                <div v-if="isMasterDataOpen" class="pl-4 space-y-1">
                  <InertiaLink :href="route('master-data.document-types.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link text-sm" :class="{ 'active': $page.component.startsWith('MasterData/DocumentTypes') }">
                    Document Types
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.users.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link text-sm" :class="{ 'active': $page.component.startsWith('MasterData/Users') }">
                    Users
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.roles.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link text-sm" :class="{ 'active': $page.component.startsWith('MasterData/Roles') }">
                    Roles
                  </InertiaLink>
                  <InertiaLink :href="route('master-data.job-categories.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link text-sm" :class="{ 'active': $page.component.startsWith('MasterData/JobCategories') }">
                    Job Categories
                  </InertiaLink>

                </div>
              </Transition>
            </div>

            <!-- User Profile Section in Mobile -->
            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-4">
              <div class="px-4 py-2">
                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $page.props.auth.user.name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $page.props.auth.user.email }}</div>
              </div>
              <InertiaLink :href="route('profile.edit')" @click="isMobileMenuOpen = false" class="mobile-nav-link">
                <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profile
              </InertiaLink>
              <InertiaLink :href="route('admin-notifications.inbox')" @click="isMobileMenuOpen = false" class="mobile-nav-link">
                <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                Inbox Notifikasi
              </InertiaLink>
              <InertiaLink v-if="hasPermission('menu.master_data')" :href="route('master-data.audit-trails.index')" @click="isMobileMenuOpen = false" class="mobile-nav-link">
                <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Audit Trail
              </InertiaLink>
              <InertiaLink :href="route('logout')" method="post" as="button" @click="isMobileMenuOpen = false" class="mobile-nav-link w-full text-left">
                <svg class="h-5 w-5 mr-3 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
              </InertiaLink>
            </div>
          </div>
        </div>
      </Transition>
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
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import NotificationBell from '@/Components/NotificationBell.vue'

const { props } = usePage()
const user = computed(() => props.auth?.user)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

const flashSuccess = computed(() => usePage().props.flash?.success);
const flashError = computed(() => usePage().props.flash?.error);
const showFlash = ref(false);

const isMobileMenuOpen = ref(false);
const isMasterDataOpen = ref(false);

watch([flashSuccess, flashError], () => {
    showFlash.value = true;
    if (flashSuccess.value || flashError.value) {
        setTimeout(() => {
            showFlash.value = false;
        }, 3000);
    }
}, { deep: true });

// Theme handling
const darkMode = ref(false);

const toggleTheme = () => {
  darkMode.value = !darkMode.value;
  if (darkMode.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('darkMode', 'true');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('darkMode', 'false');
  }
};

const showUserMenu = ref(false);

const closeUserMenu = (e) => {
  if (showUserMenu.value && !e.target.closest('.user-menu-container')) {
    showUserMenu.value = false;
  }
};

onMounted(() => {
  // Check localStorage for saved preference, default to light mode
  if (localStorage.getItem('darkMode') === 'true') {
    darkMode.value = true;
    document.documentElement.classList.add('dark');
  } else {
    darkMode.value = false;
    document.documentElement.classList.remove('dark');
    localStorage.setItem('darkMode', 'false');
  }
  document.addEventListener('click', closeUserMenu);

  // Auto-expand Master Data menu if active
  if (usePage().component.startsWith('MasterData')) {
    isMasterDataOpen.value = true;
  }
});

onUnmounted(() => {
  document.removeEventListener('click', closeUserMenu);
});


// Logout helper – uses Inertia post
const logout = () => {
  router.post(route('logout'));
};

const hasPermission = (permission) => {
    const user = usePage().props.auth.user;
    if (user?.role?.slug === 'superadmin') return true;

    const permissions = usePage().props.auth.permissions || [];
    if (permissions.includes('*')) return true;
    return permissions.includes(permission);
};
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
  @apply block py-3 px-4 text-base font-medium text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white transition-colors duration-150;
  display: flex;
  align-items: center;
}
.mobile-nav-link.active {
  @apply bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 font-semibold;
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