<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Pagination from '@/Components/Pagination.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { throttle } from 'lodash';

const props = defineProps({
    logs: Object,
    filters: Object,
});

const search = ref(props.filters.search || '');
const showingDetails = ref(false);
const selectedLog = ref(null);

watch(search, throttle((value) => {
    router.get(route('audit-trails.index'), { search: value }, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, 300));

const showDetails = (log) => {
    selectedLog.value = log;
    showingDetails.value = true;
};

const closeDetails = () => {
    showingDetails.value = false;
    selectedLog.value = null;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Audit Trail" />

    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Audit Trail
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex flex-col sm:flex-row justify-between mb-6 gap-4">
                            <div class="w-full sm:w-1/3">
                                <TextInput
                                    v-model="search"
                                    type="text"
                                    placeholder="Search entries..."
                                    class="w-full"
                                />
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Action
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Module
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="log in logs.data" :key="log.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ log.user ? log.user.name : 'System/Unknown' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ log.ip_address }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                :class="{
                                                    'bg-green-100 text-green-800': log.action === 'created',
                                                    'bg-blue-100 text-blue-800': log.action === 'updated',
                                                    'bg-red-100 text-red-800': log.action === 'deleted'
                                                }">
                                                {{ log.action.toUpperCase() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ log.module }} <span v-if="log.reference_id">#{{ log.reference_id }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(log.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="showDetails(log)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="logs.data.length === 0">
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                            No logs found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            <Pagination :links="logs.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Modal -->
        <Modal :show="showingDetails" @close="closeDetails">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Activity Details
                </h2>

                <div v-if="selectedLog" class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block font-bold text-gray-700 dark:text-gray-300">Action:</span>
                            <p class="text-gray-600 dark:text-gray-400">{{ selectedLog.action.toUpperCase() }}</p>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-700 dark:text-gray-300">Module:</span>
                            <p class="text-gray-600 dark:text-gray-400">{{ selectedLog.module }} #{{ selectedLog.reference_id }}</p>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-700 dark:text-gray-300">User:</span>
                            <p class="text-gray-600 dark:text-gray-400">{{ selectedLog.user ? selectedLog.user.name : 'Unknown' }}</p>
                        </div>
                        <div>
                            <span class="block font-bold text-gray-700 dark:text-gray-300">Date:</span>
                            <p class="text-gray-600 dark:text-gray-400">{{ formatDate(selectedLog.created_at) }}</p>
                        </div>
                    </div>

                    <div v-if="selectedLog.old_values || selectedLog.new_values" class="mt-4">
                         <div v-if="selectedLog.old_values" class="mb-4">
                            <h3 class="font-semibold text-red-600 mb-2">Old Values (Before)</h3>
                            <pre class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-auto max-h-48">{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
                        </div>
                        
                        <div v-if="selectedLog.new_values">
                             <h3 class="font-semibold text-green-600 mb-2">New Values (After)</h3>
                             <pre class="bg-gray-100 dark:bg-gray-900 p-3 rounded text-xs overflow-auto max-h-48">{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
                        </div>
                    </div>
                    <div v-else class="text-gray-500 italic">
                        No detailed changes recorded.
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeDetails">
                        Close
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>
