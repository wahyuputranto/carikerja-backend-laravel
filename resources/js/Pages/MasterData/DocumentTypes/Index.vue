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

const availableMimeTypes = [
    { label: 'PDF', value: 'application/pdf' },
    { label: 'Image (JPEG)', value: 'image/jpeg' },
    { label: 'Image (PNG)', value: 'image/png' },
    { label: 'Video (MP4)', value: 'video/mp4' },
];

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    is_mandatory: false,
    chunkable: false,
    allowed_mimetypes: [],
    max_size: 1000,
    notes: '',
    template: null,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.allowed_mimetypes = [];
    form.max_size = 1000;
    form.notes = '';
    form.template = null;
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.is_mandatory = !!item.is_mandatory;
    form.chunkable = !!item.chunkable;
    form.allowed_mimetypes = Array.isArray(item.allowed_mimetypes) ? item.allowed_mimetypes : [];
    form.max_size = item.max_size || 1000;
    form.notes = item.notes || '';
    form.template = null;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(route('master-data.document-types.update', editingItem.value.id), {
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
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 md:gap-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Document Types</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage required documents for candidates.</p>
                </div>
                <PremiumButton @click="openCreateModal" class="w-full md:w-auto flex justify-center">
                    Add New
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        <div v-for="item in documentTypes" :key="item.id" class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ item.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ item.slug }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="openEditModal(item)" class="p-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="deleteItem(item)" class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span v-if="item.is_mandatory" class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    Mandatory
                                </span>
                                <span v-else class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Optional
                                </span>
                                <span v-if="item.chunkable" class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Chunkable
                                </span>
                            </div>
                        </div>
                        <div v-if="documentTypes.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No document types found.
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 hidden md:table">
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

                    <div>
                        <InputLabel value="Allowed File Types" class="mb-2" />
                        <div class="grid grid-cols-2 gap-2">
                            <label v-for="type in availableMimeTypes" :key="type.value" class="flex items-center">
                                <Checkbox :checked="form.allowed_mimetypes.includes(type.value)" @update:checked="(checked) => {
                                    if (checked) {
                                        form.allowed_mimetypes.push(type.value);
                                        // Set default max size based on type if added
                                        if (type.value === 'video/mp4') form.max_size = 12000;
                                        else if (form.max_size === 12000 && !form.allowed_mimetypes.includes('video/mp4')) form.max_size = 1000;
                                        else if (!form.max_size) form.max_size = 1000;
                                    } else {
                                        form.allowed_mimetypes = form.allowed_mimetypes.filter(t => t !== type.value);
                                        // Revert max size if video removed and it was high
                                        if (type.value === 'video/mp4' && form.max_size === 12000) form.max_size = 1000;
                                    }
                                }" />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ type.label }}</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <InputLabel for="max_size" value="Max Size (KB)" />
                        <TextInput
                            id="max_size"
                            v-model="form.max_size"
                            type="number"
                            class="mt-1 block w-full"
                            placeholder="1000"
                        />
                        <InputError :message="form.errors.max_size" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="notes" value="Notes (Optional)" />
                        <textarea
                            id="notes"
                            v-model="form.notes"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="3"
                            placeholder="Instructions for the candidate..."
                        ></textarea>
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="template" value="Template File (Optional)" />
                        <div v-if="editingItem && editingItem.template_url" class="mb-2">
                            <a :href="editingItem.template_url" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                                View Current Template
                            </a>
                        </div>
                        <input
                            type="file"
                            id="template"
                            @input="form.template = $event.target.files[0]"
                            class="mt-1 block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            "
                        />
                        <InputError :message="form.errors.template" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">Allowed: PDF, Images, MP4. Max 10MB.</p>
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
