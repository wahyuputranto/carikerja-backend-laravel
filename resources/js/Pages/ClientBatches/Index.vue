<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Checkbox from '@/Components/Checkbox.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    client: Object,
    batches: Array,
});

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
};

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    batch_name: '',
    total_quota: '',
    start_date: '',
    end_date: '',
    is_active: true,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.reset();
    form.batch_name = item.batch_name;
    form.total_quota = item.total_quota;
    // Format date for input field (YYYY-MM-DD) which is required for type="date"
    form.start_date = item.start_date ? new Date(item.start_date).toISOString().split('T')[0] : '';
    form.end_date = item.end_date ? new Date(item.end_date).toISOString().split('T')[0] : '';
    form.is_active = Boolean(item.is_active);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('clients.batches.update', [props.client.id, editingItem.value.id]), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('clients.batches.store', props.client.id), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this batch?')) {
        useForm({}).delete(route('clients.batches.destroy', [props.client.id, item.id]));
    }
};
</script>

<template>
    <Head title="Client Batches" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 md:gap-0">
                <div>
                    <div class="flex items-center gap-2">
                         <Link :href="route('clients.index')" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        </Link>
                        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Batches for {{ client.company_name }}</h2>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Manage quota batches for this client.</p>
                </div>
                <PremiumButton @click="openCreateModal">
                    Add New Batch
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quota (Used/Total)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="batch in batches" :key="batch.id">
                                <td class="px-6 py-4 whitespace-nowrap">{{ batch.batch_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="{'text-red-500': batch.used_quota >= batch.total_quota, 'text-green-500': batch.used_quota < batch.total_quota}">
                                        {{ batch.used_quota }}
                                    </span>
                                    / {{ batch.total_quota }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ formatDate(batch.start_date) }} - {{ formatDate(batch.end_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span v-if="batch.is_active" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(batch)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                    <button @click="deleteItem(batch)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="batches.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No batches found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingItem ? 'Edit Batch' : 'Create Batch' }}
                </h2>

                <div class="mt-6 grid grid-cols-1 gap-6">
                    <div>
                        <InputLabel for="batch_name" value="Batch Name" />
                        <TextInput id="batch_name" v-model="form.batch_name" type="text" class="mt-1 block w-full" required placeholder="e.g. Q1 2025" />
                        <InputError :message="form.errors.batch_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="total_quota" value="Total Quota" />
                        <TextInput id="total_quota" v-model="form.total_quota" type="number" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.total_quota" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput id="start_date" v-model="form.start_date" type="date" class="mt-1 block w-full" />
                            <InputError :message="form.errors.start_date" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput id="end_date" v-model="form.end_date" type="date" class="mt-1 block w-full" />
                            <InputError :message="form.errors.end_date" class="mt-2" />
                        </div>
                    </div>
                    <div class="block">
                        <label class="flex items-center">
                            <Checkbox name="is_active" v-model:checked="form.is_active" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Set as Active Batch</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Note: Activating this batch will deactivate other batches for this client.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PremiumButton class="ml-3" @click="submit" :disabled="form.processing">
                        {{ editingItem ? 'Update' : 'Create' }}
                    </PremiumButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
