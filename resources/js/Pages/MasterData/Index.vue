<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <Head title="Master Data" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Master Data Management
                </h2>
                <p class="text-gray-600 dark:text-gray-400">
                    Manage system reference data and configurations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Document Types -->
                <Link :href="route('master-data.document-types.index')" 
                      class="premium-card group block hover:border-primary-500/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m2 0a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2v-6a2 2 0 012-2h2" />
                            </svg>
                        </div>
                        <span class="text-gray-400 group-hover:text-primary-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Document Types</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Manage required documents, mandatory flags, and chunking options.
                    </p>
                </Link>

                <!-- Users Management -->
                <Link :href="route('master-data.users.index')" 
                      class="premium-card group block hover:border-primary-500/50">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-xl text-primary-600 dark:text-primary-400 group-hover:scale-110 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-gray-400 group-hover:text-primary-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Users & Roles</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">
                        Manage system users, assign roles (Admin, Client, Vendor).
                    </p>
                </Link>
            </div>
        </div>
    </div>
</template>
