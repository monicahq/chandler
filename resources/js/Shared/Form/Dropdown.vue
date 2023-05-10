<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  id: {
    type: String,
    default: 'dropdown-',
  },
  data: Object,
  dropdownClass: String,
  divOuterClass: String,
  modelValue: {
    type: [String, Number],
    default: '',
  },
  name: {
    type: String,
    default: 'input',
  },
  help: String,
  label: String,
  required: Boolean,
  disabled: Boolean,
  autocomplete: String,
  placeholder: String,
});

const emit = defineEmits(['esc-key-pressed', 'update:modelValue']);

const input = ref(null);

const localDropdownClasses = computed(() => {
  return [
    'py-2 px-3 rounded-md shadow-sm sm:text-sm',
    'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-700',
    'focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 focus:outline-none',
    props.dropdownClass,
  ];
});

const sendEscKey = () => {
  emit('esc-key-pressed');
};

const change = (event) => {
  emit('update:modelValue', event.target.value);
};

defineExpose({
  focus: () => input.value.focus(),
});
</script>

<template>
  <div :class="divOuterClass">
    <label v-if="label" class="mb-2 block text-sm" :for="id">
      {{ label }}
      <span v-if="!required" class="optional-badge dark:optional-badge text-xs"> {{ $t('optional') }} </span>
    </label>

    <div class="component relative">
      <select
        :id="id"
        :ref="'input'"
        :value="modelValue"
        :class="localDropdownClasses"
        :required="required"
        :disabled="disabled"
        :placeholder="placeholder"
        @keydown.esc="sendEscKey"
        @change="change">
        <option v-for="item in data" :key="item.id" :value="item.id">
          {{ item.name }}
        </option>
      </select>
    </div>

    <p v-if="help" class="mt-1 text-xs">
      {{ help }}
    </p>
  </div>
</template>

<style lang="scss" scoped>
.optional-badge {
  border-radius: 4px;
  color: #283e59;
  background-color: #edf2f9;
  padding: 1px 3px;
}

.dark .dark\:optional-badge {
  color: #d4d8dd;
  background-color: #2f3031;
}

.counter {
  padding-right: 64px;
}

select {
  padding-left: 8px;
  padding-right: 20px;
  background-position: right 3px center;
}
</style>
