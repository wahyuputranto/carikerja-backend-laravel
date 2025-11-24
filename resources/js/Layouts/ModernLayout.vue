<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();
const drawer = ref(false);

// Main navigation items
const navItems = [
  {
    title: 'Dashboard',
    icon: 'mdi-view-dashboard-outline',
    to: '/dashboard',
  },
  {
    title: 'Master Data',
    icon: 'mdi-database-outline',
    children: [
      {
        title: 'Document Types',
        to: '/admin/document-types',
      },
      {
        title: 'Users & Roles',
        to: '/admin/users',
      },
    ],
  },
];
</script>

<template>
  <v-app>
    <!-- Top App Bar -->
    <v-app-bar
      elevation="1"
      color="white"
      height="64"
    >
      <!-- Logo -->
      <div class="d-flex align-center ml-4">
        <v-icon color="primary" size="32" class="mr-2">mdi-briefcase</v-icon>
        <span class="text-h6 font-weight-bold">CariKerja</span>
      </div>

      <v-spacer></v-spacer>

      <!-- Desktop Navigation -->
      <div class="d-none d-lg-flex align-center mx-4">
        <template v-for="item in navItems" :key="item.title">
          <!-- Menu with children -->
          <v-menu v-if="item.children" open-on-hover>
            <template v-slot:activator="{ props }">
              <v-btn
                v-bind="props"
                variant="text"
                :prepend-icon="item.icon"
                class="mx-1 text-none"
                color="grey-darken-2"
              >
                {{ item.title }}
                <v-icon size="small" class="ml-1">mdi-chevron-down</v-icon>
              </v-btn>
            </template>
            <v-list>
              <Link
                v-for="child in item.children"
                :key="child.to"
                :href="child.to"
                class="text-decoration-none"
              >
                <v-list-item
                  :title="child.title"
                  :active="page.url === child.to"
                ></v-list-item>
              </Link>
            </v-list>
          </v-menu>

          <!-- Simple link -->
          <Link
            v-else
            :href="item.to"
            class="text-decoration-none"
          >
            <v-btn
              variant="text"
              :prepend-icon="item.icon"
              class="mx-1 text-none"
              :color="page.url === item.to ? 'primary' : 'grey-darken-2'"
            >
              {{ item.title }}
            </v-btn>
          </Link>
        </template>
      </div>

      <v-spacer></v-spacer>

      <!-- Right side icons -->
      <div class="d-flex align-center mr-4">
        <!-- Search -->
        <v-btn icon variant="text" size="small" class="mx-1">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>

        <!-- Theme toggle -->
        <v-btn icon variant="text" size="small" class="mx-1">
          <v-icon>mdi-theme-light-dark</v-icon>
        </v-btn>

        <!-- Notifications -->
        <v-btn icon variant="text" size="small" class="mx-1">
          <v-badge color="error" content="3" overlap>
            <v-icon>mdi-bell-outline</v-icon>
          </v-badge>
        </v-btn>

        <!-- User Menu -->
        <v-menu offset-y>
          <template v-slot:activator="{ props }">
            <v-btn
              icon
              v-bind="props"
              class="ml-2"
            >
              <v-avatar color="primary" size="36">
                <span class="text-subtitle-2 font-weight-bold">
                  {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                </span>
              </v-avatar>
            </v-btn>
          </template>

          <v-list min-width="200">
            <v-list-item>
              <v-list-item-title class="font-weight-bold">
                {{ page.props.auth.user.name }}
              </v-list-item-title>
              <v-list-item-subtitle>
                {{ page.props.auth.user.email }}
              </v-list-item-subtitle>
            </v-list-item>
            
            <v-divider class="my-2"></v-divider>
            
            <Link href="/profile" class="text-decoration-none">
              <v-list-item prepend-icon="mdi-account-outline">
                <v-list-item-title>Profile</v-list-item-title>
              </v-list-item>
            </Link>
            
            <Link href="/logout" method="post" as="button" class="text-decoration-none w-100">
              <v-list-item prepend-icon="mdi-logout">
                <v-list-item-title>Logout</v-list-item-title>
              </v-list-item>
            </Link>
          </v-list>
        </v-menu>

        <!-- Mobile menu toggle -->
        <v-btn
          icon
          variant="text"
          @click="drawer = !drawer"
          class="d-lg-none ml-2"
        >
          <v-icon>mdi-menu</v-icon>
        </v-btn>
      </div>
    </v-app-bar>

    <!-- Mobile Navigation Drawer -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      location="right"
    >
      <v-list>
        <v-list-item
          :prepend-avatar="`https://ui-avatars.com/api/?name=${page.props.auth.user.name}&background=5E35B1&color=fff`"
          :title="page.props.auth.user.name"
          :subtitle="page.props.auth.user.email"
        ></v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list density="compact" nav>
        <template v-for="item in navItems" :key="item.title">
          <!-- Group with children -->
          <v-list-group v-if="item.children" :value="item.title">
            <template v-slot:activator="{ props }">
              <v-list-item
                v-bind="props"
                :prepend-icon="item.icon"
                :title="item.title"
              ></v-list-item>
            </template>

            <Link
              v-for="child in item.children"
              :key="child.to"
              :href="child.to"
              class="text-decoration-none"
            >
              <v-list-item
                :title="child.title"
                :active="page.url === child.to"
              ></v-list-item>
            </Link>
          </v-list-group>

          <!-- Simple item -->
          <Link
            v-else
            :href="item.to"
            class="text-decoration-none"
          >
            <v-list-item
              :prepend-icon="item.icon"
              :title="item.title"
              :active="page.url === item.to"
            ></v-list-item>
          </Link>
        </template>
      </v-list>
    </v-navigation-drawer>

    <!-- Main Content -->
    <v-main class="bg-grey-lighten-4">
      <v-container fluid class="pa-6">
        <slot />
      </v-container>
    </v-main>
  </v-app>
</template>

<style scoped>
.v-btn {
  text-transform: none !important;
}
</style>
