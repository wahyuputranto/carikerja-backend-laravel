<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    clients: Object, // for pagination
});

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
    form.company_name = item.client_profile?.company_name;
    form.industry = item.client_profile?.industry;
    form.address = item.client_profile?.address;
    form.website = item.client_profile?.website;
    form.pic_name = item.client_profile?.pic_name;
    form.pic_phone = item.client_profile?.pic_phone;
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
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Clients</h2>
                    <p class="text-gray-600 dark:text-gray-400">List of all registered clients.</p>
                </div>
                <PremiumButton @click="openCreateModal">
                    Add New Client
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
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
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ client.client_profile?.company_name || 'N/A' }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ client.client_profile?.pic_name || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ client.client_profile?.industry || 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(client)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-4">Edit</button>
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
