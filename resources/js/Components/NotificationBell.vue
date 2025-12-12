<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

const notifications = ref([]);
const unreadCount = ref(0);
const showDropdown = ref(false);
const loading = ref(false);

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/admin-notifications');
        if (response.data.success) {
            notifications.value = response.data.data;
            unreadCount.value = response.data.unread_count;
        }
    } catch (error) {
        console.error('Failed to fetch notifications:', error);
    }
};

const markAsRead = async (notification) => {
    if (!notification.is_read) {
        try {
            await axios.post(`/admin-notifications/${notification.id}/read`);
            notification.is_read = true;
            unreadCount.value = Math.max(0, unreadCount.value - 1);
        } catch (error) {
            console.error('Failed to mark as read:', error);
        }
    }
    
    // Navigate to candidate profile
    if (notification.url) {
        router.visit(notification.url);
        showDropdown.value = false;
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post('/admin-notifications/read-all');
        notifications.value.forEach(n => n.is_read = true);
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all as read:', error);
    }
};

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
    if (showDropdown.value) {
        fetchNotifications();
    }
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000); // seconds

    if (diff < 60) return 'Baru saja';
    if (diff < 3600) return `${Math.floor(diff / 60)} menit lalu`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} jam lalu`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} hari lalu`;
    
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

// Poll every 30 seconds
let pollInterval;
onMounted(() => {
    fetchNotifications();
    pollInterval = setInterval(fetchNotifications, 30000);
});

onUnmounted(() => {
    clearInterval(pollInterval);
});

// Close dropdown when clicking outside
const closeDropdown = (e) => {
    if (!e.target.closest('.notification-bell-container')) {
        showDropdown.value = false;
    }
};

window.addEventListener('click', closeDropdown);
onUnmounted(() => window.removeEventListener('click', closeDropdown));
</script>

<template>
    <div class="relative notification-bell-container">
        <button 
            @click.stop="toggleDropdown" 
            class="relative p-2 text-gray-600 hover:text-primary-600 transition-colors rounded-full hover:bg-gray-100"
            title="Notifications"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span 
                v-if="unreadCount > 0" 
                class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full min-w-[1.25rem]"
            >
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </span>
        </button>

        <div 
            v-if="showDropdown" 
            class="fixed inset-x-4 top-16 mt-2 w-auto max-h-[calc(100vh-6rem)] overflow-y-auto bg-white rounded-xl shadow-lg border border-gray-100 z-50 sm:absolute sm:inset-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-96 sm:max-h-[32rem]"
        >
            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50 sticky top-0 z-10">
                <h3 class="text-sm font-semibold text-gray-700">Notifikasi</h3>
                <button 
                    v-if="unreadCount > 0"
                    @click="markAllAsRead" 
                    class="text-xs text-primary-600 hover:text-primary-800 font-medium"
                >
                    Tandai semua dibaca
                </button>
            </div>

            <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                Tidak ada notifikasi
            </div>

            <div v-else>
                <div 
                    v-for="notification in notifications" 
                    :key="notification.id"
                    @click="markAsRead(notification)"
                    class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-0 transition-colors"
                    :class="{ 'bg-blue-50/50': !notification.is_read }"
                >
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 mt-1">
                            <div 
                                class="w-2 h-2 rounded-full" 
                                :class="notification.is_read ? 'bg-transparent' : 'bg-blue-500'"
                            ></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <p class="text-sm font-medium text-gray-900 line-clamp-1" :class="{ 'font-semibold': !notification.is_read }">
                                    {{ notification.title }}
                                </p>
                            </div>
                            <p class="text-sm text-gray-600 mt-0.5 line-clamp-2">
                                {{ notification.message }}
                            </p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-xs text-gray-400">
                                    {{ formatDate(notification.created_at) }}
                                </p>
                                <p v-if="notification.candidate" class="text-xs text-primary-600 font-medium">
                                    {{ notification.candidate.full_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.notification-bell-container {
    display: inline-block;
}
</style>
