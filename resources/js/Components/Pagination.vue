<template>
  <div v-if="links.length > 3" class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6 dark:bg-gray-800 dark:border-gray-700">
    <div class="flex flex-1 justify-between sm:hidden">
      <component 
        :is="links[0].url ? 'Link' : 'span'"
        :href="links[0].url" 
        v-html="links[0].label" 
        class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
        :class="{ 'opacity-50 cursor-not-allowed': !links[0].url }"
      />
      <component 
        :is="links[links.length - 1].url ? 'Link' : 'span'"
        :href="links[links.length - 1].url" 
        v-html="links[links.length - 1].label" 
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700"
        :class="{ 'opacity-50 cursor-not-allowed': !links[links.length - 1].url }"
      />
    </div>
    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-400">
          Showing
          <span class="font-medium">{{ from }}</span>
          to
          <span class="font-medium">{{ to }}</span>
          of
          <span class="font-medium">{{ total }}</span>
          results
        </p>
      </div>
      <div>
        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          <template v-for="(link, index) in links" :key="index">
            <Link
              v-if="link.url"
              :href="link.url"
              v-html="link.label"
              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold"
              :class="{
                'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600': link.active,
                'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0 dark:text-gray-300 dark:ring-gray-600 dark:hover:bg-gray-700': !link.active
              }"
            />
            <span
              v-else
              v-html="link.label"
              class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-400 ring-1 ring-inset ring-gray-300 cursor-not-allowed dark:text-gray-600 dark:ring-gray-600"
            />
          </template>
        </nav>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array,
  from: Number,
  to: Number,
  total: Number,
});
</script>
