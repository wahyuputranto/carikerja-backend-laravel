<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import InterviewModal from './Modals/InterviewModal.vue';
import DeploymentModal from './Modals/DeploymentModal.vue';
import { ref } from 'vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    candidate: Object,
});

const statusForm = useForm({
    hiring_status: props.candidate.hiring_status,
});

const updateStatus = () => {
    statusForm.patch(route('talent-pool.update-status', props.candidate.id));
};

const getStatusColor = (status) => {
    const colors = {
        AVAILABLE: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        READY_TO_HIRE: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        HIRED: 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        NOT_AVAILABLE: 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    };
    return colors[status] || colors.AVAILABLE;
};

// Application Status Logic
const showInterviewModal = ref(false);
const showDeploymentModal = ref(false);
const selectedApplicationId = ref(null);
const deploymentStage = ref('');

const applicationStatuses = [
    'APPLIED', 'INTERVIEW', 'OFFERING', 'PROCESSING_VISA', 'DEPLOYED', 'REJECTED'
];

const handleApplicationStatusChange = (application, event) => {
    const newStatus = event.target.value;
    selectedApplicationId.value = application.id;

    if (newStatus === 'INTERVIEW') {
        showInterviewModal.value = true;
    } else if (newStatus === 'OFFERING') {
        deploymentStage.value = 'CONTRACT';
        showDeploymentModal.value = true;
    } else if (newStatus === 'PROCESSING_VISA') {
        deploymentStage.value = 'VISA'; // Default to Visa for now, can be expanded
        showDeploymentModal.value = true;
    } else if (newStatus === 'DEPLOYED') {
        deploymentStage.value = 'FLIGHT';
        showDeploymentModal.value = true;
    } else {
        // Direct update for APPLIED, REJECTED (if no reason needed), etc.
        router.patch(route('applications.update-status', application.id), {
            status: newStatus
        });
    }
};

const onModalClose = () => {
    showInterviewModal.value = false;
    showDeploymentModal.value = false;
    selectedApplicationId.value = null;
    // Refresh page to show updated status
    router.reload();
};
</script>

<template>
    <Head :title="candidate.name" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <Link :href="route('talent-pool.index')" class="text-primary-600 dark:text-primary-400 hover:text-primary-900 text-sm font-medium">
                    ← Back to Talent Pool
                </Link>
            </div>

            <!-- Header Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card mb-6">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-6">
                            <div class="flex-shrink-0">
                                <div class="h-24 w-24 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-4xl font-bold">
                                    {{ candidate.name.charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ candidate.name }}
                                </h1>
                                <div class="space-y-2 text-gray-600 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ candidate.email }}
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ candidate.phone }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span :class="getStatusColor(candidate.hiring_status)" class="px-3 py-1 text-sm font-semibold rounded-full">
                                {{ candidate.hiring_status.replace('_', ' ') }}
                            </span>
                        </div>
                    </div>

                    <!-- Bio -->
                    <div v-if="candidate.profile?.about_me" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">About</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ candidate.profile.about_me }}</p>
                    </div>

                    <!-- Update Status -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Update Hiring Status</h3>
                        <div class="flex items-center space-x-4">
                            <select
                                v-model="statusForm.hiring_status"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            >
                                <option value="AVAILABLE">Available</option>
                                <option value="READY_TO_HIRE">Ready to Hire</option>
                                <option value="HIRED">Hired</option>
                                <option value="NOT_AVAILABLE">Not Available</option>
                            </select>
                            <PremiumButton @click="updateStatus" :disabled="statusForm.processing">
                                Update Status
                            </PremiumButton>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Education -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            Education
                        </h2>
                        <div v-if="candidate.educations && candidate.educations.length > 0" class="space-y-4">
                            <div v-for="edu in candidate.educations" :key="edu.id" class="border-l-4 border-primary-500 pl-4 py-2">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ edu.degree }} in {{ edu.major }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ edu.institution_name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ edu.start_year }} - {{ edu.end_year || 'Present' }}</p>
                                <p v-if="edu.gpa" class="text-sm text-gray-600 dark:text-gray-400">GPA: {{ edu.gpa }}</p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No education records available.</p>
                    </div>
                </div>

                <!-- Experience -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Work Experience
                        </h2>
                        <div v-if="candidate.experiences && candidate.experiences.length > 0" class="space-y-4">
                            <div v-for="exp in candidate.experiences" :key="exp.id" class="border-l-4 border-primary-500 pl-4 py-2">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ exp.position }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ exp.company_name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">
                                    {{ new Date(exp.start_date).toLocaleDateString() }} - 
                                    {{ exp.is_current ? 'Present' : new Date(exp.end_date).toLocaleDateString() }}
                                </p>
                                <p v-if="exp.description" class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ exp.description }}</p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No work experience records available.</p>
                    </div>
                </div>
            </div>

            <!-- Applications History -->
            <div v-if="candidate.applications && candidate.applications.length > 0" class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">Application History</h2>
                    <div class="space-y-3">
                        <div v-for="app in candidate.applications" :key="app.id" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ app.job?.title || 'N/A' }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Applied: {{ new Date(app.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    Current: {{ app.status }}
                                </span>
                                <!-- Status Dropdown -->
                                <select 
                                    :value="app.status"
                                    @change="handleApplicationStatusChange(app, $event)"
                                    class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="status in applicationStatuses" :key="status" :value="status">
                                        {{ status }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <InterviewModal 
            :show="showInterviewModal" 
            :application-id="selectedApplicationId"
            @close="onModalClose"
            @submitted="onModalClose"
        />

        <DeploymentModal
            :show="showDeploymentModal"
            :application-id="selectedApplicationId"
            :stage="deploymentStage"
            @close="onModalClose"
            @submitted="onModalClose"
        />
    </div>
</template>
