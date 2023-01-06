<template>
  <layout :layout-data="layoutData">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.back" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_settings') }}
              </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">{{ $t('settings.subscription_title') }}</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-24">
      <div class="mx-auto max-w-lg px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <form
          class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
          @submit.prevent="destroy()">
          <!-- title -->
          <div class="section-head border-b border-gray-200 bg-blue-50 p-5 dark:border-gray-700 dark:bg-blue-900">
            <h1 class="mb-4 text-center text-2xl font-medium">Manage your subscription</h1>
            <p class="mb-2">Monica requires a licence key to remove the limitations.</p>
            <p class="mb-2">The current limitations are:</p>
            <ul class="mb-2 list-disc pl-6">
              <li>5 maximum contacts,</li>
              <li>10 Mb of storage.</li>
            </ul>
            <p class="mb-2">Once subscribed, your account gets:</p>
            <ul class="mb-2 list-disc pl-6">
              <li>unlimited contacts,</li>
              <li>10 Mb storage.</li>
            </ul>
            <p class="mb-4">Licence keys are obtained and managed on the customer portal.</p>
            <p class="mb-2 text-center">
              <external-link :href="data.url.customer_portal" :text="'Purchase a licence key'" />
            </p>
          </div>

          <!-- form -->
          <div class="border-b border-gray-200 p-5 dark:border-gray-700">
            <errors :errors="form.errors" />

            <text-input
              v-model="form.password"
              :label="'Paste your licence key'"
              :type="'password'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255" />
          </div>

          <div class="flex justify-between p-5">
            <pretty-link :href="data.url.back" :text="'Go back'" :classes="'mr-3'" />
            <pretty-button
              :href="'data.url.vault.create'"
              :text="$t('app.save')"
              :state="loadingState"
              :icon="'arrow'"
              :classes="'save'" />
          </div>
        </form>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue';
import PrettyLink from '@/Shared/Form/PrettyLink.vue';
import ExternalLink from '@/Shared/Form/ExternalLink.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import Errors from '@/Shared/Form/Errors.vue';

export default {
  components: {
    Layout,
    PrettyLink,
    ExternalLink,
    PrettyButton,
    TextInput,
    Errors,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      form: {
        password: '',
        errors: [],
      },
    };
  },

  methods: {
    destroy() {
      this.loadingState = 'loading';

      axios
        .put(this.data.url.destroy, this.form)
        .then((response) => {
          this.$inertia.visit(response.data.data);
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.section-head {
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
}
</style>
