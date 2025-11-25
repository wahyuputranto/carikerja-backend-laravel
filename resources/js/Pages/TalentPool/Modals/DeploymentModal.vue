<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { watch } from 'vue';

const props = defineProps({
    show: Boolean,
    applicationId: String,
    stage: String, // CONTRACT, MEDICAL, VISA, FLIGHT
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    // Contract
    contract_number: '',
    signed_at: '',
    start_date: '',
    end_date: '',
    
    // Medical
    medical_status: 'PENDING',
    check_date: '',

    // Visa/Legal
    visa_number: '',
    visa_expiry: '',
    reference_code: '',

    // Flight
    flight_airline: '',
    flight_number: '',
    departure_at: '',
    arrival_at: '',
});

// Reset form when modal opens/closes or stage changes
watch(() => props.show, (newVal) => {
    if (!newVal) form.reset();
});

const submit = () => {
    form.post(route('deployments.store', { applicationId: props.applicationId, stage: props.stage }), {
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
                Update Deployment: {{ stage }}
            </h2>

            <div class="mt-6 space-y-4">
                <!-- CONTRACT FORM -->
                <div v-if="stage === 'CONTRACT'" class="space-y-4">
                    <div>
                        <InputLabel for="contract_number" value="Contract Number" />
                        <TextInput id="contract_number" type="text" class="mt-1 block w-full" v-model="form.contract_number" required />
                        <InputError :message="form.errors.contract_number" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="signed_at" value="Signed Date" />
                        <TextInput id="signed_at" type="date" class="mt-1 block w-full" v-model="form.signed_at" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="start_date" value="Start Date" />
                            <TextInput id="start_date" type="date" class="mt-1 block w-full" v-model="form.start_date" required />
                        </div>
                        <div>
                            <InputLabel for="end_date" value="End Date" />
                            <TextInput id="end_date" type="date" class="mt-1 block w-full" v-model="form.end_date" required />
                        </div>
                    </div>
                </div>

                <!-- MEDICAL FORM -->
                <div v-if="stage === 'MEDICAL'" class="space-y-4">
                    <div>
                        <InputLabel for="medical_status" value="Medical Status" />
                        <select id="medical_status" v-model="form.medical_status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm">
                            <option value="PENDING">Pending</option>
                            <option value="FIT">Fit</option>
                            <option value="UNFIT">Unfit</option>
                        </select>
                    </div>
                    <div>
                        <InputLabel for="check_date" value="Check Date" />
                        <TextInput id="check_date" type="date" class="mt-1 block w-full" v-model="form.check_date" required />
                    </div>
                </div>

                <!-- VISA FORM -->
                <div v-if="stage === 'VISA'" class="space-y-4">
                    <div>
                        <InputLabel for="visa_number" value="Visa Number" />
                        <TextInput id="visa_number" type="text" class="mt-1 block w-full" v-model="form.visa_number" required />
                    </div>
                    <div>
                        <InputLabel for="visa_expiry" value="Visa Expiry Date" />
                        <TextInput id="visa_expiry" type="date" class="mt-1 block w-full" v-model="form.visa_expiry" required />
                    </div>
                </div>

                <!-- FLIGHT FORM -->
                <div v-if="stage === 'FLIGHT'" class="space-y-4">
                    <div>
                        <InputLabel for="flight_airline" value="Airline" />
                        <TextInput id="flight_airline" type="text" class="mt-1 block w-full" v-model="form.flight_airline" required />
                    </div>
                    <div>
                        <InputLabel for="flight_number" value="Flight Number" />
                        <TextInput id="flight_number" type="text" class="mt-1 block w-full" v-model="form.flight_number" required />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="departure_at" value="Departure Time" />
                            <TextInput id="departure_at" type="datetime-local" class="mt-1 block w-full" v-model="form.departure_at" required />
                        </div>
                        <div>
                            <InputLabel for="arrival_at" value="Arrival Time" />
                            <TextInput id="arrival_at" type="datetime-local" class="mt-1 block w-full" v-model="form.arrival_at" required />
                        </div>
                    </div>
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
                    Save & Update Status
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
