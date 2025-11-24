<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps({
    documentTypes: Array,
});

const showModal = ref(false);
const editing = ref(false);
const form = useForm({
    id: null,
    name: '',
    is_mandatory: false,
    chunkable: false,
    allowed_mimetypes: [],
});

const openModal = (documentType = null) => {
    editing.value = !!documentType;
    if (documentType) {
        form.id = documentType.id;
        form.name = documentType.name;
        form.is_mandatory = documentType.is_mandatory;
        form.chunkable = documentType.chunkable;
        form.allowed_mimetypes = documentType.allowed_mimetypes || [];
    } else {
        form.reset();
        form.id = null;
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    editing.value = false;
};

const submit = () => {
    if (editing.value) {
        form.put(route('admin.document-types.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('admin.document-types.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteDocumentType = (id) => {
    if (confirm('Are you sure you want to delete this document type?')) {
        useForm({}).delete(route('admin.document-types.destroy', id));
    }
};
</script>

<template>
    <Head title="Document Types" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Document Types
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <PrimaryButton @click="openModal()">
                                Add Document Type
                            </PrimaryButton>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandatory</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chunkable</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="type in documentTypes" :key="type.id">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ type.name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="type.is_mandatory" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">No</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span v-if="type.chunkable" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Yes</span>
                                        <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">No</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button @click="openModal(type)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                        <button @click="deleteDocumentType(type.id)" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showModal" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    {{ editing ? 'Edit Document Type' : 'Add Document Type' }}
                </h2>

                <div class="mt-6">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Document Name"
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <div class="mt-6 block">
                    <label class="flex items-center">
                        <Checkbox name="is_mandatory" v-model:checked="form.is_mandatory" />
                        <span class="ms-2 text-sm text-gray-600">Is Mandatory?</span>
                    </label>
                </div>

                <div class="mt-4 block">
                    <label class="flex items-center">
                        <Checkbox name="chunkable" v-model:checked="form.chunkable" />
                        <span class="ms-2 text-sm text-gray-600">Chunkable (Large Files)?</span>
                    </label>
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <PrimaryButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                        {{ editing ? 'Update' : 'Save' }}
                    </PrimaryButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
