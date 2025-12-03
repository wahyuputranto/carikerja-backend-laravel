<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import InterviewModal from './Modals/InterviewModal.vue';
import DeploymentModal from './Modals/DeploymentModal.vue';
import DocumentRejectModal from './Modals/DocumentRejectModal.vue';
import FeedbackModal from './Modals/FeedbackModal.vue';
import PreInterviewResultModal from './Modals/PreInterviewResultModal.vue'; // Import PreInterviewResultModal
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
const showFeedbackModal = ref(false);
const showPreInterviewResultModal = ref(false); // New State
const selectedApplication = ref(null);
const selectedInterview = ref(null); // New State
const deploymentStage = ref('');
const interviewStage = ref('USER_INTERVIEW'); // Default stage

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
    if (newStatus === 'INTERVIEW') {
        interviewStage.value = 'USER_INTERVIEW';
        showInterviewModal.value = true;
    } else if (newStatus === 'OFFERING') {
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
    showPreInterviewResultModal.value = false;
    selectedApplication.value = null;
    selectedInterview.value = null;
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

const getWhatsappUrl = (phone) => {
    if (!phone) return '#';
    let formatted = phone.replace(/\D/g, ''); // Remove non-digits
    if (formatted.startsWith('0')) {
        formatted = '62' + formatted.slice(1);
    }
    return `https://wa.me/${formatted}`;
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
                                        <a :href="getWhatsappUrl(candidate.phone)" target="_blank" class="flex items-center group" title="Chat on WhatsApp">
                                            <svg class="h-5 w-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                            </svg>
                                            <span class="group-hover:text-green-600 transition-colors">{{ candidate.phone }}</span>
                                        </a>
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

            <!-- Candidate Screening / Pre-Interview Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Candidate Screening (Pre-Interview)
                        </h2>
                        <button 
                            @click="selectedApplication = null; selectedInterview = null; interviewStage = 'PRE_INTERVIEW'; showInterviewModal = true"
                            class="text-sm bg-purple-50 text-purple-700 hover:bg-purple-100 px-3 py-1.5 rounded-md font-medium transition-colors"
                        >
                            Schedule Pre-Interview
                        </button>
                    </div>

                    <!-- List of Pre-Interviews -->
                    <div v-if="candidate.interviews && candidate.interviews.filter(i => i.stage === 'PRE_INTERVIEW').length > 0" class="space-y-4">
                        <div v-for="interview in candidate.interviews.filter(i => i.stage === 'PRE_INTERVIEW')" :key="interview.id" class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-gray-50 dark:bg-gray-700/30">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-gray-900 dark:text-gray-100">{{ formatDate(interview.scheduled_at) }}</span>
                                        <span :class="{
                                            'bg-green-100 text-green-800': interview.result === 'PASSED',
                                            'bg-red-100 text-red-800': interview.result === 'FAILED',
                                            'bg-yellow-100 text-yellow-800': !interview.result
                                        }" class="text-xs px-2 py-0.5 rounded-full font-semibold uppercase">
                                            {{ interview.result || 'Scheduled' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Type: {{ interview.type }} 
                                        <span v-if="interview.type === 'ONLINE' && interview.meeting_link">
                                            - <a :href="interview.meeting_link" target="_blank" class="text-indigo-600 hover:underline">Join Meeting</a>
                                        </span>
                                        <span v-if="interview.type === 'OFFLINE' && interview.location_address">
                                            - {{ interview.location_address }}
                                        </span>
                                    </p>
                                    <p v-if="interview.feedback_notes" class="text-sm text-gray-600 dark:text-gray-400 mt-2 italic">
                                        "{{ interview.feedback_notes }}"
                                    </p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <button 
                                        v-if="!interview.result"
                                        @click="selectedInterview = interview; selectedApplication = null; showInterviewModal = true"
                                        class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded shadow-sm transition-colors"
                                    >
                                        Reschedule
                                    </button>
                                    <button 
                                        v-if="!interview.result"
                                        @click="selectedApplication = null; showPreInterviewResultModal = true"
                                        class="text-xs bg-green-600 text-white hover:bg-green-700 px-3 py-1.5 rounded shadow-sm transition-colors"
                                    >
                                        Input Result
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p v-else class="text-gray-500 dark:text-gray-400 italic text-sm">No pre-interviews scheduled or recorded.</p>
                </div>
            </div>

            <!-- Personal Details & Passport -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Personal Details
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Basic Information</h3>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">NIK:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.profile?.nik || '-' }}</span>
                                
                                <span class="text-gray-500 dark:text-gray-400">Birth Place/Date:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">
                                    {{ candidate.profile?.birth_place }}, {{ formatDate(candidate.profile?.birth_date) }}
                                </span>

                                <span class="text-gray-500 dark:text-gray-400">Gender:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.profile?.gender === 'M' ? 'Male' : 'Female' }}</span>

                                <span class="text-gray-500 dark:text-gray-400">Marital Status:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.personal_detail?.marital_status || '-' }}</span>

                                <span class="text-gray-500 dark:text-gray-400">Religion:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.personal_detail?.religion || '-' }}</span>

                                <span class="text-gray-500 dark:text-gray-400">Citizenship:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.personal_detail?.citizenship || 'IDN' }}</span>
                            </div>
                        </div>

                        <!-- Physical & Family -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Physical & Family</h3>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Height/Weight:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">
                                    {{ candidate.personal_detail?.height ? candidate.personal_detail.height + ' cm' : '-' }} / 
                                    {{ candidate.personal_detail?.weight ? candidate.personal_detail.weight + ' kg' : '-' }}
                                </span>

                                <span class="text-gray-500 dark:text-gray-400">Father's Name:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.personal_detail?.fathers_name || '-' }}</span>

                                <span class="text-gray-500 dark:text-gray-400">Mother's Name:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.personal_detail?.mothers_name || '-' }}</span>
                                
                                <span class="text-gray-500 dark:text-gray-400">Interested Job:</span>
                                <span class="text-gray-900 dark:text-gray-200 font-medium">{{ candidate.profile?.interested_job_category?.name || '-' }}</span>
                            </div>
                        </div>

                        <!-- Emergency Contact -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">Emergency Contact</h3>
                            <div v-if="candidate.emergency_contacts && candidate.emergency_contacts.length > 0" class="text-sm space-y-2">
                                <div v-for="contact in candidate.emergency_contacts" :key="contact.id">
                                    <p class="font-medium text-gray-900 dark:text-gray-200">{{ contact.name }} ({{ contact.relation }})</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ contact.contact_number }}</p>
                                    <p class="text-gray-500 dark:text-gray-500 text-xs">{{ contact.address }}</p>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 dark:text-gray-400 italic text-sm">No emergency contacts.</p>
                        </div>
                    </div>

                    <!-- Passport Details -->
                    <div v-if="candidate.passports && candidate.passports.length > 0" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div v-for="(passport, index) in candidate.passports" :key="passport.id" class="space-y-3">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                                    Passport Information {{ candidate.passports.length > 1 ? `#${index + 1}` : '' }}
                                </h3>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Number:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">{{ passport.passport_number }}</span>
                                    
                                    <span class="text-gray-500 dark:text-gray-400">Issued By:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">{{ passport.issued_by }}</span>
                                    
                                    <span class="text-gray-500 dark:text-gray-400">Valid:</span>
                                    <span class="text-gray-900 dark:text-gray-200 font-medium">
                                        {{ new Date(passport.issue_date).toLocaleDateString('en-GB') }} - {{ new Date(passport.expiry_date).toLocaleDateString('en-GB') }}
                                    </span>

                                    <div v-if="passport.file_path" class="col-span-2 mt-1">
                                         <button @click="viewDocument({ file_path: passport.file_path })" class="text-xs text-primary-600 hover:text-primary-800 font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                            View Scanned Passport
                                         </button>
                                    </div>
                                </div>
                            </div>
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

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                <!-- Languages & Skills -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                            </svg>
                            Languages & Skills
                        </h2>
                        
                        <!-- Languages -->
                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2 text-sm uppercase tracking-wider">Languages</h3>
                            <div v-if="candidate.languages && candidate.languages.length > 0" class="space-y-2">
                                <div v-for="lang in candidate.languages" :key="lang.id" class="flex justify-between items-center bg-gray-50 dark:bg-gray-700/30 p-2 rounded">
                                    <span class="font-medium text-gray-900 dark:text-gray-200">{{ lang.language }}</span>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-right">
                                        <div>S: {{ lang.speaking }} | R: {{ lang.reading }}</div>
                                        <div>W: {{ lang.writing }}</div>
                                    </div>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 dark:text-gray-400 italic text-sm">No languages listed.</p>
                        </div>

                        <!-- Computer Skills -->
                        <div v-if="candidate.personal_detail?.computer_skills">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2 text-sm uppercase tracking-wider">Computer Skills</h3>
                            <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/30 p-3 rounded text-sm">
                                {{ candidate.personal_detail.computer_skills }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Non-Formal Education -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                    <div class="p-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <svg class="h-6 w-6 mr-2 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Non-Formal Education
                        </h2>
                        <div v-if="candidate.non_formal_educations && candidate.non_formal_educations.length > 0" class="space-y-4">
                            <div v-for="edu in candidate.non_formal_educations" :key="edu.id" class="border-l-4 border-green-500 pl-4 py-2">
                                <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ edu.name }}</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ edu.subject }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-500">{{ edu.year }} • {{ edu.country }}</p>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No non-formal education records.</p>
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
                                <p class="text-xs text-gray-400 mt-1">Last Uploaded: {{ formatDate(doc.updated_at) }}</p>
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

            <!-- Client Documents (New Section) -->
            <div v-if="candidate.applications && candidate.applications.some(app => app.documents && app.documents.length > 0)" class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                <div class="p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                        </svg>
                        Client Documents (Contracts & Offerings)
                    </h2>
                    <div class="space-y-4">
                        <div v-for="app in candidate.applications" :key="app.id">
                            <div v-if="app.documents && app.documents.length > 0">
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ app.job?.title }} - {{ app.job?.client_profile?.company_name }}
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div v-for="doc in app.documents" :key="doc.id" class="border border-purple-200 dark:border-purple-800 rounded-lg p-4 flex items-center justify-between bg-purple-50 dark:bg-purple-900/10">
                                        <div>
                                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ doc.title }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Uploaded by Client: {{ formatDate(doc.created_at) }}</p>
                                        </div>
                                        <a :href="doc.file_path" target="_blank" class="text-purple-600 hover:text-purple-800 text-sm font-medium flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                            Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                            v-if="['INTERVIEW'].includes(app.status)"
                                            @click="selectedApplication = app; interviewStage = 'USER_INTERVIEW'; showInterviewModal = true"
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
            :candidate="candidate"
            :interview="selectedInterview"
            :stage="interviewStage"
            @close="onModalClose"
            @submitted="onModalClose"
        />

        <PreInterviewResultModal
            :show="showPreInterviewResultModal"
            :application="selectedApplication"
            :candidate="candidate"
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
