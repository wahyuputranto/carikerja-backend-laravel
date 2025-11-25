<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash-es';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    locations: Object,
    parentLocations: Array,
    filters: Object,
});

const search = ref(props.filters?.search || '');
const typeFilter = ref(props.filters?.type || '');

watch([search, typeFilter], debounce(([searchValue, typeValue]) => {
    router.get(route('master-data.locations.index'), { 
        search: searchValue,
        type: typeValue 
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    type: 'CITY',
    parent_id: null,
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.type = item.type;
    form.parent_id = item.parent_id;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('master-data.locations.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('master-data.locations.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this location?')) {
        useForm({}).delete(route('master-data.locations.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Locations" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Locations</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage countries, provinces, and cities.</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select
                            v-model="typeFilter"
                            class="w-40 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">All Types</option>
                            <option value="COUNTRY">Country</option>
                            <option value="PROVINCE">Province</option>
                            <option value="CITY">City</option>
                        </select>
                    </div>
                    <div class="relative">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search locations..." 
                            class="w-64 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        >
                        <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <PremiumButton @click="openCreateModal" class="inline-flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New
                    </PremiumButton>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Parent</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in locations.data" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.parent?.name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(item)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-4">Edit</button>
                                    <button @click="deleteItem(item)" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="locations.data.length === 0">
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No locations found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="locations.links" :from="locations.from" :to="locations.to" :total="locations.total" />
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingItem ? 'Edit Location' : 'Create Location' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. Jakarta"
                            autofocus
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="type" value="Type" />
                        <select v-model="form.type" id="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="COUNTRY">Country</option>
                            <option value="PROVINCE">Province</option>
                            <option value="CITY">City</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="parent_id" value="Parent Location" />
                        <select v-model="form.parent_id" id="parent_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option :value="null">None</option>
                            <option v-for="parent in parentLocations" :key="parent.id" :value="parent.id">{{ parent.name }}</option>
                        </select>
                        <InputError :message="form.errors.parent_id" class="mt-2" />
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
