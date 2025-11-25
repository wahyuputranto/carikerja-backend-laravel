<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: Boolean,
    applicationId: String,
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    scheduled_at: '',
    type: 'ONLINE',
    meeting_link: '',
    notes: '',
});

const submit = () => {
    form.post(route('interviews.store', props.applicationId), {
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
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Schedule Interview
            </h2>

            <div class="mt-6 space-y-4">
                <div>
                    <InputLabel for="scheduled_at" value="Date & Time" />
                    <TextInput
                        id="scheduled_at"
                        type="datetime-local"
                        class="mt-1 block w-full"
                        v-model="form.scheduled_at"
                        required
                    />
                    <InputError :message="form.errors.scheduled_at" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="type" value="Interview Type" />
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

                <div v-if="form.type === 'ONLINE'">
                    <InputLabel for="meeting_link" value="Meeting Link" />
                    <TextInput
                        id="meeting_link"
                        type="url"
                        class="mt-1 block w-full"
                        v-model="form.meeting_link"
                        placeholder="https://zoom.us/..."
                    />
                    <InputError :message="form.errors.meeting_link" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="notes" value="Notes (Optional)" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="3"
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="close"> Cancel </SecondaryButton>
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Schedule Interview
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
