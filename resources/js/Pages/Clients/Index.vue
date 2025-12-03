<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { debounce } from 'lodash-es';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    clients: Object, // for pagination
    filters: Object,
});

const search = ref(props.filters?.search || '');

watch(search, debounce((value) => {
    router.get(route('clients.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    company_name: '',
    industry: '',
    address: '',
    website: '',
    pic_name: '',
    pic_phone: '',
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.reset();
    form.name = item.name;
    form.email = item.email;
    form.company_name = item.company_name;
    form.industry = item.industry;
    form.address = item.address;
    form.website = item.website;
    form.pic_name = item.pic_name;
    form.pic_phone = item.pic_phone;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('clients.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('clients.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this client? This will also delete their associated user account.')) {
        useForm({}).delete(route('clients.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Clients" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 md:gap-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Clients</h2>
                    <p class="text-gray-600 dark:text-gray-400">List of all registered clients.</p>
                </div>
                <div class="flex flex-col md:flex-row items-stretch md:items-center space-y-3 md:space-y-0 md:space-x-4 w-full md:w-auto">
                    <div class="relative">
                        <input 
                            v-model="search"
                            type="text" 
                            placeholder="Search clients..." 
                            class="w-full md:w-64 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        >
                        <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <PremiumButton @click="openCreateModal" class="inline-flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add New
                    </PremiumButton>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Mobile Card View -->
                    <div class="md:hidden space-y-4">
                        <div v-for="client in clients.data" :key="client.id" class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ client.company_name || 'N/A' }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ client.industry || 'N/A' }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button @click="openEditModal(client)" class="p-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="deleteItem(client)" class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center text-gray-600 dark:text-gray-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    <span>{{ client.pic_name || 'N/A' }}</span>
                                </div>
                                <div class="flex items-center text-gray-600 dark:text-gray-300">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v9a2 2 0 002 2z"></path></svg>
                                    <span>{{ client.email }}</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="clients.data.length === 0" class="text-center text-gray-500 dark:text-gray-400 py-4">
                            No clients found.
                        </div>
                    </div>

                    <!-- Desktop Table View -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 hidden md:table">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Company Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact Person</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Industry</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="client in clients.data" :key="client.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ client.company_name || 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ client.pic_name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.industry || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(client)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-4">Edit</button>
                                    <Link :href="route('clients.batches.index', client.id)" class="text-green-600 dark:text-green-400 hover:text-green-900 mr-4">Batches</Link>
                                    <button @click="deleteItem(client)" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                            <tr v-if="clients.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No clients found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination v-if="clients.links" :links="clients.links" :from="clients.from" :to="clients.to" :total="clients.total" />
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingItem ? 'Edit Client' : 'Create Client' }}
                </h2>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- User Details -->
                    <div class="md:col-span-2 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2">User Account</div>
                    <div>
                        <InputLabel for="name" value="User Name" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="email" value="User Email" />
                        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" :required="!editingItem" />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                     <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password" class="mt-1 block w-full" :required="!editingItem" />
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>

                    <!-- Client Profile Details -->
                    <div class="md:col-span-2 font-semibold text-gray-700 dark:text-gray-300 border-b border-gray-200 dark:border-gray-700 pb-2 mt-4">Company Profile</div>
                    <div>
                        <InputLabel for="company_name" value="Company Name" />
                        <TextInput id="company_name" v-model="form.company_name" type="text" class="mt-1 block w-full" required />
                        <InputError :message="form.errors.company_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="industry" value="Industry" />
                        <TextInput id="industry" v-model="form.industry" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.industry" class="mt-2" />
                    </div>
                    <div class="md:col-span-2">
                        <InputLabel for="address" value="Address" />
                        <textarea id="address" v-model="form.address" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm"></textarea>
                        <InputError :message="form.errors.address" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="website" value="Website URL" />
                        <TextInput id="website" v-model="form.website" type="url" class="mt-1 block w-full" />
                        <InputError :message="form.errors.website" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="pic_name" value="PIC Name" />
                        <TextInput id="pic_name" v-model="form.pic_name" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.pic_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="pic_phone" value="PIC Phone" />
                        <TextInput id="pic_phone" v-model="form.pic_phone" type="text" class="mt-1 block w-full" />
                        <InputError :message="form.errors.pic_phone" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PremiumButton class="ml-3" @click="submit" :disabled="form.processing">
                        {{ editingItem ? 'Update Client' : 'Create Client' }}
                    </PremiumButton>
                </div>
            </div>
        </Modal>
    </div>
</template>
