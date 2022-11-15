<script setup>
import Layout from '@/Shared/Layout.vue';
import PrettyLink from '@/Shared/Form/PrettyLink.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { ref } from 'vue';

const props = defineProps({
  data: Object,
  layoutData: Object,
});

const form = useForm({
  search: '',
  name: '',
  rows: [],
  errors: [],
});

const loadingState = ref(false);

const addRow = () => {
  // position starts at 1
  var position = form.rows.length + 1;

  form.rows.push({
    position: position,
    fields: [
      {
        inputSelectionMode: true,
        position: 1,
      },
    ],
  });
};

const destroyRow = (row) => {
  var id = form.rows.findIndex((x) => x.id === row.id);
  form.rows.splice(id, 1);
};

const addFieldToRight = (row) => {
  var id = form.rows.findIndex((x) => x.position === row.position);
  var highestPosition = form.rows[id].fields.length;

  form.rows[id].fields.push({
    inputSelectionMode: true,
    position: highestPosition + 1,
  });
};

const addFieldToLeft = (row) => {
  var id = form.rows.findIndex((x) => x.position === row.position);
  var lowestPosition = -1;

  form.rows[id].fields.unshift({
    inputSelectionMode: true,
    position: lowestPosition,
  });
};

const destroyField = (row, field) => {
  var rowId = form.rows.findIndex((x) => x.position === row.position);

  if (form.rows[rowId].fields.length == 0) {
    this.destroyRow(row);
  } else {
    var fieldId = form.rows[rowId].fields.findIndex((x) => x.position === field.position);
    form.rows[rowId].fields.splice(fieldId, 1);
  }
};

const addInput = (row, field) => {
  var rowId = form.rows.findIndex((x) => x.position === row.position);
  var fieldId = form.rows[rowId].fields.findIndex((x) => x.position === field.position);

  form.rows[rowId].fields[fieldId] = {
    position: form.rows[rowId].fields[fieldId].position,
    type: 'input',
    name: '',
    placeholder: '',
    help: '',
    required: true,
    inputSelectionMode: false,
  };
};

const addTextarea = (row, field) => {
  var rowId = form.rows.findIndex((x) => x.position === row.position);
  var fieldId = form.rows[rowId].fields.findIndex((x) => x.position === field.position);

  form.rows[rowId].fields[fieldId] = {
    position: form.rows[rowId].fields[fieldId].position,
    type: 'textarea',
    name: '',
    placeholder: '',
    help: '',
    required: true,
    inputSelectionMode: false,
  };
};

const addDropdown = (row, field) => {
  var rowId = form.rows.findIndex((x) => x.position === row.position);
  var fieldId = form.rows[rowId].fields.findIndex((x) => x.position === field.position);

  form.rows[rowId].fields[fieldId] = {
    position: form.rows[rowId].fields[fieldId].position,
    type: 'dropdown',
    name: '',
    placeholder: '',
    help: '',
    required: true,
    inputSelectionMode: false,
    choices: [],
  };
};

const addDropdownChoice = (field) => {
  var highestPosition = field.choices.length;

  field.choices.push({
    position: highestPosition + 1,
    label: '',
  });
};

const destroyChoice = (field, choice) => {
  var choiceId = field.choices.findIndex((x) => x.position === choice.position);
  field.choices.splice(choiceId, 1);
};

const changeType = (row, field) => {
  var rowId = form.rows.findIndex((x) => x.position === row.position);
  var fieldId = form.rows[rowId].fields.findIndex((x) => x.position === field.position);

  form.rows[rowId].fields[fieldId] = {
    position: form.rows[rowId].fields[fieldId].position,
    inputSelectionMode: true,
  };
};

