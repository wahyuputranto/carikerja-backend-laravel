<script setup>
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import PremiumButton from '@/Components/PremiumButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    job: Object,
    categories: Array,
    locations: Array,
    clients: Array,
});

const page = usePage();
const isSuperAdmin = page.props.auth.role === 'superadmin';

const form = useForm({
    title: props.job.title,
    description: props.job.description,
    requirements: props.job.requirements || '',
    job_category_id: props.job.job_category_id,
    location_id: props.job.location_id,
    client_profile_id: props.job.client_profile_id,
    salary_min: props.job.salary_min || '',
    salary_max: props.job.salary_max || '',
    quota: props.job.quota,
    deadline: props.job.deadline || '',
    status: props.job.status,
});

const submit = () => {
    form.put(route('jobs.update', props.job.id));
};

const deleteJob = () => {
    if (confirm('Are you sure you want to delete this job posting?')) {
        useForm({}).delete(route('jobs.destroy', props.job.id));
    }
};
</script>

<template>
    <Head :title="`Edit: ${job.title}`" />

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-start">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200">Edit Job Posting</h2>
                    <p class="text-gray-600 dark:text-gray-400">Update job vacancy details.</p>
                </div>
                <DangerButton @click="deleteJob">
                    Delete Job
                </DangerButton>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg premium-card">
                <form @submit.prevent="submit" class="p-6 space-y-6">
                    <!-- Client Selection (for Superadmin) -->
                    <div v-if="isSuperAdmin">
                        <InputLabel for="client_profile_id" value="Client *" />
                        <select
                            id="client_profile_id"
                            v-model="form.client_profile_id"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.company_name }} ({{ client.name }})
                            </option>
                        </select>
                        <InputError :message="form.errors.client_profile_id" class="mt-2" />
                    </div>

                    <!-- Job Title -->
                    <div>
                        <InputLabel for="title" value="Job Title *" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div>
                        <InputLabel for="description" value="Job Description *" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="5"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        ></textarea>
                        <InputError :message="form.errors.description" class="mt-2" />
                    </div>

                    <!-- Requirements -->
                    <div>
                        <InputLabel for="requirements" value="Requirements" />
                        <textarea
                            id="requirements"
                            v-model="form.requirements"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        ></textarea>
                        <InputError :message="form.errors.requirements" class="mt-2" />
                    </div>

                    <!-- Category & Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="job_category_id" value="Job Category *" />
                            <select
                                id="job_category_id"
                                v-model="form.job_category_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            >
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                            <InputError :message="form.errors.job_category_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="location_id" value="Location *" />
                            <select
                                id="location_id"
                                v-model="form.location_id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            >
                                <option v-for="location in locations" :key="location.id" :value="location.id">
                                    {{ location.name }}{{ location.parent ? ', ' + location.parent.name : '' }}
                                </option>
                            </select>
                            <InputError :message="form.errors.location_id" class="mt-2" />
                        </div>
                    </div>

                    <!-- Salary Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="salary_min" value="Minimum Salary (IDR)" />
                            <TextInput
                                id="salary_min"
                                v-model="form.salary_min"
                                type="number"
                                class="mt-1 block w-full"
                                min="0"
                            />
                            <InputError :message="form.errors.salary_min" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="salary_max" value="Maximum Salary (IDR)" />
                            <TextInput
                                id="salary_max"
                                v-model="form.salary_max"
                                type="number"
                                class="mt-1 block w-full"
                                min="0"
                            />
                            <InputError :message="form.errors.salary_max" class="mt-2" />
                        </div>
                    </div>

                    <!-- Quota & Deadline -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <InputLabel for="quota" value="Quota (Number of Positions) *" />
                            <TextInput
                                id="quota"
                                v-model="form.quota"
                                type="number"
                                class="mt-1 block w-full"
                                min="1"
                                required
                            />
                            <InputError :message="form.errors.quota" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="deadline" value="Application Deadline" />
                            <TextInput
                                id="deadline"
                                v-model="form.deadline"
                                type="date"
                                class="mt-1 block w-full"
                            />
                            <InputError :message="form.errors.deadline" class="mt-2" />
                        </div>
                    </div>

                    <!-- Status -->
                    <div>
                        <InputLabel for="status" value="Status *" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="DRAFT">Draft</option>
                            <option value="PUBLISHED">Published</option>
                            <option value="CLOSED">Closed</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <SecondaryButton @click="$inertia.visit(route('jobs.index'))">
                            Cancel
                        </SecondaryButton>
                        <PremiumButton type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Updating...' : 'Update Job' }}
                        </PremiumButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
