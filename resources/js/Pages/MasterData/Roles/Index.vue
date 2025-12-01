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
import Checkbox from '@/Components/Checkbox.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    roles: Array,
    availablePermissions: Object,
});

const showModal = ref(false);
const editingItem = ref(null);
const form = useForm({
    name: '',
    permissions: [],
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.permissions = [];
    showModal.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.name = item.name;
    form.permissions = item.privileges.map(p => p.permission);
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
};

const submit = () => {
    if (editingItem.value) {
        form.put(route('master-data.roles.update', editingItem.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('master-data.roles.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteItem = (item) => {
    if (confirm('Are you sure you want to delete this role?')) {
        useForm({}).delete(route('master-data.roles.destroy', item.id));
    }
};

const togglePermission = (permission) => {
    if (form.permissions.includes(permission)) {
        form.permissions = form.permissions.filter(p => p !== permission);
    } else {
        form.permissions.push(permission);
    }
};
</script>

<template>
    <Head title="Roles Management" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 md:gap-0">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Roles & Permissions</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manage user roles and their access privileges.</p>
                </div>
                <PremiumButton @click="openCreateModal" class="w-full md:w-auto flex justify-center">
                    Add New Role
                </PremiumButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="role in roles" :key="role.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ role.name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">{{ role.slug }}</p>
                                </div>
                                <div class="flex space-x-2" v-if="role.slug !== 'superadmin'">
                                    <button @click="openEditModal(role)" class="p-1 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button @click="deleteItem(role)" class="p-1 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                                <div v-else class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 text-xs font-bold rounded-full">
                                    System
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</p>
                                <div class="flex flex-wrap gap-1">
                                    <span v-if="role.slug === 'superadmin'" class="px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">
                                        All Permissions
                                    </span>
                                    <template v-else>
                                        <span v-for="privilege in role.privileges.slice(0, 5)" :key="privilege.id" class="px-2 py-1 text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                            {{ privilege.permission }}
                                        </span>
                                        <span v-if="role.privileges.length > 5" class="px-2 py-1 text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full">
                                            +{{ role.privileges.length - 5 }} more
                                        </span>
                                        <span v-if="role.privileges.length === 0" class="text-xs text-gray-400 italic">
                                            No permissions assigned
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <Modal :show="showModal" @close="closeModal" maxWidth="4xl">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ editingItem ? 'Edit Role' : 'Create Role' }}
                </h2>

                <div class="mt-6 space-y-6">
                    <div>
                        <InputLabel for="name" value="Role Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. HR Manager"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel value="Permissions" class="mb-3" />
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="(perms, group) in availablePermissions" :key="group" class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">{{ group }}</h4>
                                <div class="space-y-2">
                                    <label v-for="permission in perms" :key="permission" class="flex items-start cursor-pointer">
                                        <Checkbox 
                                            :checked="form.permissions.includes(permission)" 
                                            @update:checked="togglePermission(permission)"
                                            class="mt-0.5"
                                        />
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400 break-all">{{ permission }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
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
