<script setup>
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: Boolean,
    documentId: [Number, String],
    rejectRouteName: {
        type: String,
        default: 'candidate-documents.reject'
    }
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    rejection_note: '',
});

const submit = () => {
    form.post(route(props.rejectRouteName, props.documentId), {
        onSuccess: () => {
            form.reset();
            emit('submitted');
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
                Reject Document
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Please provide a reason for rejecting this document.
            </p>

            <div class="mt-6">
                <InputLabel for="rejection_note" value="Rejection Reason" />
                <TextInput
                    id="rejection_note"
                    v-model="form.rejection_note"
                    type="text"
                    class="mt-1 block w-full"
                    placeholder="e.g. Image is blurry, Wrong document type"
                    required
                />
                <InputError :message="form.errors.rejection_note" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end">
                <SecondaryButton @click="close"> Cancel </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Reject Document
                </DangerButton>
            </div>
        </div>
    </Modal>
</template>
