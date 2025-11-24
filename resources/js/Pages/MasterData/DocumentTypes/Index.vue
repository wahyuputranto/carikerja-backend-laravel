<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    documentTypes: Array,
});

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    is_mandatory: false,
    chunkable: false,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.is_mandatory = !!item.is_mandatory;
    form.chunkable = !!item.chunkable;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('master-data.document-types.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('master-data.document-types.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this document type?')) {
        useForm({}).delete(route('master-data.document-types.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Document Types" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Document Types</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage required documents for candidates.</p>
                </div>
                <PremiumButton @click="openCreateModal">
                    Add New Type
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Slug</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Mandatory</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Chunkable</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in documentTypes" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.slug }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="item.is_mandatory" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Yes
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span v-if="item.chunkable" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        Yes
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        No
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(item)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-4">Edit</button>
                                    <button @click="deleteItem(item)" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="documentTypes.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No document types found.
                                </td>
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
                    {{ editingItem ? 'Edit Document Type' : 'Create Document Type' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. CV / Resume"
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.is_mandatory" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Is Mandatory?</span>
                        </label>

                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.chunkable" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Chunkable (Large Files)</span>
                        </label>
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
