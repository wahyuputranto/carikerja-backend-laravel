<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    candidates: Object,
    filters: Object,
});

const search = ref(props.filters?.search || '');

const performSearch = () => {
    router.get(route('talent-pool.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Talent Pool" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Talent Pool</h2>
                <p class="text-gray-600 dark:text-gray-400">Browse candidates ready to hire.</p>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="flex gap-4">
                    <TextInput
                        v-model="search"
                        type="text"
                        placeholder="Search by name, email, or phone..."
                        class="flex-1"
                        @keyup.enter="performSearch"
                    />
                    <button @click="performSearch" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        Search
                    </button>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="space-y-4">
                        <div v-for="candidate in candidates.data" :key="candidate.id" class="premium-card group">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <div class="flex-shrink-0">
                                        <div class="h-16 w-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-2xl font-bold">
                                            {{ candidate.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                            {{ candidate.name }}
                                        </h3>
                                        <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                </svg>
                                                {{ candidate.email }}
                                            </div>
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                </svg>
                                                {{ candidate.phone }}
                                            </div>
                                            <div v-if="candidate.profile" class="mt-2">
                                                <p class="text-gray-700 dark:text-gray-300">
                                                    {{ candidate.profile.bio || 'No bio available' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end space-y-2">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Ready to Hire
                                    </span>
                                    <Link :href="route('talent-pool.show', candidate.id)" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 text-sm font-medium">
                                        View Profile →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <div v-if="candidates.data.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400">
                            No candidates found in the talent pool.
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="candidates.data.length > 0" class="mt-6 flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            Showing {{ candidates.from }} to {{ candidates.to }} of {{ candidates.total }} results
                        </div>
                        <div class="flex space-x-2">
                            <Link v-for="link in candidates.links" :key="link.label" 
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
