<script setup>
import Layout from '@/Shared/Layout.vue';
import PrettyLink from '@/Shared/Form/PrettyLink.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import TextArea from '@/Shared/Form/TextArea.vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  data: Object,
  layoutData: Object,
});

const form = useForm({});

const destroy = () => {
  if (
    confirm(
      'Are you sure? This will delete the module permanently, and delete the data from all the contacts associated with this module.',
    )
  ) {
    form.delete(props.data.url.destroy, {
      onFinish: () => {},
    });
  }
};
</script>

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
              <inertia-link :href="data.url.settings" class="text-blue-500 hover:underline">
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
            <li class="mr-2 inline">
              <inertia-link :href="data.url.personalize" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_settings_personalize') }}
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
            <li class="mr-2 inline">
              <inertia-link :href="data.url.modules" class="text-blue-500 hover:underline">{{
                $t('app.breadcrumb_settings_modules')
              }}</inertia-link>
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
            <li class="mr-2 inline">{{ data.name }}</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-24">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-6 rounded border border-gray-200">
          <div class="border-b border-gray-200 p-3">
            <h3 class="mb-2 text-xl font-bold">
              {{ data.name }}
            </h3>
            <p class="text-sm text-gray-500">Module created on {{ data.created_at }}</p>
          </div>
          <ul class="bg-gray-50 p-3 text-sm">
            <li class="ml-4 inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy()">
              {{ $t('app.delete') }}
            </li>
          </ul>
        </div>

        <!-- preview -->
        <div class="rounded border border-gray-200 bg-gray-100 dark:border-gray-700">
          <!-- example -->
          <p class="border-b border-gray-200 p-3 text-center">👇 This is what the module will look like 👇</p>

          <!-- loop on module rows -->
          <div
            v-for="module_row in data.module_rows"
            :key="module_row.id"
            class="grid auto-cols-fr grid-flow-col border-b border-gray-200 bg-white last:mb-0 dark:bg-gray-900">
            <!-- loop on module row fields -->
            <div
              v-for="field in module_row.module_row_fields"
              :key="field.id"
              class="border-r border-gray-200 p-3 last:border-r-0 dark:border-gray-700">
              <!-- case of an input text -->
              <text-input
                v-if="field.module_field_type == 'input'"
                :type="'text'"
                :autofocus="true"
                :label="field.label"
                :help="field.help"
                :placeholder="field.placeholder"
                :input-class="'block w-full'"
                :required="field.required"
                :autocomplete="false"
                :maxlength="255" />

              <!-- case of a text area -->
              <text-area
                v-if="field.module_field_type == 'textarea'"
                v-model="field.description"
                :label="field.label"
                :help="field.help"
                :required="field.required"
                :placeholder="field.placeholder"
                :maxlength="255"
                :textarea-class="'block w-full'" />

              <!-- dropdown -->
              <label v-if="field.module_field_type == 'dropdown'" class="mb-2 block text-sm">{{ field.label }}</label>
              <select
                v-if="field.module_field_type == 'dropdown'"
                class="mr-1 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 sm:text-sm"
                :required="required">
                <option v-for="choice in field.choices" :key="choice.id">
                  {{ choice.label }}
                </option>
              </select>
            </div>
          </div>

          <!-- actions -->
          <div class="flex justify-between p-5">
            <pretty-link :href="data.url.back" :text="$t('app.cancel')" :classes="'mr-3'" />
            <pretty-button :text="$t('app.save')" :state="loadingState" :icon="'check'" :classes="'save'" />
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
select {
  padding-left: 8px;
  padding-right: 20px;
  background-position: right 3px center;
}
</style>
