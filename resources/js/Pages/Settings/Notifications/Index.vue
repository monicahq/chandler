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
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Notification channels</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <!-- title + cta -->
        <div class="mb-3 mt-8 items-center justify-between sm:mt-0 sm:flex">
          <h3 class="mb-4 sm:mb-0"><span class="mr-1">🛰️</span> Configure how we should notify you</h3>
          <pretty-button v-if="!editMode" :text="'Edit'" @click="enableEditMode" />
        </div>

        <!-- help text -->
        <div class="mb-6 flex rounded border bg-slate-50 px-3 py-2 text-sm">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 grow pr-2"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <div>
            <p>
              You can be notified through different channels: emails, a Telegram message, on Facebook. You decide.
            </p>
          </div>
        </div>

        <!-- normal mode -->
        <div class="mb-3 items-center flex justify-between">
          <span>Via email</span>

          <pretty-button
            v-if="!createAddressTypeModalShown"
            :text="'Add an email'"
            :icon="'plus'"
            @click="showAddressTypeModal" />
        </div>
        <ul v-if="!editMode" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <li v-for="email in localEmails" :key="email.id" class="item-list border-b border-gray-200 hover:bg-slate-50 flex items-center justify-between px-5 py-2">
            <div class="flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-2 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>

              <!-- email address + label -->
              <div>
                <span class="block mb-0">{{ email.content }}</span>
                <ul class="text-sm text-gray-500 mr-2 bulleted-list">
                  <li v-if="email.label" class="mr-1 inline">{{ email.label }}</li>
                  <li class="inline">Sent at 9:00pm</li>
                </ul>
              </div>
            </div>

            <!-- actions -->
            <ul class="text-sm">
              <li
                v-if="email.active"
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                @click="updateAdressTypeModal(addressType)">
                Deactivate
              </li>
              <li
                v-if="!email.active"
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                @click="updateAdressTypeModal(addressType)">
                Activate
              </li>
              <li
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">
                View log
              </li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(addressType)">
                Delete
              </li>
            </ul>
          </li>
        </ul>

        <p>Via Telegram</p>
        <ul v-if="!editMode" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <li class="item-list border-b border-gray-200 hover:bg-slate-50 flex items-center justify-between">
            <span>r@c.com</span>

            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </li>
        </ul>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    Layout,
    PrettyButton,
    PrettySpan,
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
      localEmails: [],
      form: {
        name: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localEmails = this.data.emails;
  },
};
</script>
