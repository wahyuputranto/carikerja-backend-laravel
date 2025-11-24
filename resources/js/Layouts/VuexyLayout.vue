<script setup>
import { useConfigStore } from '@core/stores/config';
import { VerticalNavLayout } from '@layouts';
import { Link, usePage } from '@inertiajs/vue3';

const configStore = useConfigStore();
const page = usePage();

// Inject skin classes
const { layoutAttrs, injectSkinClasses } = useSkins();
injectSkinClasses();

// Navigation items
const navItems = [
  {
    title: 'Dashboard',
    icon: 'tabler-smart-home',
    to: '/dashboard',
  },
  { heading: 'Master Data' },
  {
    title: 'Document Types',
    icon: 'tabler-file-type-doc',
    to: '/admin/document-types',
  },
  {
    title: 'Users & Roles',
    icon: 'tabler-users',
    to: '/admin/users',
  },
];
</script>

<template>
  <VerticalNavLayout v-bind="layoutAttrs">
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="tabler-menu-2" />
        </IconBtn>

        <VSpacer />

        <!-- User Profile Dropdown -->
        <VMenu offset-y>
          <template #activator="{ props }">
            <VBtn
              icon
              v-bind="props"
            >
              <VAvatar size="38">
                <VImg src="https://api.dicebear.com/7.x/avataaars/svg?seed=Felix" />
              </VAvatar>
            </VBtn>
          </template>

          <VList>
            <VListItem>
              <VListItemTitle>{{ $page.props.auth.user.name }}</VListItemTitle>
              <VListItemSubtitle>{{ $page.props.auth.user.email }}</VListItemSubtitle>
            </VListItem>
            <VDivider />
            <VListItem>
              <Link :href="route('profile.edit')" class="text-decoration-none text-body-1">
                <VListItemTitle>Profile</VListItemTitle>
              </Link>
            </VListItem>
            <VListItem>
              <Link :href="route('logout')" method="post" as="button" class="text-decoration-none text-body-1 w-100 text-left">
                <VListItemTitle>Logout</VListItemTitle>
              </Link>
            </VListItem>
          </VList>
        </VMenu>
      </div>
    </template>

    <!-- 👉 Navigation Items -->
    <template #navigation>
      <VList>
        <template v-for="(item, index) in navItems" :key="index">
          <!-- Heading -->
          <VListSubheader v-if="item.heading" class="text-uppercase">
            {{ item.heading }}
          </VListSubheader>

          <!-- Nav Item -->
          <VListItem
            v-else
            :to="item.to"
            :active="page.url === item.to"
          >
            <template #prepend>
              <VIcon :icon="item.icon" />
            </template>
            <VListItemTitle>{{ item.title }}</VListItemTitle>
          </VListItem>
        </template>
      </VList>
    </template>

    <!-- 👉 Pages -->
    <slot />
  </VerticalNavLayout>
</template>

<style lang="scss">
@use "@layouts/styles/_default-layout";
</style>
