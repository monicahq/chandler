<template>
  <layout :layout-data="layoutData">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="me-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('You are here:') }}
            </li>
            <li class="me-2 inline">
              <inertia-link :href="data.url.settings" class="text-blue-500 hover:underline">
                {{ $t('Settings') }}
              </inertia-link>
            </li>
            <li class="relative me-2 inline">
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
              {{ $t('Personalize your account') }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-md px-2 py-2 sm:px-6 sm:py-6 lg:px-8">
        <h2 class="mb-6 text-center text-lg">
          {{ $t('Personalize your account') }}
        </h2>
        <div class="rounded-lg border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-900">
          <ul>
            <li class="mb-2 flex">
              <span class="me-2">📐</span>
              <inertia-link :href="data.url.manage_templates" class="text-blue-500 hover:underline">
                {{ $t('Manage templates') }}
              </inertia-link>
            </li>
            <!-- <li class="mb-2 flex">
              <span class="me-2">🥪</span>
              <inertia-link :href="data.url.manage_modules" class="text-blue-500 hover:underline">
                {{ $t('Manage modules') }}
              </inertia-link>
            </li> -->
            <li class="mb-2 flex">
              <span class="me-2">👥</span>
              <inertia-link :href="data.url.manage_relationships" class="text-blue-500 hover:underline">
                {{ $t('Manage relationship types') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">👥</span>
              <inertia-link :href="data.url.manage_group_types" class="text-blue-500 hover:underline">
                {{ $t('Manage group types') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">👩‍🔬</span>
              <inertia-link :href="data.url.manage_pronouns" class="text-blue-500 hover:underline">
                {{ $t('Manage pronouns') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">🚻</span>
              <inertia-link :href="data.url.manage_genders" class="text-blue-500 hover:underline">
                {{ $t('Manage genders') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">🏖</span>
              <inertia-link :href="data.url.manage_address_types" class="text-blue-500 hover:underline">
                {{ $t('Manage address types') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">☎️</span>
              <inertia-link :href="data.url.manage_contact_information_types" class="text-blue-500 hover:underline">
                {{ $t('Manage contact information types') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">📲</span>
              <inertia-link :href="data.url.manage_call_reasons" class="text-blue-500 hover:underline">
                {{ $t('Manage call reasons') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">🐱</span>
              <inertia-link :href="data.url.manage_pet_categories" class="text-blue-500 hover:underline">
                {{ $t('Manage pet categories') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">🎁</span>
              <inertia-link :href="data.url.manage_gift_occasions" class="text-blue-500 hover:underline">
                {{ $t('Manage gift occasions') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">🎁</span>
              <inertia-link :href="data.url.manage_gift_states" class="text-blue-500 hover:underline">
                {{ $t('Manage gift states') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">📮</span>
              <inertia-link :href="data.url.manage_post_templates" class="text-blue-500 hover:underline">
                {{ $t('Manage post templates') }}
              </inertia-link>
            </li>
            <li class="mb-2 flex">
              <span class="me-2">💵</span>
              <inertia-link :href="data.url.manage_currencies" class="text-blue-500 hover:underline">
                {{ $t('Manage currencies') }}
              </inertia-link>
            </li>
            <li class="flex">
              <span class="me-2">⛪</span>
              <inertia-link :href="data.url.manage_religions" class="text-blue-500 hover:underline">
                {{ $t('Manage religions') }}
              </inertia-link>
            </li>
          </ul>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout.vue';

export default {
  components: {
    Layout,
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
};
</script>

<style lang="scss" scoped>
.section-head {
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
}
</style>
