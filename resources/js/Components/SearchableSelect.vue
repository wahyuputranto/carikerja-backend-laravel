<template>
    <div class="relative" v-click-outside="closeDropdown">
        <div class="relative">
            <input
                type="text"
                v-model="searchQuery"
                @focus="openDropdown"
                @input="handleSearch"
                :placeholder="placeholder"
                class="w-full px-4 py-2 pr-10 rounded-md border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                autocomplete="off"
            />
            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>

        <!-- Dropdown -->
        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto"
            >
                <!-- Grouped options -->
                <template v-for="(items, groupLabel) in filteredGroupedOptions" :key="groupLabel">
                    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-900 sticky top-0">
                        {{ groupLabel }}
                    </div>
                    <div
                        v-for="option in items"
                        :key="option.value"
                        @click="selectOption(option)"
                        class="px-4 py-2 cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-700 text-gray-900 dark:text-gray-100"
                        :class="{ 'bg-indigo-100 dark:bg-gray-600': modelValue === option.value }"
                    >
                        {{ option.label }}
                    </div>
                </template>

                <!-- No results -->
                <div
                    v-if="Object.keys(filteredGroupedOptions).length === 0"
                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center"
                >
                    No results found
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
    options: {
        type: Array,
        required: true,
        // Expected format: [{ value: 1, label: 'City, Province', group: 'Country' }]
    },
    placeholder: {
        type: String,
        default: 'Search...'
    }
});

const emit = defineEmits(['update:modelValue']);

const searchQuery = ref('');
const isOpen = ref(false);

// Get selected option label
const selectedOption = computed(() => {
    return props.options.find(opt => opt.value === props.modelValue);
});

// Update search query when selection changes from outside
watch(() => props.modelValue, (newVal) => {
    if (newVal && selectedOption.value) {
        searchQuery.value = selectedOption.value.label;
    } else {
        searchQuery.value = '';
    }
}, { immediate: true });

// Group options by group property
const groupedOptions = computed(() => {
    const groups = {};
    props.options.forEach(option => {
        const group = option.group || 'Other';
        if (!groups[group]) {
            groups[group] = [];
        }
        groups[group].push(option);
    });
    return groups;
});

// Filter options based on search query
const filteredGroupedOptions = computed(() => {
    if (!searchQuery.value.trim()) {
        return groupedOptions.value;
    }

    const query = searchQuery.value.toLowerCase();
    const filtered = {};

    Object.keys(groupedOptions.value).forEach(groupLabel => {
        const groupItems = groupedOptions.value[groupLabel].filter(option => {
            return option.label.toLowerCase().includes(query) ||
                   groupLabel.toLowerCase().includes(query);
        });

        if (groupItems.length > 0) {
            filtered[groupLabel] = groupItems;
        }
    });

    return filtered;
});

const openDropdown = () => {
    isOpen.value = true;
    searchQuery.value = ''; // Clear search to show all options
};

const closeDropdown = () => {
    isOpen.value = false;
    // Restore selected option label
    if (selectedOption.value) {
        searchQuery.value = selectedOption.value.label;
    }
};

const handleSearch = () => {
    if (!isOpen.value) {
        isOpen.value = true;
    }
};

const selectOption = (option) => {
    emit('update:modelValue', option.value);
    searchQuery.value = option.label;
    closeDropdown();
};

// Click outside directive
const vClickOutside = {
    mounted(el, binding) {
        el.clickOutsideEvent = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value();
            }
        };
        document.addEventListener('click', el.clickOutsideEvent);
    },
    unmounted(el) {
        document.removeEventListener('click', el.clickOutsideEvent);
    }
};
</script>
