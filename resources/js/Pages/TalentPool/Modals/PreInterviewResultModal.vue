<script setup>
import { useForm } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { watch, ref } from 'vue';

const props = defineProps({
    show: Boolean,
    application: Object,
    candidate: Object, // Need candidate data to pre-fill skills
});

const emit = defineEmits(['close', 'submitted']);

const form = useForm({
    languages: [],
    computer_skills: [],
    notes: '',
    result: 'PASSED',
});

watch(() => props.show, (newVal) => {
    if (newVal && props.candidate) {
        // Pre-fill languages
        if (props.candidate.languages && props.candidate.languages.length > 0) {
            form.languages = props.candidate.languages.map(l => ({
                language: l.language,
                speaking: l.speaking,
                reading: l.reading,
                writing: l.writing
            }));
        } else {
            form.languages = [{ language: '', speaking: '', reading: '', writing: '' }];
        }

        // Pre-fill computer skills
        if (props.candidate.computer_skills && props.candidate.computer_skills.length > 0) {
            form.computer_skills = props.candidate.computer_skills.map(s => ({
                skill_name: s.skill_name,
                proficiency_level: s.proficiency_level
            }));
        } else {
            form.computer_skills = [{ skill_name: '', proficiency_level: 'Fair' }];
        }
        form.notes = '';
        form.result = 'PASSED';
    }
});

const addLanguage = () => {
    form.languages.push({ language: '', speaking: '', reading: '', writing: '' });
};

const removeLanguage = (index) => {
    form.languages.splice(index, 1);
};

const addComputerSkill = () => {
    form.computer_skills.push({ skill_name: '', proficiency_level: 'Fair' });
};

const removeComputerSkill = (index) => {
    form.computer_skills.splice(index, 1);
};

const submit = () => {
    let url;
    if (props.application && props.application.id) {
        url = route('interviews.pre-interview-result', props.application.id);
    } else if (props.candidate && props.candidate.id) {
        url = route('interviews.pre-interview-result-candidate', props.candidate.id);
    }

    form.transform((data) => ({
        ...data,
        candidate_id: props.candidate?.id
    })).post(url, {
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
    <Modal :show="show" @close="close" maxWidth="2xl">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center">
                <div class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                Pre-Interview Result
            </h2>
            <button @click="close" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- Languages Section -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <InputLabel value="Languages" />
                    <button @click="addLanguage" type="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Language
                    </button>
                </div>
                <div v-for="(lang, index) in form.languages" :key="index" class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg mb-2 relative group">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div class="md:col-span-4">
                            <TextInput v-model="lang.language" placeholder="Language Name (e.g. English)" class="w-full text-sm" />
                        </div>
                        <div>
                            <select v-model="lang.speaking" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="" disabled>Speaking</option>
                                <option value="Fair">Fair</option>
                                <option value="Good">Good</option>
                                <option value="Fluent">Fluent</option>
                            </select>
                        </div>
                        <div>
                            <select v-model="lang.reading" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="" disabled>Reading</option>
                                <option value="Fair">Fair</option>
                                <option value="Good">Good</option>
                                <option value="Fluent">Fluent</option>
                            </select>
                        </div>
                        <div>
                            <select v-model="lang.writing" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="" disabled>Writing</option>
                                <option value="Fair">Fair</option>
                                <option value="Good">Good</option>
                                <option value="Fluent">Fluent</option>
                            </select>
                        </div>
                        <div class="flex items-center justify-end">
                             <button @click="removeLanguage(index)" type="button" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Computer Skills -->
            <div>
                <div class="flex justify-between items-center mb-2">
                    <InputLabel value="Computer Skills" />
                    <button @click="addComputerSkill" type="button" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Add Skill
                    </button>
                </div>
                <div v-for="(skill, index) in form.computer_skills" :key="index" class="bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg mb-2 relative group">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="md:col-span-2">
                            <TextInput v-model="skill.skill_name" placeholder="Skill Name (e.g. MS Excel)" class="w-full text-sm" />
                        </div>
                        <div>
                            <select v-model="skill.proficiency_level" class="w-full text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="Fair">Fair</option>
                                <option value="Good">Good</option>
                                <option value="Expert">Expert</option>
                            </select>
                        </div>
                        <div class="md:col-span-3 flex items-center justify-end">
                             <button @click="removeComputerSkill(index)" type="button" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes & Result -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <InputLabel for="notes" value="Interviewer Notes" />
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="3"
                        placeholder="General impression and notes..."
                    ></textarea>
                </div>

                <div>
                    <InputLabel for="result" value="Result" />
                    <select
                        id="result"
                        v-model="form.result"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >
                        <option value="PASSED">PASSED (Proceed to Document Verification)</option>
                        <option value="FAILED">FAILED (Reject Candidate)</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-5 border-t border-gray-100 dark:border-gray-700">
                <SecondaryButton @click="close"> Cancel </SecondaryButton>
                <PrimaryButton
                    class="px-6"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Submit Result
                </PrimaryButton>
            </div>
        </div>
    </Modal>
</template>
