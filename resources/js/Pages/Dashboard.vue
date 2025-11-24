<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <Head title="Dashboard" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Dashboard
                </h2>
            </div>
            
            <div class="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg premium-card">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    You're logged in!
                </div>
            </div>
        </div>
    </div>
</template>
