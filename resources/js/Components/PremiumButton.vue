<template>
  <button
    :type="type"
    class="relative inline-flex items-center justify-center px-6 py-3 overflow-hidden font-medium text-white transition-all duration-300 bg-primary-600 rounded-xl group hover:bg-primary-700 hover:scale-105 hover:shadow-lg hover:shadow-primary-500/40 focus:outline-none focus:ring-2 focus:ring-primary-400 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
  >
    <span class="absolute top-0 right-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-primary-800 rounded group-hover:-mr-4 group-hover:-mt-4">
      <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white opacity-10"></span>
    </span>
    <span class="absolute bottom-0 rotate-180 left-0 inline-block w-4 h-4 transition-all duration-500 ease-in-out bg-primary-800 rounded group-hover:-ml-4 group-hover:-mb-4">
      <span class="absolute top-0 right-0 w-5 h-5 rotate-45 translate-x-1/2 -translate-y-1/2 bg-white opacity-10"></span>
    </span>
    <span class="absolute bottom-0 left-0 w-full h-full transition-all duration-500 ease-in-out delay-200 -translate-x-full bg-primary-500 rounded-xl group-hover:translate-x-0"></span>
    <span class="relative w-full text-left transition-colors duration-200 ease-in-out group-hover:text-white flex items-center gap-2">
      <slot />
    </span>
  </button>
</template>

<script setup>
defineProps({
  type: {
    type: String,
    default: 'button',
  },
});
</script>
