<template>
  <div v-if="data.love_relationships.length != 0 || data.family_relationships.length != 0" class="mb-4">
    <div class="mb-3 items-center justify-between border-b border-gray-200 dark:border-gray-700 sm:flex">
      <div class="mb-1 text-xs">{{ $t('Family summary') }}</div>
    </div>

    <!-- love relationships -->
    <div v-if="data.love_relationships.length > 0" class="flex">
      <span class="inline text-sm ltr:mr-2 rtl:ml-2"> ❤️ </span>
      <ul>
        <li v-for="relationship in data.love_relationships" :key="relationship.id" class="inline ltr:mr-2 rtl:ml-2">
          <!-- name -->
          <inertia-link
            v-if="relationship.contact.url.show"
            :href="relationship.contact.url.show"
            class="text-blue-500 hover:underline">
            {{ relationship.contact.name }}
          </inertia-link>
          <span v-else>{{ relationship.contact.name }}</span>

          <!-- age -->
          <span v-if="relationship.contact.age" class="text-xs text-gray-400 ltr:ml-2 rtl:mr-2"
            >({{ relationship.contact.age }})</span
          >
        </li>
      </ul>
    </div>

    <!-- family relationships -->
    <div v-if="data.family_relationships.length > 0" class="flex">
      <span class="inline text-sm ltr:mr-2 rtl:ml-2"> 👪 </span>
      <ul>
        <li v-for="relationship in data.family_relationships" :key="relationship.id" class="inline ltr:mr-2 rtl:ml-2">
          <!-- name -->
          <inertia-link
            v-if="relationship.contact.url.show"
            :href="relationship.contact.url.show"
            class="text-blue-500 hover:underline">
            {{ relationship.contact.name }}
          </inertia-link>
          <span v-else>{{ relationship.contact.name }}</span>

          <!-- age -->
          <span v-if="relationship.contact.age" class="text-xs text-gray-400 ltr:ml-2 rtl:mr-2"
            >({{ relationship.contact.age }})</span
          >
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
