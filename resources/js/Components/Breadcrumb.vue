<script setup>
defineProps({
  items: Array,
});
</script>

<template>
  <div class="flex items-baseline justify-between space-x-6">
    <ul class="text-sm">
      <li class="inline text-gray-600 ltr:mr-2 rtl:ml-2 dark:text-gray-400">
        {{ $t('You are here:') }}
      </li>
      <template v-for="(item, index) in items" :key="index">
        <li v-if="index > 0" class="relative inline ltr:ml-2 rtl:mr-2">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon-breadcrumb relative inline h-3 w-3"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </li>

        <li class="inline" :class="{ 'ltr:ml-2 rtl:mr-2': index > 0 }">
          <inertia-link v-if="item.url" :href="item.url" class="text-blue-500 hover:underline">
            {{ item.name }}
          </inertia-link>
          <template v-else>
            {{ item.name }}
          </template>
        </li>
      </template>
    </ul>
  </div>
</template>
