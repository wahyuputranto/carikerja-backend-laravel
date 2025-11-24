<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineOptions({
    layout: AppLayout,
});

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    Profile Settings
                </h2>
                <p class="text-gray-600 dark:text-gray-400">Manage your account information and security settings.</p>
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 premium-card">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-xl"
                />
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 premium-card">
                <UpdatePasswordForm class="max-w-xl" />
            </div>

            <div class="bg-white dark:bg-gray-800 p-4 shadow sm:rounded-lg sm:p-8 premium-card">
                <DeleteUserForm class="max-w-xl" />
            </div>
        </div>
    </div>
</template>
