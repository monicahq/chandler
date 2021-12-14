<style lang="scss" scoped>
pre {
  background-color: #1f2937;
  color: #c9ef78;
}
</style>

<template>
  <div>
    <!-- title + cta -->
    <div class="sm:flex items-center justify-between mb-3 sm:mt-0 mt-8">
      <h3 class="mb-4 sm:mb-0"><span class="mr-1">👉</span> Customize how contacts should be displayed</h3>
      <pretty-button @click="enableEditMode" :text="'Edit'" />
    </div>

    <!-- help text -->
    <div class="px-3 py-2 border mb-6 flex rounded text-sm bg-slate-50">
      <svg xmlns="http://www.w3.org/2000/svg" class="grow h-6 pr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>

      <div>
        <p>You can customize how contacts should be displayed according to your own taste/culture. Perhaps you would want to use James Bond instead of Bond James. Here, you can define it at will.</p>
      </div>
    </div>

    <!-- normal mode -->
    <div v-if="!editMode" class="bg-white border border-gray-200 rounded-lg mb-6">
      <p class="px-5 py-2 border-b border-gray-200">
        <span class="mb-2">Current way of displaying contact names:</span>
        <pre class="px-5 py-2 text-sm rounded">alsdjflask</pre>
      </p>
      <p class="px-5 py-2 text-sm bg-orange-50 font-medium"><span class="font-light">Contacts will be shown as follow:</span> {{ data.name_example }}</p>
    </div>

    <!-- edit mode -->
    <div v-if="editMode" class="bg-white border border-gray-200 rounded-lg mb-6">
      <div class="px-5 py-2 border-b border-gray-200">
        <div class="flex items-center mb-2">
          <input @click="disableNameOrder = true" id="first_name_last_name" value="%first_name% %last_name%" name="name-order" type="radio" class="focus:ring-sky-500 h-4 w-4 text-sky-500 border-gray-300">
          <label for="first_name_last_name" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            First name Last name <span class="text-gray-500 font-normal ml-4">James Bond</span>
          </label>
        </div>
        <div class="flex items-center mb-2">
          <input @click="disableNameOrder = true" id="last_name_first_name" value="%last_name% %first_name%" name="name-order" type="radio" class="focus:ring-sky-500 h-4 w-4 text-sky-500 border-gray-300">
          <label for="last_name_first_name" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            Last name First name <span class="text-gray-500 font-normal ml-4">Bond James</span>
          </label>
        </div>
        <div class="flex items-center mb-2">
          <input @click="disableNameOrder = true" id="first_name_last_name_surname" value="%first_name% %last_name% (%surname%)" name="name-order" type="radio" class="focus:ring-sky-500 h-4 w-4 text-sky-500 border-gray-300">
          <label for="first_name_last_name_surname" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            First name Last name (Surname) <span class="text-gray-500 font-normal ml-4">James Bond (007)</span>
          </label>
        </div>
        <div class="flex items-center mb-2">
          <input @click="disableNameOrder = true" id="surname" value="%surname%" name="name-order" type="radio" class="focus:ring-sky-500 h-4 w-4 text-sky-500 border-gray-300">
          <label for="surname" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            Surname <span class="text-gray-500 font-normal ml-4">007</span>
          </label>
        </div>
        <div class="flex items-center mb-2">
          <input @click="focusNameOrder" id="push-everything" name="name-order" type="radio" class="focus:ring-sky-500 h-4 w-4 text-sky-500 border-gray-300">
          <label for="push-everything" class="ml-3 block text-sm font-medium text-gray-700 cursor-pointer">
            Custom name order
          </label>
        </div>
        <div class="ml-8">
          <text-input v-model="form.groupTypeName"
            :type="'text'" :autofocus="true"
            :input-class="'block w-full'"
            :div-outer-class="'block mb-2'"
            :disabled="disableNameOrder"
            :ref="'nameOrder'"
            :autocomplete="false"
            :maxlength="255" />

            <p class="mb-4 text-sm">Please read <a href="https://www.notion.so/monicahq/Customize-your-account-8e015b7488c143abab9eb8a6e2fbca77#b3fd57def37445f4a9cf234e373c52ca" target="_blank" class="text-sky-500 hover:text-blue-900">our documentation</a> to know more about this feature, and which variables you have access to.</p>
        </div>
      </div>

      <div class="p-5 flex justify-between">
        <pretty-link @click="editMode = false" :text="'Cancel'" :classes="'mr-3'" />
        <pretty-button :text="'Save'" :state="loadingState" :icon="'check'" :classes="'save'" />
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue3';
import PrettyButton from '@/Shared/PrettyButton';
import PrettyLink from '@/Shared/PrettyLink';
import PrettySpan from '@/Shared/PrettySpan';
import TextInput from '@/Shared/TextInput';
import Errors from '@/Shared/Errors';

export default {
  components: {
    Link,
    PrettyButton,
    PrettyLink,
    PrettySpan,
    TextInput,
    Errors,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      editMode: false,
      disableNameOrder: true,
      form: {
        nameOrder: '',
        errors: [],
      },
    };
  },

  methods: {
    enableEditMode() {
      this.editMode = true;
    },

    focusNameOrder() {
      this.disableNameOrder = false;

      this.$nextTick(() => {
        this.$refs.nameOrder.focus();
      });
    },
  },
};
</script>
