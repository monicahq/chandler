<style lang="scss" scoped>
.section-head {
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
}
</style>

<template>
  <Layout :layoutData="layoutData">
    <!-- breadcrumb -->
    <nav class="sm:border-b bg-white">
      <div class="max-w-8xl mx-auto px-4 sm:px-6 py-2 hidden md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="inline mr-2 text-gray-600">You are here:</li>
            <li class="inline mr-2">
              <Link :href="data.url.settings" class="text-sky-500 hover:text-blue-900">Settings</Link>
            </li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline mr-2"><Link :href="data.url.personalize" class="text-sky-500 hover:text-blue-900">Personalize your account</Link></li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Relationship types</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-20 relative">
      <div class="max-w-3xl mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="px-3 py-2 border mb-6 flex rounded text-sm bg-slate-50">
          <svg xmlns="http://www.w3.org/2000/svg" class="grow h-6 pr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <div>
            <p class="mb-2">When you define a relationship between two contacts, for instance a father-son relationship, Monica creates two relations, one for each contact:</p>
            <ul class="list-disc pl-4 mb-2">
              <li>a father-son relation—shown on the father page,</li>
              <li>a son-father relation—shown on the son page.</li>
            </ul>
            <p class="mb-2">We call them a relation, and its reverse relation. For each relation you define, you need to define its counterpart.</p>
          </div>
        </div>

        <!-- title + cta -->
        <div class="flex items-center justify-between mb-6">
          <h3>All the relationship types</h3>
          <pretty-button :href="''" :text="'Add a new group type'" :icon="'plus'" />
        </div>

        <!-- list of groups types -->
        <ul class="bg-white border border-gray-200 rounded-lg mb-6">
          <li v-for="groupType in data.group_types" :key="groupType.id">
            <div class="flex justify-between items-center px-5 py-2 border-b border-gray-200 hover:bg-slate-50">
              <span class="text-base font-semibold">{{ groupType.name }}</span>

              <!-- actions -->
              <ul class="text-sm">
                <li class="inline mr-4 text-sky-500 hover:text-blue-900">Rename</li>
                <li class="inline text-red-500 hover:text-red-900">Delete</li>
              </ul>
            </div>

            <div v-for="type in groupType.types" :key="type.id" class="px-5 py-2 border-b border-gray-200 hover:bg-slate-50 pl-6">
              <div class="flex justify-between items-center">
                <div class="relative">

                  <!-- relation type name -->
                  <span>{{ type.name }}</span>

                  <svg xmlns="http://www.w3.org/2000/svg" class="px-1 h-5 w-5 inline relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>

                  <!-- relation type reverse name -->
                  <span>{{ type.name_reverse_relationship }}</span>
                </div>

                <!-- actions -->
                <ul class="text-sm">
                  <li class="inline mr-4 text-sky-500 hover:text-blue-900">Rename</li>
                  <li class="inline text-red-500 hover:text-red-900">Delete</li>
                </ul>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </main>
  </Layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import { Link } from '@inertiajs/inertia-vue3';
import PrettyButton from '@/Shared/PrettyButton';

export default {
  components: {
    Layout,
    Link,
    PrettyButton,
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
