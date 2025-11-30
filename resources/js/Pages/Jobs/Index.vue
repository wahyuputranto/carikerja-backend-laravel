<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import { debounce } from 'lodash-es';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    jobs: Object,
    filters: Object,
    locations: Array,
});

const search = ref(props.filters?.search || '');
const status = ref(props.filters?.status || '');
const location_id = ref(props.filters?.location_id || '');

watch([search, status, location_id], debounce(([searchValue, statusValue, locationValue]) => {
    router.get(route('jobs.index'), { 
        search: searchValue,
        status: statusValue,
        location_id: locationValue
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

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

    <div class="py-6 sm:py-12 px-4 sm:px-0">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6">
                <div class="mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200">Job Posting</h2>
                    <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">Manage job vacancies and postings.</p>
                </div>
                
                <!-- Filters Section -->
                <div class="flex flex-col space-y-3">
                    <!-- Row 1: Dropdowns -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Status Filter -->
                        <select 
                            v-model="status" 
                            class="flex-1 sm:flex-none rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500 text-sm"
                        >
                            <option value="">All Status</option>
                            <option value="DRAFT">Draft</option>
                            <option value="PUBLISHED">Published</option>
                            <option value="CLOSED">Closed</option>
                        </select>

                        <!-- Location Filter -->
                        <select 
                            v-model="location_id" 
                            class="flex-1 sm:flex-none rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500 text-sm"
                        >
                            <option value="">All Locations</option>
                            <option v-for="loc in locations" :key="loc.id" :value="loc.id">
                                {{ loc.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Row 2: Search & Button -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Search Input -->
                        <div class="relative flex-1">
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Search jobs..." 
                                class="w-full px-4 py-2 pr-10 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500 text-sm"
                            >
                            <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        
                        <!-- Post Job Button -->
                        <Link :href="route('jobs.create')" class="flex-shrink-0">
                            <PremiumButton class="inline-flex items-center justify-center w-full sm:w-auto">
                                <svg class="h-5 w-5 sm:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span class="hidden sm:inline">Post Job</span>
                            </PremiumButton>
                        </Link>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                        <div v-for="job in jobs.data" :key="job.id" class="premium-card group">
                            <div class="flex justify-between items-start mb-3">
                                <span :class="getStatusBadge(job.status)" class="px-2 py-1 text-xs font-semibold rounded-full">
                                    {{ job.status }}
                                </span>
                                <span v-if="job.deadline" class="text-xs text-gray-500 dark:text-gray-400">
                                    Deadline: {{ new Date(job.deadline).toLocaleDateString() }}
                                </span>
                            </div>
                            
                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                {{ job.title }}
                            </h3>
                            
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                {{ job.client_profile?.company_name || 'Unknown Client' }}
                            </p>
                            
                            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="truncate">{{ job.location?.name }}, {{ job.location?.parent?.parent?.name }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ job.jobCategory?.name || 'N/A' }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div v-if="jobs.data.length > 0" class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 text-center sm:text-left">
                            Showing {{ jobs.from }} to {{ jobs.to }} of {{ jobs.total }} results
                        </div>
                        <div class="flex flex-wrap justify-center gap-2">
                            <template v-for="(link, key) in jobs.links" :key="key">
                                <Link v-if="link.url" 
                                      :href="link.url" 
                                      v-html="link.label"
                                      :class="[
                                          'px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm',
                                          link.active ? 'bg-primary-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'
                                      ]"
                                />
                                <span v-else
                                      v-html="link.label"
                                      class="px-2 sm:px-3 py-1 rounded-md text-xs sm:text-sm bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600 cursor-not-allowed opacity-50 border border-gray-200 dark:border-gray-700"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
