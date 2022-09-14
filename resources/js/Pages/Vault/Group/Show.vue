<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.groups" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_group_index') }}
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
            <li class="inline">
              {{ data.name }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-6xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- group title -->
        <h1 class="mb-8 text-center text-2xl">{{ data.name }}</h1>

        <div v-for="role in data.roles" :key="role.id" class="mb-8">
          <p
            class="mr-2 mb-2 inline-block rounded bg-neutral-200 py-1 px-2 text-xs font-semibold text-neutral-800 last:mr-0">
            {{ role.label }}
          </p>

          <div class="grid grid-cols-3 gap-x-12 sm:grid-cols-4">
            <div
              v-for="contact in role.contacts"
              :key="contact.id"
              class="rounded-lg border border-gray-200 bg-white p-3 text-center dark:border-gray-700 dark:bg-gray-900">
              <avatar :data="contact.avatar" :classes="'inline-block rounded-full h-14 w-14'" />

              <inertia-link class="text-blue-500 hover:underline">{{ contact.name }}</inertia-link>

              <span v-if="contact.age" class="ml-1 text-xs text-gray-500">({{ contact.age }})</span>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue';
import Avatar from '@/Shared/Avatar.vue';

export default {
  components: {
    Layout,
    Avatar,
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
    return {};
  },

  methods: {},
};
</script>

<style lang="scss" scoped></style>
