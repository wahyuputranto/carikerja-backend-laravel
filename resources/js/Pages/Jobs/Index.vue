<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    jobs: Object,
});

const getStatusBadge = (status) => {
    const badges = {
        DRAFT: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        PUBLISHED: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        CLOSED: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    };
    return badges[status] || badges.DRAFT;
};
</script>

<template>
    <Head title="Job Posting" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Job Posting</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage job vacancies and postings.</p>
                </div>
                <Link :href="route('jobs.create')">
                    <PremiumButton>
                        Post New Job
                    </PremiumButton>
                </Link>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="job in jobs.data" :key="job.id" class="premium-card group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getStatusBadge(job.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ job.status }}
                                </span>
                                <span v-if="job.deadline" class="text-xs text-gray-500 dark:text-gray-400">
                                    Deadline: {{ new Date(job.deadline).toLocaleDateString() }}
                                </span>
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ job.title }}
                            </h3>
                            
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ job.location?.name || 'N/A' }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ job.job_category?.name || 'N/A' }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Quota: {{ job.quota }}
                                </div>
                            </div>

                            <div class="flex justify-end space-x-2">
                                <Link :href="route('jobs.edit', job.id)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </Link>
                            </div>
                        </div>

                        <div v-if="jobs.data.length === 0" class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                            No jobs posted yet. Click "Post New Job" to get started.
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="jobs.data.length > 0" class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing {{ jobs.from }} to {{ jobs.to }} of {{ jobs.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <Link v-for="link in jobs.links" :key="link.label" 
                                  :href="link.url" 
                                  v-html="link.label"
                                  :class="[
                                      'px-3 py-1 rounded-md text-sm',
                                      link.active ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600',
                                      !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                  ]"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
