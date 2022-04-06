<style lang="scss" scoped>
.optional-badge {
  border-radius: 4px;
  color: #283e59;
  background-color: #edf2f9;
  padding: 1px 3px;
}

.icon-search {
  left: 8px;
  top: 13px;
}

.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}
</style>

<template>
  <div :class="divOuterClass">

    <!-- input -->
    <div>
      <label v-if="label" class="mb-2 block text-sm" :for="id">
        {{ label }}
        <span v-if="!required" class="optional-badge text-xs"> optional </span>
      </label>

      <div class="relative">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon-search absolute h-4 w-4 text-gray-400"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>

        <input
          :ref="ref"
          :class="localInputClasses"
          :value="modelValue"
          :type="type"
          :name="name"
          :required="required"
          :placeholder="placeholder"
          @keydown.esc="sendEscKey"
          @focus="showMaxLength"
          @blur="displayMaxLength = false" />
        <span v-if="maxlength && displayMaxLength" class="length absolute rounded text-xs">
          {{ charactersLeft }}
        </span>
      </div>

      <p v-if="help" class="mb-3 mt-1 text-xs">
        {{ help }}
      </p>
    </div>

    <!-- blank state -->
    <div v-if="localContacts.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">Add your first contact</p>
    </div>

    <!-- search results -->
    <div v-if="searchResults.length == 0">
      <ul class="mb-4 rounded-lg border border-gray-200 bg-white">
        <li
          v-for="contact in searchResults"
          :key="contact.id"
          class="item-list border-b border-gray-200 hover:bg-slate-50 flex items-center justify-between">
            <inertia-link :href="contact.url">
              {{ contact.name }}
            </inertia-link>

            <!-- actions -->
            <ul class="text-sm">
              <li
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                @click="showEditReminderModal(reminder)">
                Add
              </li>
            </ul>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    Errors,
  },

  props: {
    inputClass: {
      type: String,
      default: '',
    },
    divOuterClass: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: 'Type the first letters of the name',
    },
    help: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: '',
    },
    type: {
      type: String,
      default: 'text',
    },
    required: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      localInputClasses: '',
      localContacts: [],
      searchResults: [],
      form: {
        name: '',
        errors: [],
      },
    };
  },

  created() {
    this.localInputClasses =
      'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm disabled:bg-slate-50 pl-8 w-full' +
      this.inputClass;
  },

  methods: {
    sendEscKey() {
      this.$emit('esc-key-pressed');
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.address_type_store, this.form)
        .then((response) => {
          this.flash('The address type has been created', 'success');
          this.localAddressTypes.unshift(response.data.data);
          this.loadingState = null;
          this.createAddressTypeModalShown = false;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },
  },
};
</script>
