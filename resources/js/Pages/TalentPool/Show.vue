<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import InterviewModal from './Modals/InterviewModal.vue';
import DeploymentModal from './Modals/DeploymentModal.vue';
import DocumentRejectModal from './Modals/DocumentRejectModal.vue';
import FeedbackModal from './Modals/FeedbackModal.vue'; // Import FeedbackModal
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
const showFeedbackModal = ref(false); // New State
const selectedApplication = ref(null); // Changed to Object
const deploymentStage = ref('');

const getAvailableStatuses = (app) => {
    const allStatuses = [
        'APPLIED', 'REVIEW', 'INTERVIEW', 'OFFERING', 'HIRED', 
        'PROCESSING_VISA', 'DEPLOYED', 'REJECTED'
    ];
    
    // If currently in INTERVIEW but no date set, prevent moving forward to OFFERING/HIRED
    if (app.status === 'INTERVIEW' && !app.interview_date) {
        return allStatuses.filter(s => !['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(s));
    }

    // If status is before INTERVIEW (APPLIED, REVIEW), prevent jumping to OFFERING/HIRED directly without Interview
    if (['APPLIED', 'REVIEW'].includes(app.status)) {
         return allStatuses.filter(s => !['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(s));
    }

    return allStatuses;
};

const handleApplicationStatusChange = (application, event) => {
    const newStatus = event.target.value;
    selectedApplication.value = application;

    // Logic changed: Don't auto-open Interview Modal
    if (newStatus === 'OFFERING') {
        deploymentStage.value = 'CONTRACT';
        showDeploymentModal.value = true;
    } else if (newStatus === 'PROCESSING_VISA') {
        deploymentStage.value = 'VISA'; 
        showDeploymentModal.value = true;
    } else if (newStatus === 'DEPLOYED') {
        deploymentStage.value = 'FLIGHT';
        showDeploymentModal.value = true;
    } else {
        // Just update status
        router.patch(route('applications.update-status', application.id), {
            status: newStatus
        });
    }
};

// Document Logic
const showRejectModal = ref(false);
const selectedDocumentId = ref(null);

const viewDocument = async (doc) => {
    try {
        const response = await axios.post(route('talent-pool.document-url', props.candidate.id), {
            document_path: doc.file_path
        });
        window.open(response.data.url, '_blank');
    } catch (error) {
        console.error('Error viewing document:', error);
        alert('Failed to view document.');
    }
};

const approveDocument = (doc) => {
    if (confirm('Are you sure you want to approve this document?')) {
        router.post(route('candidate-documents.approve', doc.id), {}, {
            preserveScroll: true,
        });
    }
};

const openRejectModal = (doc) => {
    selectedDocumentId.value = doc.id;
    showRejectModal.value = true;
};

const onRejectModalClose = () => {
    showRejectModal.value = false;
    selectedDocumentId.value = null;
    router.reload();
};

const onModalClose = () => {
    showInterviewModal.value = false;
    showDeploymentModal.value = false;
    showFeedbackModal.value = false;
    selectedApplication.value = null;
    router.reload();
};

// Collapsible state for each application's details
const expandedInterviews = ref({});
const expandedOfferings = ref({});

const toggleInterview = (appId) => {
    expandedInterviews.value[appId] = !expandedInterviews.value[appId];
};

const toggleOffering = (appId) => {
    expandedOfferings.value[appId] = !expandedOfferings.value[appId];
};

// Helper for date formatting
const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleString('en-US', { 
        weekday: 'short', year: 'numeric', month: 'short', day: 'numeric', 
        hour: '2-digit', minute: '2-digit' 
    });
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

            <!-- Header Card (Unchanged) -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-4">
                        <div class="flex flex-col md:flex-row items-center md:items-start space-y-4 md:space-y-0 md:space-x-6 w-full">
                            <div class="flex-shrink-0">
                                <img v-if="candidate.profile?.photo_url" :src="candidate.profile.photo_url" :alt="candidate.name" class="h-24 w-24 rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-md">
                                <div v-else class="h-24 w-24 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 text-4xl font-bold border-4 border-white dark:border-gray-700 shadow-md">
                                    {{ candidate.name.charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div class="text-center md:text-left">
                                <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                    {{ candidate.name }}
                                </h1>
                                <div class="space-y-2 text-gray-600 dark:text-gray-400 flex flex-col items-center md:items-start">
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
                        <div class="w-full md:w-auto flex justify-center md:justify-end">
                            <span :class="getStatusColor(candidate.hiring_status)" class="px-3 py-1 text-sm font-semibold rounded-full whitespace-nowrap">
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
                        <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 md:space-x-4">
                            <select
                                v-model="statusForm.hiring_status"
                                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full md:w-auto"
                            >
                                <option value="AVAILABLE">Available</option>
                                <option value="READY_TO_HIRE">Ready to Hire</option>
                                <option value="HIRED">Hired</option>
                                <option value="NOT_AVAILABLE">Not Available</option>
                            </select>
                            <PremiumButton @click="updateStatus" :disabled="statusForm.processing" class="w-full md:w-auto justify-center">
                                Update Status
                            </PremiumButton>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Education (Unchanged) -->
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

                <!-- Experience (Unchanged) -->
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

            <!-- Documents (Unchanged) -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Documents
                    </h2>
                    <div v-if="candidate.documents && candidate.documents.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="doc in candidate.documents" :key="doc.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ doc.document_type?.name || 'Unknown Type' }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate max-w-[150px]" :title="doc.file_name">{{ doc.file_name }}</p>
                                <p class="text-xs mt-1">
                                    Status: 
                                    <span :class="{
                                        'text-green-600': doc.status === 'VALID',
                                        'text-red-600': doc.status === 'INVALID',
                                        'text-yellow-600': doc.status === 'PENDING' || doc.status === 'UPLOADED'
                                    }" class="font-semibold">{{ doc.status }}</span>
                                </p>
                                <p v-if="doc.rejection_note" class="text-xs text-red-500 mt-1">Note: {{ doc.rejection_note }}</p>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <button @click="viewDocument(doc)" class="text-blue-600 hover:text-blue-800 text-sm font-medium text-right">View</button>
                                <button v-if="doc.status !== 'VALID'" @click="approveDocument(doc)" class="text-green-600 hover:text-green-800 text-sm font-medium text-right">Approve</button>
                                <button v-if="doc.status !== 'INVALID'" @click="openRejectModal(doc)" class="text-red-600 hover:text-red-800 text-sm font-medium text-right">Reject</button>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 dark:text-gray-400 italic">No documents uploaded.</p>
                </div>
            </div>

            <!-- Applications History -->
            <div v-if="candidate.applications && candidate.applications.length > 0" class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-6">Application History</h2>
                    <div class="space-y-6">
                        <div v-for="app in candidate.applications" :key="app.id" class="border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Card Header: Job Info & Status -->
                            <div class="p-5 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ app.job?.title || 'Unknown Position' }}</h3>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300 mt-1">
                                        {{ app.job?.client_profile?.company_name || 'Unknown Company' }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ app.job?.location?.name }}
                                        </span>
                                        <span>•</span>
                                        <span>Applied: {{ new Date(app.created_at).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                                
                                <!-- Actions Toolbar -->
                                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-lg shadow-sm border border-gray-100 dark:border-gray-600">
                                    <div class="flex flex-col">
                                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-0.5">Current Status</span>
                                        <select 
                                            :value="app.status"
                                            @change="handleApplicationStatusChange(app, $event)"
                                            class="text-sm font-semibold border-none p-0 pr-8 focus:ring-0 bg-transparent text-gray-800 dark:text-gray-200 cursor-pointer"
                                        >
                                            <option v-for="status in getAvailableStatuses(app)" :key="status" :value="status">
                                                {{ status }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center border-l border-gray-200 dark:border-gray-600 pl-3 gap-2">
                                        <!-- Schedule Interview -->
                                        <button 
                                            v-if="app.status === 'INTERVIEW'"
                                            @click="selectedApplication = app; showInterviewModal = true"
                                            class="p-2 rounded-full hover:bg-indigo-50 text-gray-400 hover:text-indigo-600 dark:hover:bg-indigo-900/30 dark:hover:text-indigo-400 transition-all"
                                            :title="app.interview_date ? 'Reschedule Interview' : 'Schedule Interview'"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </button>

                                        <!-- Manage Offering / Contract -->
                                        <button 
                                            v-if="app.status === 'OFFERING'"
                                            @click="selectedApplication = app; deploymentStage = 'CONTRACT'; showDeploymentModal = true"
                                            class="p-2 rounded-full hover:bg-purple-50 text-gray-400 hover:text-purple-600 dark:hover:bg-purple-900/30 dark:hover:text-purple-400 transition-all"
                                            title="Manage Offering & Contract Details"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                        </button>

                                        <!-- Feedback -->
                                        <button 
                                            v-if="app.status === 'INTERVIEW' && app.interview_date"
                                            @click="selectedApplication = app; showFeedbackModal = true"
                                            class="p-2 rounded-full hover:bg-green-50 text-gray-400 hover:text-green-600 dark:hover:bg-green-900/30 dark:hover:text-green-400 transition-all"
                                            title="Give Feedback"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Interview Details Section -->
                            <div v-if="app.status === 'INTERVIEW' || app.interview_date" class="p-5 bg-white dark:bg-gray-800">
                                <div v-if="app.interview_date" class="relative">
                                    <!-- Decorative Line -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 rounded-full"
                                        :class="{
                                            'bg-green-500': ['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(app.status),
                                            'bg-red-500': app.status === 'REJECTED',
                                            'bg-yellow-500': app.status === 'INTERVIEW' && new Date(app.interview_date) < new Date(),
                                            'bg-blue-500': app.status === 'INTERVIEW' && new Date(app.interview_date) >= new Date()
                                        }"
                                    ></div>
                                    
                                    <div class="pl-4">
                                        <!-- Collapsible Header -->
                                        <button 
                                            @click="toggleInterview(app.id)" 
                                            class="w-full flex items-center justify-between mb-3 hover:opacity-80 transition-opacity"
                                        >
                                            <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                                Interview Scheduled
                                                <!-- Dynamic Badge -->
                                                <span v-if="['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(app.status)" 
                                                      class="ml-2 text-[10px] px-2 py-0.5 bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 rounded-full uppercase tracking-wide">
                                                    Passed
                                                </span>
                                                <span v-else-if="app.status === 'REJECTED'" 
                                                      class="ml-2 text-[10px] px-2 py-0.5 bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full uppercase tracking-wide">
                                                    Rejected
                                                </span>
                                                <span v-else-if="new Date(app.interview_date) < new Date()" 
                                                      class="ml-2 text-[10px] px-2 py-0.5 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 rounded-full uppercase tracking-wide">
                                                    Pending Feedback
                                                </span>
                                                <span v-else 
                                                      class="ml-2 text-[10px] px-2 py-0.5 bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 rounded-full uppercase tracking-wide">
                                                    Upcoming
                                                </span>
                                            </h4>
                                            <svg 
                                                class="w-5 h-5 text-gray-500 transition-transform"
                                                :class="{ 'rotate-180': expandedInterviews[app.id] }"
                                                fill="none" 
                                                stroke="currentColor" 
                                                viewBox="0 0 24 24"
                                            >
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                        
                                        <!-- Collapsible Content -->
                                        <div v-show="expandedInterviews[app.id]" class="space-y-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Time -->
                                            <div class="flex items-start">
                                                <div class="bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg mr-3">
                                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Date & Time</p>
                                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-0.5">{{ formatDate(app.interview_date) }}</p>
                                                </div>
                                            </div>

                                            <!-- Location -->
                                            <div class="flex items-start">
                                                <div class="bg-indigo-50 dark:bg-indigo-900/20 p-2 rounded-lg mr-3">
                                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Location</p>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-200 mt-0.5">
                                                        <a v-if="app.interview_location && app.interview_location.startsWith('http')" :href="app.interview_location" target="_blank" class="text-indigo-600 hover:underline flex items-center">
                                                            Join Online Meeting
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        </a>
                                                        <span v-else>{{ app.interview_address || app.interview_location || 'Not specified' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Notes -->
                                            <div v-if="app.interview_notes" class="md:col-span-2 bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg text-sm text-gray-600 dark:text-gray-300 italic">
                                                <span class="font-semibold not-italic text-gray-500 text-xs uppercase mr-1">Note:</span>
                                                {{ app.interview_notes }}
                                            </div>
                                            </div>
                                        </div>

                                        <!-- Feedback Display -->
                                        <div v-if="app.interview_feedback" v-show="expandedInterviews[app.id]" class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                                            <h5 class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2 flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Interviewer Feedback
                                            </h5>
                                            <div class="bg-green-50 dark:bg-green-900/10 border border-green-100 dark:border-green-800/30 rounded-lg p-4">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed">{{ app.interview_feedback }}</p>
                                            </div>
                                        </div>
                                    </div>

                                <!-- Empty State -->
                                <div v-if="!app.interview_date" class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-full shadow-sm mb-3">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Interview Not Scheduled</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 mb-3">Please set a date and time for the interview.</p>
                                    <button 
                                        @click="selectedApplication = app; showInterviewModal = true"
                                        class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-4 py-2 rounded-md transition-colors"
                                    >
                                        Schedule Now
                                    </button>
                                </div>
                            </div>

                            <!-- Offering & Contract Details Section -->
                            <div v-if="['OFFERING', 'HIRED', 'PROCESSING_VISA', 'DEPLOYED'].includes(app.status) && app.deployment" class="mt-4 p-5 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <!-- Collapsible Header -->
                                <button 
                                    @click="toggleOffering(app.id)" 
                                    class="w-full flex items-center justify-between mb-4 hover:opacity-80 transition-opacity"
                                >
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                        </svg>
                                        Offering & Contract Details
                                    </h4>
                                    <svg 
                                        class="w-5 h-5 text-gray-500 transition-transform"
                                        :class="{ 'rotate-180': expandedOfferings[app.id] }"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <!-- Collapsible Content -->
                                <div v-show="expandedOfferings[app.id]" class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm bg-purple-50 dark:bg-purple-900/10 p-4 rounded-lg border border-purple-100 dark:border-purple-800/30">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Contract Number</p>
                                        <p class="font-bold text-gray-900 dark:text-gray-200 mt-0.5">{{ app.deployment.contract_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Signed Date</p>
                                        <p class="font-medium text-gray-900 dark:text-gray-200 mt-0.5">{{ formatDate(app.deployment.signed_at) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold">Duration</p>
                                        <p class="font-medium text-gray-900 dark:text-gray-200 mt-0.5">
                                            {{ formatDate(app.deployment.start_date) }} - {{ formatDate(app.deployment.end_date) }}
                                        </p>
                                    </div>
                                    <div v-if="app.offering_letter_path">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs uppercase font-semibold mb-1">Document</p>
                                        <button 
                                            @click="viewDocument({ file_path: app.offering_letter_path })"
                                            class="flex items-center text-xs font-bold text-purple-700 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300 bg-white dark:bg-gray-800 px-3 py-1.5 rounded border border-purple-200 dark:border-purple-700 shadow-sm transition-all"
                                        >
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download Offering Letter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
        <InterviewModal 
            :show="showInterviewModal" 
            :application="selectedApplication"
            @close="onModalClose"
            @submitted="onModalClose"
        />

        <FeedbackModal 
            :show="showFeedbackModal" 
            :application="selectedApplication"
            @close="onModalClose"
            @submitted="onModalClose"
        />

        <DeploymentModal
            :show="showDeploymentModal"
            :application-id="selectedApplication?.id"
            :stage="deploymentStage"
            @close="onModalClose"
            @submitted="onModalClose"
        />

        <DocumentRejectModal
            :show="showRejectModal"
            :document-id="selectedDocumentId"
            @close="onRejectModalClose"
            @submitted="onRejectModalClose"
        />
    </div>
</template>
