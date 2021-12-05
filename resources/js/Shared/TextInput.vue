<style lang="scss" scoped>
.error-explanation {
  background-color: #fff5f5;
  border-color: #fc8181;
  color: #c53030;
}

.error {
  &:focus {
    box-shadow: 0 0 0 1px #fff9f8;
  }
}

.optional-badge {
  border-radius: 4px;
  color: #283e59;
  background-color: #edf2f9;
  padding: 3px 4px;
}

.length {
  top: 10px;
  right: 10px;
  background-color: #e5eeff;
  padding: 3px 4px;
}

.counter {
  padding-right: 64px;
}
</style>

<template>
  <label v-if="label" class="db fw4 lh-copy f6" :for="id">
    {{ label }}
    <span v-if="!required" class="optional-badge f7">
      {{ $t('app.optional') }}
    </span>
  </label>
  <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
    :value="modelValue"
    @input="$emit('update:modelValue', $event.target.value)"
    ref="input"
    :id="id"
  >
  <p v-if="help" class="text-sm mb-3">
    {{ help }}
  </p>
</template>

<script>
export default {
  props: ['modelValue'],

  emits: ['update:modelValue'],

  props: {
    id: {
      type: String,
      default: 'text-input-',
    },
    type: {
      type: String,
      default: 'text',
    },
    name: {
      type: String,
      default: 'input',
    },
    placeholder: {
      type: String,
      default: '',
    },
    help: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    focus() {
      this.$refs.input.focus()
    }
  }
}
</script>
