<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3'; // updated import for inertia-vue3
import { ref, watch } from 'vue';

const props = defineProps({
    notifications: Object,
    filters: Object,
});

const filters = ref({
    status: props.filters?.status || 'all',
});

watch(filters, (value) => {
    router.get(route('admin-notifications.inbox'), value, {
        preserveState: true,
        replace: true,
    });
}, { deep: true });

const markAsRead = (id) => {
    router.post(route('admin-notifications.mark-read', id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Optimistic update or handled by Inertia reload
        }
    });
};

const markAllAsRead = () => {
    if (confirm('Are you sure you want to mark all as read?')) {
        router.post(route('admin-notifications.mark-all-read'), {}, {
            preserveScroll: true,
        });
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
    }).format(date);
};
</script>

<template>
    <Head title="Inbox Notifikasi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Inbox Notifikasi
                </h2>
                
                <div class="flex items-center gap-4">
                    <!-- Filters -->
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button 
                            type="button" 
                            class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-s-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                            :class="filters.status === 'all' ? 'bg-gray-100 text-blue-700' : 'bg-white text-gray-900'"
                            @click="filters.status = 'all'"
                        >
                            Semua
                        </button>
                        <button 
                            type="button" 
                            class="px-4 py-2 text-sm font-medium border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                            :class="filters.status === 'unread' ? 'bg-gray-100 text-blue-700' : 'bg-white text-gray-900'"
                            @click="filters.status = 'unread'"
                        >
                            Belum Dibaca
                        </button>
                        <button 
                            type="button" 
                            class="px-4 py-2 text-sm font-medium border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white"
                            :class="filters.status === 'read' ? 'bg-gray-100 text-blue-700' : 'bg-white text-gray-900'"
                            @click="filters.status = 'read'"
                        >
                            Sudah Dibaca
                        </button>
                    </div>

                    <div v-if="notifications.data.length > 0">
                        <button
                            @click="markAllAsRead"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                        >
                            Tandai Semua Dibaca
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="notifications.data.length === 0" class="p-6 text-center text-gray-500">
                        Tidak ada notifikasi.
                    </div>
                    <ul v-else role="list" class="divide-y divide-gray-100">
                        <li v-for="notification in notifications.data" :key="notification.id" 
                            class="flex gap-x-4 py-5 px-6 hover:bg-gray-50 transition duration-150"
                            :class="{ 'bg-blue-50': !notification.is_read }"
                        >
                            <div class="flex-auto">
                                <div class="flex items-baseline justify-between gap-x-4">
                                    <p class="text-sm font-semibold leading-6 text-gray-900">
                                        {{ notification.title }}
                                        <span v-if="!notification.is_read" class="ml-2 inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">Baru</span>
                                    </p>
                                    <p class="flex-none text-xs text-gray-500">
                                        {{ formatDate(notification.created_at) }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm leading-6 text-gray-600">{{ notification.message }}</p>
                                
                                <div class="mt-2 flex items-center gap-x-4">
                                    <a 
                                        v-if="notification.url" 
                                        :href="notification.url" 
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-500"
                                        @click="!notification.is_read && markAsRead(notification.id)"
                                    >
                                        Lihat Detail &rarr;
                                    </a>
                                    <button 
                                        v-if="!notification.is_read"
                                        @click="markAsRead(notification.id)"
                                        class="text-xs text-gray-500 hover:text-gray-700"
                                    >
                                        Tandai Dibaca
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                    
                    <!-- Pagination -->
                    <div v-if="notifications.links.length > 3" class="px-6 py-4 border-t border-gray-100">
                        <div class="flex flex-wrap gap-1">
                            <Link
                                v-for="(link, key) in notifications.links"
                                :key="key"
                                :href="link.url"
                                v-html="link.label"
                                class="px-4 py-2 border rounded-md text-sm"
                                :class="{ 'bg-indigo-600 text-white': link.active, 'bg-white text-gray-700 hover:bg-gray-50': !link.active, 'opacity-50 cursor-not-allowed': !link.url }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