const submit = () => {
  loadingState.value = true;

  form.post(props.data.url.store, {
    onFinish: () => {},
  });
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
            <li class="mr-2 inline">Create a custom module</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-16">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-gray-200 dark:border-gray-700">
          <!-- title -->
          <div
            class="section-head border-b border-gray-200 bg-blue-50 p-3 dark:border-gray-700 dark:bg-blue-900 sm:p-5">
            <h1 class="mb-1 flex justify-center text-2xl font-medium">
              <span>Module details</span>

              <help :url="$page.props.help_links.vault_create" :top="'9px'" :classes="'ml-2'" />
            </h1>
            <p class="text-center text-sm">
              The module can be associated with a template, that will be displayed on a contact sheet.
            </p>
          </div>

          <!-- add module -->
          <form @submit.prevent="submit">
            <errors :errors="form.errors" />

            <!-- module details -->
            <div class="border-b border-gray-200 p-5 dark:border-gray-700">
              <text-input
                v-model="form.name"
                :type="'text'"
                :autofocus="true"
                :label="'Name of the module'"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255" />
            </div>

            <!-- content of the module -->
            <div class="border-b border-gray-200 bg-gray-100 p-5 dark:border-gray-700">
              <div v-for="row in form.rows" :key="row.realId" class="mb-2">
                <div class="rounded border border-gray-300 bg-white dark:bg-gray-900">
                  <!-- row options -->
                  <div class="flex justify-between border-b border-gray-200 px-3 py-1 text-xs dark:border-gray-700">
                    <div v-if="row.fields.length <= 2" class="flex items-center">
                      <div class="relative mr-3 inline cursor-pointer" @click="addFieldToLeft(row)">
                        <svg
                          class="mr-1 inline h-3 w-3"
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                            fill="currentColor" />
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                            fill="currentColor" />
                        </svg>
                        <span class="text-blue-500 hover:underline">Add a field to the left</span>
                      </div>

                      <div class="relative mr-2 inline cursor-pointer" @click="addFieldToRight(row)">
                        <svg
                          class="mr-1 inline h-3 w-3"
                          viewBox="0 0 24 24"
                          fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                            fill="currentColor" />
                          <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                            fill="currentColor" />
                        </svg>
                        <span class="text-blue-500 hover:underline">Add a field to the right</span>
                      </div>
                    </div>

                    <div
                      class="flex cursor-pointer items-center text-red-500 hover:text-red-900"
                      @click="destroyRow(row)">
                      <svg
                        class="mr-1 inline h-3 w-3"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M16.3956 7.75734C16.7862 8.14786 16.7862 8.78103 16.3956 9.17155L13.4142 12.153L16.0896 14.8284C16.4802 15.2189 16.4802 15.8521 16.0896 16.2426C15.6991 16.6331 15.0659 16.6331 14.6754 16.2426L12 13.5672L9.32458 16.2426C8.93405 16.6331 8.30089 16.6331 7.91036 16.2426C7.51984 15.8521 7.51984 15.2189 7.91036 14.8284L10.5858 12.153L7.60436 9.17155C7.21383 8.78103 7.21383 8.14786 7.60436 7.75734C7.99488 7.36681 8.62805 7.36681 9.01857 7.75734L12 10.7388L14.9814 7.75734C15.372 7.36681 16.0051 7.36681 16.3956 7.75734Z"
                          fill="currentColor" />
                        <path
                          fill-rule="evenodd"
                          clip-rule="evenodd"
                          d="M4 1C2.34315 1 1 2.34315 1 4V20C1 21.6569 2.34315 23 4 23H20C21.6569 23 23 21.6569 23 20V4C23 2.34315 21.6569 1 20 1H4ZM20 3H4C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V4C21 3.44772 20.5523 3 20 3Z"
                          fill="currentColor" />
                      </svg>
                      <span>Delete row</span>
                    </div>
                  </div>

                  <!-- row fields -->
                  <div class="m-3 grid auto-cols-fr grid-flow-col rounded border border-gray-200">
                    <div
                      v-for="field in row.fields"
                      :key="field.id"
                      class="border-r border-gray-200 last:border-r-0 dark:border-gray-700">
                      <!-- row options -->
                      <div class="flex justify-between border-b border-gray-200 px-3 py-1 text-xs dark:border-gray-700">
                        <div>
                          <div v-if="!field.inputSelectionMode" class="relative mr-3 inline cursor-pointer">
                            <svg
                              class="mr-1 inline h-3 w-3"
                              viewBox="0 0 24 24"
                              fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                d="M12 6C12.5523 6 13 6.44772 13 7V11H17C17.5523 11 18 11.4477 18 12C18 12.5523 17.5523 13 17 13H13V17C13 17.5523 12.5523 18 12 18C11.4477 18 11 17.5523 11 17V13H7C6.44772 13 6 12.5523 6 12C6 11.4477 6.44772 11 7 11H11V7C11 6.44772 11.4477 6 12 6Z"
                                fill="currentColor" />
                              <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M5 22C3.34315 22 2 20.6569 2 19V5C2 3.34315 3.34315 2 5 2H19C20.6569 2 22 3.34315 22 5V19C22 20.6569 20.6569 22 19 22H5ZM4 19C4 19.5523 4.44772 20 5 20H19C19.5523 20 20 19.5523 20 19V5C20 4.44772 19.5523 4 19 4H5C4.44772 4 4 4.44772 4 5V19Z"
                                fill="currentColor" />
                            </svg>
                            <span @click="changeType(row, field)" class="text-blue-500 hover:underline"
                              >Change field type</span
                            >
                          </div>
                        </div>

                        <div
                          class="flex cursor-pointer items-center text-red-500 hover:text-red-900"
                          @click="destroyField(row, field)">
                          <svg
                            class="mr-1 inline h-3 w-3"
                            viewBox="0 0 24 24"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                              d="M16.3956 7.75734C16.7862 8.14786 16.7862 8.78103 16.3956 9.17155L13.4142 12.153L16.0896 14.8284C16.4802 15.2189 16.4802 15.8521 16.0896 16.2426C15.6991 16.6331 15.0659 16.6331 14.6754 16.2426L12 13.5672L9.32458 16.2426C8.93405 16.6331 8.30089 16.6331 7.91036 16.2426C7.51984 15.8521 7.51984 15.2189 7.91036 14.8284L10.5858 12.153L7.60436 9.17155C7.21383 8.78103 7.21383 8.14786 7.60436 7.75734C7.99488 7.36681 8.62805 7.36681 9.01857 7.75734L12 10.7388L14.9814 7.75734C15.372 7.36681 16.0051 7.36681 16.3956 7.75734Z"
                              fill="currentColor" />
                            <path
                              fill-rule="evenodd"
                              clip-rule="evenodd"
                              d="M4 1C2.34315 1 1 2.34315 1 4V20C1 21.6569 2.34315 23 4 23H20C21.6569 23 23 21.6569 23 20V4C23 2.34315 21.6569 1 20 1H4ZM20 3H4C3.44772 3 3 3.44772 3 4V20C3 20.5523 3.44772 21 4 21H20C20.5523 21 21 20.5523 21 20V4C21 3.44772 20.5523 3 20 3Z"
                              fill="currentColor" />
                          </svg>
                          <span>Delete field</span>
                        </div>
                      </div>

                      <!-- choose a field type -->
                      <div v-if="field.inputSelectionMode" class="px-5 py-5 text-center">
                        <p class="mb-3 text-xs text-gray-600">Choose a field type:</p>
                        <ul>
                          <li @click="addInput(row, field)" class="cursor-pointer">Text field</li>
                          <li @click="addTextarea(row, field)" class="cursor-pointer">Text area</li>
                          <li @click="addDropdown(row, field)" class="cursor-pointer">Dropdown</li>
                        </ul>
                      </div>

                      <!-- case of text input or textarea  -->
                      <div class="p-5" v-if="field.type == 'input' || field.type == 'textarea'">
                        <!-- type -->
                        <div
                          v-if="field.type == 'input'"
                          class="mr-2 mb-4 inline-block rounded bg-neutral-200 py-1 px-2 text-xs font-semibold text-neutral-800 last:mr-0">
                          text field
                        </div>
                        <div
                          v-if="field.type == 'textarea'"
                          class="mr-2 mb-4 inline-block rounded bg-neutral-200 py-1 px-2 text-xs font-semibold text-neutral-800 last:mr-0">
                          text area
                        </div>

                        <!-- name of the field -->
                        <text-input
                          class="mb-4"
                          v-model="field.name"
                          :type="'text'"
                          :autofocus="true"
                          :label="'Name of the field'"
                          :input-class="'block w-full'"
                          :required="true"
                          :autocomplete="false"
                          :maxlength="255" />

                        <!-- placeholder -->
                        <text-input
                          class="mb-4"
                          v-model="field.placeholder"
                          :type="'text'"
                          :label="'Placeholder value'"
                          :input-class="'block w-full'"
                          :help="'This text will be displayed when the field is empty.'"
                          :required="false"
                          :autocomplete="false"
                          :maxlength="255" />

                        <!-- help text -->
                        <text-input
                          v-model="field.help"
                          :type="'text'"
                          :label="'Help text'"
                          :help="'This text will be displayed beneath the input field.'"
                          :input-class="'block w-full'"
                          :required="false"
                          :autocomplete="false"
                          :maxlength="255" />

                        <!-- mandatory or not -->
                        <div class="flex items-center">
                          <input
                            v-model="field.required"
                            :id="'required' + row.position + field.position"
                            :name="'required' + row.position + field.position"
                            type="checkbox"
                            class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 focus:dark:ring-blue-600" />
                          <label
                            :for="'required' + row.position + field.position"
                            class="ml-2 cursor-pointer text-sm text-gray-900 dark:text-gray-100">
                            This field is mandatory for the form to be submitted
                          </label>
                        </div>
                      </div>

                      <!-- case of dropdown  -->
                      <div class="p-5" v-if="field.type == 'dropdown'">
                        <!-- type -->
                        <div
                          class="mr-2 mb-4 inline-block rounded bg-neutral-200 py-1 px-2 text-xs font-semibold text-neutral-800 last:mr-0">
                          dropdown
                        </div>

                        <!-- name of the field -->
                        <text-input
                          class="mb-4"
                          v-model="field.name"
                          :type="'text'"
                          :autofocus="true"
                          :label="'Name of the field'"
                          :input-class="'block w-full'"
                          :required="true"
                          :autocomplete="false"
                          :maxlength="255" />

                        <!-- help text -->
                        <text-input
                          v-model="field.help"
                          :type="'text'"
                          :label="'Help text'"
                          :help="'This text will be displayed beneath the dropdown.'"
                          :input-class="'block w-full'"
                          :required="false"
                          :autocomplete="false"
                          :maxlength="255" />

                        <!-- dropdown choices -->
                        <p class="mb-2 text-sm">List of choices that will be presented to the user</p>

                        <div v-for="choice in field.choices" :key="choice.id">
                          <div class="mb-4 flex items-center">
                            <text-input
                              class="w-2/3"
                              v-model="choice.label"
                              :type="'text'"
                              :autofocus="true"
                              :input-class="'block w-full'"
                              :required="true"
                              :autocomplete="false"
                              :maxlength="255" />

                            <div
                              class="ml-2 cursor-pointer text-sm text-red-500 hover:text-red-900"
                              @click="destroyChoice(field, choice)">
                              <span>Delete</span>
                            </div>
                          </div>
                        </div>

                        <!-- if there are no choices yet -->
                        <div
                          @click="addDropdownChoice(field)"
                          class="cursor-pointer text-sm text-blue-500 hover:underline">
                          + Add an item in the dropdown
                        </div>
                      </div>
                    </div>

                    <!-- no row / blank state -->
                    <div v-if="row.fields.length == 0" class="p3 rounded bg-gray-50 py-5 text-center">
                      Please add a field
                    </div>
                  </div>
                </div>
              </div>

              <!-- add a new row CTA -->
              <div
                class="mx-16 mb-2 cursor-pointer rounded border border-gray-300 bg-white px-5 py-3 text-center dark:bg-gray-900"
                @click="addRow()">
                + Add row
              </div>
            </div>

            <!-- actions -->
            <div class="flex justify-between p-5">
              <pretty-link :href="data.url.back" :text="$t('app.cancel')" :classes="'mr-3'" />
              <pretty-button
                :href="'data.url.store'"
                :text="$t('app.add')"
                :state="loadingState"
                :icon="'check'"
                :classes="'save'" />
            </div>
          </form>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped></style>
