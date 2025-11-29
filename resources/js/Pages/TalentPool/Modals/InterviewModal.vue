<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { watch, computed } from 'vue';

const props = defineProps({
    show: Boolean,
    application: Object, // Changed from applicationId to full object
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    scheduled_at: '',
    type: 'ONLINE',
    meeting_link: '',
    address: '',
    notes: '',
});

// Watch for application changes to pre-fill data (Reschedule feature)
watch(() => props.application, (newApp) => {
    if (newApp) {
        form.scheduled_at = newApp.interview_date || '';
        // Determine type based on location content if not explicitly stored, 
        // but ideally we should store type. For now, infer:
        // If interview_location looks like URL -> ONLINE, else OFFLINE
        if (newApp.interview_location && newApp.interview_location.startsWith('http')) {
            form.type = 'ONLINE';
            form.meeting_link = newApp.interview_location;
            form.address = '';
        } else if (newApp.interview_address) {
            form.type = 'OFFLINE';
            form.address = newApp.interview_address;
            form.meeting_link = '';
        } else {
             // Default
             form.type = 'ONLINE';
        }
        
        form.notes = newApp.interview_notes || '';
    }
}, { immediate: true });

const submit = () => {
    form.post(route('interviews.store', props.application.id), {
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
                <div class="bg-indigo-100 dark:bg-indigo-900/30 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                {{ form.scheduled_at ? 'Reschedule Interview' : 'Schedule Interview' }}
            </h2>
            <button @click="close" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="space-y-5">
                <!-- Date & Type Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <InputLabel for="scheduled_at" value="Date & Time" class="mb-1.5" />
                        <TextInput
                            id="scheduled_at"
                            type="datetime-local"
                            class="mt-1 block w-full"
                            v-model="form.scheduled_at"
                        />
                        <InputError :message="form.errors.scheduled_at" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="type" value="Interview Type" class="mb-1.5" />
                        <select
                            id="type"
                            v-model="form.type"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="ONLINE">Online Meeting</option>
                            <option value="OFFLINE">Offline / On-site</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>
                </div>

                <!-- Dynamic Location Input -->
                <div v-if="form.type === 'ONLINE'" class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-800/30">
                    <InputLabel for="meeting_link" value="Meeting Link" class="mb-1.5 text-blue-800 dark:text-blue-300" />
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">🔗</span>
                        </div>
                        <TextInput
                            id="meeting_link"
                            type="url"
                            class="pl-10 block w-full border-blue-200 focus:border-blue-500 focus:ring-blue-500"
                            v-model="form.meeting_link"
                            placeholder="https://zoom.us/j/..."
                            :required="form.type === 'ONLINE'"
                        />
                    </div>
                    <InputError :message="form.errors.meeting_link" class="mt-2" />
                </div>

                <div v-if="form.type === 'OFFLINE'" class="bg-gray-50 dark:bg-gray-700/30 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <InputLabel for="address" value="Interview Location / Address" class="mb-1.5" />
                    <div class="relative rounded-md shadow-sm">
                         <div class="absolute top-3 left-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">📍</span>
                        </div>
                        <textarea
                            id="address"
                            v-model="form.address"
                            class="pl-10 mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="2"
                            placeholder="e.g. Meeting Room A, 2nd Floor, Main Office..."
                            :required="form.type === 'OFFLINE'"
                        ></textarea>
                    </div>
                    <InputError :message="form.errors.address" class="mt-2" />
                </div>

                <!-- Notes -->
                <div>
                    <InputLabel for="notes" value="Notes (Optional)" class="mb-1.5" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="3"
                        placeholder="Add any specific instructions for the candidate..."
                    ></textarea>
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
                    {{ form.scheduled_at ? 'Update Schedule' : 'Schedule Interview' }}
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
