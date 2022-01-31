<style lang="scss" scoped>
.grid {
  grid-template-columns: 1fr 2fr;
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
  }
}

.module-list {
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
  <layout :layout-data="layoutData">
    <!-- breadcrumb -->
    <nav class="bg-white sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600">You are here:</li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.settings" class="text-sky-500 hover:text-blue-900"> Settings </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.personalize" class="text-sky-500 hover:text-blue-900">
                Personalize your account
              </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Modules</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-16">
      <div class="mx-auto max-w-5xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-6 mt-8 sm:mt-0">
          <h3 class="mb-4 text-center text-xl sm:mb-2">All the modules in the account</h3>
        </div>

        <!-- help text -->
        <div class="mb-10 flex rounded border bg-slate-50 px-3 py-2 text-sm">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 pr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
          </svg>

          <div>
            <p class="mb-1">Modules contain each one of your contact's data.</p>
            <p class="mb-1">
              Monica comes with a set of predefined modules that can't be edited or deleted – because we need them for
              Monica to function properly. However, you can create your own modules to record the data you want in your
              account.
            </p>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            <div class="mb-4 mt-8 items-center justify-between border-b pb-3 sm:mt-0 sm:flex">
              <h3>Modules</h3>
              <pretty-button :text="'Add new module'" :icon="'plus'" @click="showPageModal" />
            </div>

            <div>
              <!-- search a module -->
              <div
                class="module-list rounded-t-md border-t border-r border-l border-gray-200 px-3 py-2 hover:bg-slate-50"
              >
                <text-input
                  v-model="form.search"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :placeholder="'Filter'"
                  :maxlength="255"
                />
              </div>

              <!-- list of modules -->
              <ul class="h-80 overflow-auto rounded-b border border-gray-200 bg-white">
                <li v-for="module in data.modules" :key="module.id" class="module-list border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
                  <span class="">{{ module.name }}</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- middle -->
          <div class="rounded-lg border border-gray-200">
            <h3 class="border-b border-gray-200 px-5 py-2">Module details</h3>

            <errors :errors="form.errors" />

            <!-- module details -->
            <div class="border-b border-gray-200 p-5">
              <text-input
                v-model="form.search"
                :type="'text'"
                :autofocus="true"
                :label="'Name of the module'"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255"
              />
             </div>

            <!-- content of the module -->
            <div class="border-b border-gray-200 p-5 bg-gray-100">
              <div @click="addRow()" class="mb-2 border rounded border-gray-300 px-5 py-3 bg-white text-center">
                + Add row
              </div>

              <div v-for="row in form.rows" :key="row.realId" class="mb-2">
                <div class="border rounded border-gray-300 bg-white">

                  <!-- row options -->
                  <div class="text-xs px-3 py-3 border-b border-gray-200 flex justify-between">
                    <div>
                      <span class="inline cursor-pointer mr-2">Add a field to the left</span>
                      <span class="inline cursor-pointer mr-2">Add a field to the right</span>
                    </div>

                    <span class="inline cursor-pointer">Delete row</span>
                  </div>

                  <!-- row fields -->
                  <div v-for="field in row.fields" :key="field.id">
                    abs
                  </div>
                </div>
              </div>
            </div>

             <!-- actions -->
            <div class="flex justify-between p-5">
              <pretty-link :href="data.url.back" :text="'Cancel'" :classes="'mr-3'" />
              <pretty-button
                :href="'data.url.vault.create'"
                :text="'Add'"
                :state="loadingState"
                :icon="'check'"
                :classes="'save'"
              />
            </div>

            <!-- blank state -->
            <div class="mb-6">
              <p class="p-5 text-center">Please select a module on the left or create a new module.</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyLink from '@/Shared/Form/PrettyLink';
import PrettyButton from '@/Shared/Form/PrettyButton';
import TextInput from '@/Shared/Form/TextInput';

export default {
  components: {
    Layout,
    PrettyLink,
    PrettyButton,
    TextInput,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      addMode: false,
      relativeId: 0,
      localModules: [],
      realId: 0, // real id doesn't get updated when array is reordered. this is used to uniquely identify the item in the array.
      form: {
        search: '',
        name: '',
        rows: [],
        errors: [],
      },
    };
  },

  methods: {
    addRow() {
      this.relativeId = this.relativeId + 1;
      this.realId = this.realId + 1;

      this.form.rows.push({
        id: this.relativeId,
        realId: this.realId,
        fields: [{
          id: 1,
          name: 'sdsfa',
        }],
      });
    },

    addField(row) {
      this.relativeId = this.relativeId + 1;
      this.realId = this.realId + 1;

      this.form.rows.push({
        id: this.relativeId,
        realId: this.realId,
      });
    },
  },
};
</script>
