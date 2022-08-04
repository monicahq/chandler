<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-slate-200">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.contacts" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_contact_index') }}
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
              <inertia-link :href="data.url.contact" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_contact_show', { name: data.contact.name }) }}
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
              {{ $t('app.breadcrumb_contact_note_index') }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <h1>{{ data.name }}</h1>

        <div class="grid">
          <div v-for="week in data.weeks" :key="week.id">
            <div v-for="streak in week.streaks" :key="streak.id" class="">
              <a-tooltip placement="topLeft" :title="streak.date" arrow-point-at-center>
                <!-- there is a streak for this day -->
                <div
                  v-if="!streak.not_yet_happened && streak.streak"
                  class="h-3 w-3 cursor-pointer rounded border border-transparent bg-green-400 hover:border-green-500"></div>

                <!-- there is not a streak for this day -->
                <div
                  v-if="!streak.not_yet_happened && !streak.streak"
                  class="h-3 w-3 cursor-pointer rounded bg-slate-200"></div>
              </a-tooltip>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import ContactName from '@/Shared/Modules/ContactName';

export default {
  components: {
    Layout,
    ContactName,
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

  data() {},

  methods: {},
};
</script>

<style lang="scss" scoped>
.grid {
  grid-template-columns: repeat(53, 1fr);

  & > div {
    grid-column: 2px;

    & > div {
      margin-bottom: 2px;
    }
  }
}
</style>
