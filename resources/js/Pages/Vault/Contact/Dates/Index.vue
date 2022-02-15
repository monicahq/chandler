<style lang="scss" scoped>
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
  <layout :layout-data="layoutData">
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
                stroke="currentColor"
              >
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
                stroke="currentColor"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">All the important dates</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- list of dates -->
        <ul v-if="localDates.length > 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <li
            v-for="addressType in localDates"
            :key="addressType.id"
            class="item-list border-b border-gray-200 hover:bg-slate-50"
          >
            <!-- detail of the group type -->
            <div
              v-if="renameAddressTypeModalShownId != addressType.id"
              class="flex items-center justify-between px-5 py-2"
            >
              <span class="text-base">{{ addressType.name }}</span>

              <!-- actions -->
              <ul class="text-sm">
                <li
                  class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                  @click="updateAdressTypeModal(addressType)"
                >
                  Rename
                </li>
                <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(addressType)">
                  Delete
                </li>
              </ul>
            </div>

            <!-- rename a addressType modal -->
            <form
              v-if="renameAddressTypeModalShownId == addressType.id"
              class="item-list border-b border-gray-200 hover:bg-slate-50"
              @submit.prevent="update(addressType)"
            >
              <div class="border-b border-gray-200 p-5">
                <errors :errors="form.errors" />

                <text-input
                  :ref="'rename' + addressType.id"
                  v-model="form.name"
                  :label="'Name'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="renameAddressTypeModalShownId = 0"
                />
              </div>

              <div class="flex justify-between p-5">
                <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="renameAddressTypeModalShownId = 0" />
                <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import NameOrder from '@/Pages/Settings/Preferences/Partials/NameOrder';
import DateFormat from '@/Pages/Settings/Preferences/Partials/DateFormat';

export default {
  components: {
    Layout,
    NameOrder,
    DateFormat,
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
      createAddressTypeModalShown: false,
      renameAddressTypeModalShownId: 0,
      localDates: [],
      form: {
        name: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localDates = this.data.dates;
  },
};
</script>
