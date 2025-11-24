<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
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
    users: Array,
    roles: Array,
});

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    email: '',
    role_id: '',
    password: '',
    password_confirmation: '',
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    // Default to first role if available
    if (props.roles.length > 0) {
        form.role_id = props.roles[0].id;
    }
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.email = item.email;
    form.role_id = item.role_id;
    form.password = '';
    form.password_confirmation = '';
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('master-data.users.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('master-data.users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this user?')) {
        useForm({}).delete(route('master-data.users.destroy', item.id));
    }
};
</script>

<template>
    <Head title="Users Management" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Users & Roles</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage system users and assign roles.</p>
                </div>
                <PremiumButton @click="openCreateModal">
                    Add New User
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="item in users" :key="item.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" :src="'https://ui-avatars.com/api/?name='+item.name" alt="" />
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ item.name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ item.email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ item.role ? item.role.name : 'No Role' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button @click="openEditModal(item)" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 mr-4">Edit</button>
                                    <button @click="deleteItem(item)" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
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
                    {{ editingItem ? 'Edit User' : 'Create User' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Role" />
                        <select
                            id="role"
                            v-model="form.role_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.name }}
                            </option>
                        </select>
                        <InputError :message="form.errors.role_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="password" value="Password" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            :placeholder="editingItem ? 'Leave blank to keep current' : ''"
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                        />
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
