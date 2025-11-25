<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { debounce } from 'lodash-es';

defineOptions({ layout: AppLayout });

const props = defineProps({
    candidates: Object,
    filters: Object,
    filterOptions: Object,
});

// Search and filters
// Search and filters
const search = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || '');
const selectedLocation = ref(props.filters?.location || '');

// Watch for filter changes
watch([search, selectedStatus, selectedLocation], debounce(([searchVal, status, location]) => {
    router.get(route('talent-pool.index'), {
        search: searchVal,
        status: status,
        location: location,
    }, {
        preserveState: true,
        replace: true,
    });
}, 300));

// Review actions
const showRejectModal = ref(false);
const showReviseModal = ref(false);
const selectedCandidate = ref(null);
const selectedApplication = ref(null);

const rejectForm = useForm({
    application_id: null,
    rejection_reason: '',
});

const reviseForm = useForm({
    application_id: null,
    revision_notes: '',
});

const openRejectModal = (candidate, application) => {
    selectedCandidate.value = candidate;
    selectedApplication.value = application;
    rejectForm.application_id = application.id;
    rejectForm.rejection_reason = '';
    showRejectModal.value = true;
};

const openReviseModal = (candidate, application) => {
    selectedCandidate.value = candidate;
    selectedApplication.value = application;
    reviseForm.application_id = application.id;
    reviseForm.revision_notes = '';
    showReviseModal.value = true;
};

const approveCandidate = (candidate, application) => {
    if (confirm('Are you sure you want to approve this candidate for interview?')) {
        router.post(route('talent-pool.approve', candidate.id), {
            application_id: application.id,
        });
    }
};

const submitReject = () => {
    rejectForm.post(route('talent-pool.reject', selectedCandidate.value.id), {
        onSuccess: () => {
            showRejectModal.value = false;
            rejectForm.reset();
        },
    });
};

const submitRevise = () => {
    reviseForm.post(route('talent-pool.revise', selectedCandidate.value.id), {
        onSuccess: () => {
            showReviseModal.value = false;
            reviseForm.reset();
        },
    });
};

const clearFilters = () => {
    search.value = '';
    selectedStatus.value = '';
    selectedLocation.value = '';
};

const getStatusBadgeClass = (status) => {
    const classes = {
        'AVAILABLE': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'READY_TO_HIRE': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'HIRED': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
        'NOT_AVAILABLE': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getApplicationStatusBadgeClass = (status) => {
    const classes = {
        'PENDING': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'INTERVIEW': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'REJECTED': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        'REVISION_REQUESTED': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Talent Pool" />
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Talent Pool</h2>
                <p class="text-gray-600 dark:text-gray-400">Review and manage candidate applications</p>
            </div>

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <div class="relative">
                            <input 
                                v-model="search"
                                type="text" 
                                placeholder="Name, email, phone..." 
                                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                            >
                            <svg class="absolute right-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select 
                            v-model="selectedStatus"
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        >
                            <option value="">All Statuses</option>
                            <option v-for="status in filterOptions.statuses" :key="status.value" :value="status.value">
                                {{ status.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
                        <input 
                            v-model="selectedLocation"
                            type="text"
                            placeholder="City name..."
                            class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-primary-500 focus:border-primary-500"
                        >
                    </div>
                </div>

                <!-- Clear Filters -->
                <div class="mt-4 flex justify-end">
                    <button 
                        @click="clearFilters"
                        class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium"
                    >
                        Clear Filters
                    </button>
                </div>
            </div>

            <!-- Candidates Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Candidate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Applications</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="candidate in candidates.data" :key="candidate.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <!-- Candidate Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" :src="`https://ui-avatars.com/api/?name=${candidate.name}`" alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ candidate.name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ candidate.profile?.city || 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">{{ candidate.email }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ candidate.phone }}</div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusBadgeClass(candidate.hiring_status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{ candidate.hiring_status.replace('_', ' ') }}
                                    </span>
                                </td>

                                <!-- Applications -->
                                <td class="px-6 py-4">
                                    <div v-if="candidate.applications && candidate.applications.length > 0" class="space-y-2">
                                        <div v-for="app in candidate.applications" :key="app.id" class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ app.job?.title || 'N/A' }}</div>
                                                <span :class="getApplicationStatusBadgeClass(app.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full mt-1">
                                                    {{ app.status.replace('_', ' ') }}
                                                </span>
                                            </div>
                                            <!-- Review Actions for Pending Applications -->
                                            <div v-if="app.status === 'PENDING'" class="flex space-x-2 ml-4">
                                                <button 
                                                    @click="approveCandidate(candidate, app)"
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                    title="Approve"
                                                >
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="openReviseModal(candidate, app)"
                                                    class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300"
                                                    title="Request Revision"
                                                >
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                                <button 
                                                    @click="openRejectModal(candidate, app)"
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                    title="Reject"
                                                >
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="text-sm text-gray-500 dark:text-gray-400">No applications</div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <Link 
                                        :href="route('talent-pool.show', candidate.id)"
                                        class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300"
                                    >
                                        View Details
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="candidates.data.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    No candidates found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <Pagination :links="candidates.links" :from="candidates.from" :to="candidates.to" :total="candidates.total" />
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <Modal :show="showRejectModal" @close="showRejectModal = false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Reject Application
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Are you sure you want to reject this application? You can optionally provide a reason.
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Rejection Reason (Optional)
                </label>
                <textarea
                    v-model="rejectForm.rejection_reason"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Provide feedback to the candidate..."
                ></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <SecondaryButton @click="showRejectModal = false">Cancel</SecondaryButton>
                <button
                    @click="submitReject"
                    :disabled="rejectForm.processing"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
                >
                    {{ rejectForm.processing ? 'Rejecting...' : 'Reject Application' }}
                </button>
            </div>
        </div>
    </Modal>

    <!-- Revise Modal -->
    <Modal :show="showReviseModal" @close="showReviseModal = false">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                Request Revision
            </h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Provide specific feedback on what needs to be revised in the application.
            </p>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Revision Notes <span class="text-red-500">*</span>
                </label>
                <textarea
                    v-model="reviseForm.revision_notes"
                    rows="4"
                    required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-primary-500 focus:border-primary-500"
                    placeholder="Please update your resume to include..."
                ></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <SecondaryButton @click="showReviseModal = false">Cancel</SecondaryButton>
                <PremiumButton
                    @click="submitRevise"
                    :disabled="reviseForm.processing || !reviseForm.revision_notes"
                >
                    {{ reviseForm.processing ? 'Sending...' : 'Send Revision Request' }}
                </PremiumButton>
            </div>
        </div>
    </Modal>
</template>