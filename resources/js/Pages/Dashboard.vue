<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    stats: Object,
    recentJobs: Array,
    recentCandidates: Array,
});

const page = usePage();
const isSuperAdmin = computed(() => page.props.auth.role === 'superadmin');
const isClient = computed(() => page.props.auth.role === 'client');

const formatCurrency = (value) => {
    if (!value) return 'N/A';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'short', year: 'numeric'
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Dashboard Overview
                </h2>
                <p class="text-gray-600 dark:text-gray-400">Welcome back, {{ $page.props.auth.user.name }}!</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 mb-8 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Jobs -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800 premium-card border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:bg-blue-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Jobs</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.total_jobs }}</p>
                        </div>
                    </div>
                </div>

                <!-- Active Jobs -->
                <div class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800 premium-card border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:bg-green-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Active Jobs</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.active_jobs }}</p>
                        </div>
                    </div>
                </div>

                <!-- Total Candidates (Superadmin) or Applications (Client) -->
                <div v-if="isSuperAdmin" class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800 premium-card border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:bg-purple-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Candidates</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.total_candidates }}</p>
                        </div>
                    </div>
                </div>
                <div v-else class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800 premium-card border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-purple-500 bg-purple-100 rounded-full dark:bg-purple-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 0h2a2 2 0 002-2V6a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2h2m0 0l-4 4-4-4"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Applications</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.total_applications }}</p>
                        </div>
                    </div>
                </div>

                <!-- Ready to Hire (Superadmin) or Total Applications (Client - Duplicate but okay for layout) -->
                <div v-if="isSuperAdmin" class="p-6 bg-white rounded-lg shadow-sm dark:bg-gray-800 premium-card border-l-4 border-yellow-500">
                    <div class="flex items-center">
                        <div class="p-3 mr-4 text-yellow-500 bg-yellow-100 rounded-full dark:bg-yellow-900/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Ready to Hire</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ stats.ready_candidates }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Jobs -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Recent Jobs</h3>
                        <Link :href="route('jobs.index')" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">View All</Link>
                    </div>
                    <div class="p-6">
                        <div v-if="recentJobs.length > 0" class="space-y-4">
                            <div v-for="job in recentJobs" :key="job.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ job.title }}</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ job.location?.name }} • {{ job.job_category?.name }}</p>
                                </div>
                                <div class="text-right">
                                    <span :class="{
                                        'bg-green-100 text-green-800': job.status === 'PUBLISHED',
                                        'bg-gray-100 text-gray-800': job.status === 'DRAFT',
                                        'bg-red-100 text-red-800': job.status === 'CLOSED'
                                    }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ job.status }}
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ formatDate(job.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No recent jobs found.
                        </div>
                    </div>
                </div>

                <!-- Recent Candidates (Superadmin Only) -->
                <div v-if="isSuperAdmin" class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">New Candidates</h3>
                        <Link :href="route('talent-pool.index')" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">View All</Link>
                    </div>
                    <div class="p-6">
                        <div v-if="recentCandidates.length > 0" class="space-y-4">
                            <div v-for="candidate in recentCandidates" :key="candidate.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                                        {{ candidate.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ candidate.name }}</h4>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ candidate.email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span :class="{
                                        'bg-green-100 text-green-800': candidate.hiring_status === 'READY_TO_HIRE',
                                        'bg-blue-100 text-blue-800': candidate.hiring_status === 'AVAILABLE',
                                        'bg-gray-100 text-gray-800': candidate.hiring_status === 'NOT_AVAILABLE',
                                        'bg-purple-100 text-purple-800': candidate.hiring_status === 'HIRED'
                                    }" class="px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ candidate.hiring_status.replace(/_/g, ' ') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No recent candidates found.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
