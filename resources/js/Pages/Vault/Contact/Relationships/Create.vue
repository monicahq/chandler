<style lang="scss" scoped>
.section-head {
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
}
</style>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600">You are here:</li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.contacts" class="text-sky-500 hover:text-blue-900">
                Contacts
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
              <inertia-link :href="data.url.contact" class="text-sky-500 hover:text-blue-900">
                Profile of {{ data.contact.name }}
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
            <li class="inline">Add a relationship</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-16">
      <div class="mx-auto max-w-lg px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <form class="mb-6 rounded-lg border border-gray-200 bg-white" @submit.prevent="submit()">
          <div class="section-head border-b border-gray-200 bg-blue-50 p-5">
            <h1 class="text-center text-2xl font-medium">Add a relationship</h1>
          </div>
          <div class="border-b border-gray-200 p-5">
            <errors :errors="form.errors" />

            <!-- relationship type -->
            <dropdown
              v-model="form.gender_id"
              :data="data.genders"
              :required="false"
              :placeholder="'Choose a value'"
              :dropdown-class="'block w-full'"
              :label="'Choose a relationship type'" />
          </div>

          <div class="border-b border-gray-200 p-5">
            <div class="">
              <!-- relationship -->
              <div class="mb-6">
                <p class="mb-2 text-sm text-gray-700">Father</p>
                <div class="flex items-center">
                  <div v-html="data.contact.avatar" class="mr-2 h-5 w-5"></div>

                  <span>{{ data.contact.name }}</span>
                </div>
              </div>

              <!-- switch -->
              <div class="w-100 mx-auto mr-4 block text-center">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="mx-auto block h-5 w-5"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                </svg>
                <span class="text-xs">Switch</span>
              </div>

              <!-- reverse relationship -->
              <div>
                <p class="mb-2 text-sm text-gray-700">Father</p>
                <div class="">
                  <!-- I don't know the name -->
                  <div class="mb-2 flex items-center">
                    <input
                      id="first_name_last_name"
                      v-model="form.nameOrder"
                      value="%first_name% %last_name%"
                      name="name-order"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500" />
                    <label
                      for="first_name_last_name"
                      class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                      I don't know the name
                    </label>
                  </div>

                  <!-- I know the contact's name -->
                  <div class="mb-2 flex items-center">
                    <input
                      id="first_name_last_name"
                      v-model="form.nameOrder"
                      value="%first_name% %last_name%"
                      name="name-order"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500" />
                    <label
                      for="first_name_last_name"
                      class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                      I know the contact's name
                    </label>
                  </div>

                  <!-- Choose an existing contact -->
                  <div class="mb-2 flex items-center">
                    <input
                      id="first_name_last_name"
                      v-model="form.nameOrder"
                      value="%first_name% %last_name%"
                      name="name-order"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500" />
                    <label
                      for="first_name_last_name"
                      class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                      Choose an existing contact
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- first name -->
            <text-input
              v-model="form.first_name"
              :autofocus="true"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="true"
              :maxlength="255"
              :label="'First name'" />

            <!-- last name -->
            <text-input
              :id="'last_name'"
              v-model="form.last_name"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="false"
              :maxlength="255"
              :label="'Last name'" />

            <!-- middle name -->
            <text-input
              v-if="showMiddleNameField"
              :id="'middle_name'"
              v-model="form.middle_name"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="false"
              :maxlength="255"
              :label="'Middle name'" />

            <!-- nickname -->
            <text-input
              v-if="showNicknameField"
              :id="'nickname'"
              v-model="form.nickname"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="false"
              :maxlength="255"
              :label="'Nickname'" />

            <!-- nickname -->
            <text-input
              v-if="showMaidenNameField"
              :id="'maiden_name'"
              v-model="form.maiden_name"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="false"
              :maxlength="255"
              :label="'Maiden name'" />

            <!-- genders -->
            <dropdown
              v-if="showGenderField"
              v-model="form.gender_id"
              :data="data.genders"
              :required="false"
              :div-outer-class="'mb-5'"
              :placeholder="'Choose a value'"
              :dropdown-class="'block w-full'"
              :label="'Gender'" />

            <!-- pronouns -->
            <dropdown
              v-if="showPronounField"
              v-model="form.pronoun_id"
              :data="data.pronouns"
              :required="false"
              :div-outer-class="'mb-5'"
              :placeholder="'Choose a value'"
              :dropdown-class="'block w-full'"
              :label="'Pronoun'" />

            <!-- other fields -->
            <div class="flex flex-wrap text-xs">
              <span
                v-if="!showMiddleNameField"
                class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300"
                @click="displayMiddleNameField">
                + middle name
              </span>
              <span
                v-if="!showNicknameField"
                class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300"
                @click="displayNicknameField">
                + nickname
              </span>
              <span
                v-if="!showMaidenNameField"
                class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300"
                @click="displayMaidenNameField">
                + maiden name
              </span>
              <span
                v-if="data.genders.length > 0 && !showGenderField"
                class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300"
                @click="displayGenderField">
                + gender
              </span>
              <span
                v-if="data.pronouns.length > 0 && !showPronounField"
                class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300"
                @click="displayPronounField">
                + pronoun
              </span>
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
              :classes="'save'" />
          </div>
        </form>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyLink from '@/Shared/Form/PrettyLink';
import PrettyButton from '@/Shared/Form/PrettyButton';
import TextInput from '@/Shared/Form/TextInput';
import Dropdown from '@/Shared/Form/Dropdown';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    Layout,
    PrettyLink,
    PrettyButton,
    TextInput,
    Dropdown,
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
      showMiddleNameField: false,
      showNicknameField: false,
      showMaidenNameField: false,
      showGenderField: false,
      showPronounField: false,
      showTemplateField: false,
      form: {
        first_name: '',
        last_name: '',
        middle_name: '',
        nickname: '',
        maiden_name: '',
        gender_id: '',
        pronoun_id: '',
        template_id: '',
        errors: [],
      },
    };
  },

  methods: {
    displayMiddleNameField() {
      this.showMiddleNameField = true;
    },

    displayNicknameField() {
      this.showNicknameField = true;
    },

    displayMaidenNameField() {
      this.showMaidenNameField = true;
    },

    displayGenderField() {
      this.showGenderField = true;
    },

    displayPronounField() {
      this.showPronounField = true;
    },

    displayTemplateField() {
      this.showTemplateField = true;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          localStorage.success = 'The contact has been added';
          this.$inertia.visit(response.data.data);
        })
        .catch((error) => {
          this.form.errors = error.response.data;
          this.loadingState = null;
        });
    },
  },
};
</script>
