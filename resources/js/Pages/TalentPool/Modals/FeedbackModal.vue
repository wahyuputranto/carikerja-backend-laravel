<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    application: Object,
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    feedback: '',
    status: 'INTERVIEW', // Default keep current status
});

watch(() => props.application, (newApp) => {
    if (newApp) {
        form.feedback = newApp.interview_feedback || '';
        form.status = newApp.status || 'INTERVIEW';
    }
}, { immediate: true });

const submit = () => {
    form.post(route('interviews.feedback', props.application.id), {
        onSuccess: () => {
            form.reset();
            emit('submitted');
            emit('close');
        },
    });
};

const close = () => {
    form.reset();
    emit('close');
};
</script>

<template>
    <Modal :show="show" @close="close">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                <div class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                Interview Feedback
            </h2>
            <button @click="close" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="space-y-4">
                <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800/30 rounded-lg p-4 mb-4">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        Please provide detailed feedback about the candidate's performance. You can also update the application status directly.
                    </p>
                </div>

                <div>
                    <InputLabel for="feedback" value="Feedback & Notes" class="mb-1.5" />
                    <textarea
                        id="feedback"
                        v-model="form.feedback"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="6"
                        placeholder="Enter interview results, candidate strengths/weaknesses, and recommendation..."
                        required
                    ></textarea>
                    <InputError :message="form.errors.feedback" class="mt-2" />
                </div>

                <div class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Status</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ application?.status }}
                        </span>
                    </div>
                    
                    <InputLabel for="status" value="Update Status To (Next Step)" class="mb-1.5" />
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm font-medium"
                        :class="{
                            'text-green-600': form.status === 'OFFERING',
                            'text-red-600': form.status === 'REJECTED',
                            'text-blue-600': form.status === 'INTERVIEW'
                        }"
                    >
                        <option value="INTERVIEW" class="text-blue-600">Still in Interview (No Change)</option>
                        <option value="OFFERING" class="text-green-600 font-bold">Pass Interview → Proceed to Offering</option>
                        <option value="REJECTED" class="text-red-600 font-bold">Fail Interview → Reject Application</option>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Selecting "Proceed to Offering" will advance the candidate pipeline.
                    </p>
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100 dark:border-gray-700">
                <SecondaryButton @click="close"> Cancel </SecondaryButton>
                <PrimaryButton
                    class="px-6"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Save Feedback & Update Status
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
