<template>
  <div class="mb-4">
    <div class="ml-4 border-l border-gray-200">
      <div v-for="feedItem in data.items" :key="feedItem.id" class="mb-8">
        <!-- action & user -->
        <div class="mb-3 flex">
          <div class="icon-avatar relative w-6">
            <avatar :data="feedItem.author.avatar" :classes="'rounded-full border-gray-200 border relative h-5 w-5'" />
          </div>

          <div>
            <p class="mr-2 inline text-gray-400">
              <!-- contact name + link -->
              <inertia-link
                v-if="feedItem.author.url"
                :href="feedItem.author.url"
                class="font-medium text-gray-800 hover:underline"
                >{{ feedItem.author.name }}</inertia-link
              >
              <span v-else class="font-medium text-gray-800">{{ feedItem.author.name }}</span>

              <!-- action -->
              <span class="ml-2">{{ feedItem.sentence }}</span>
            </p>
            <p class="mr-2 inline">•</p>
            <p class="inline text-sm text-gray-400">
              {{ feedItem.created_at }}
            </p>
          </div>
        </div>

        <div v-if="feedItem.data" class="ml-6">
          <contact-created v-if="feedItem.action === 'contact_created'" :data="feedItem.data" />
        </div>

        <!-- details -->
        <div v-if="feedItem.description" class="ml-6">
          <div class="rounded-lg border border-gray-300 px-3 py-2">
            <span class="text-sm">
              {{ feedItem.description }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- blank state -->
    <div v-if="data.items.length == 0">
      <p class="p-5 text-center">This person doesn't have any activity yet.</p>
    </div>
  </div>
</template>

<script>
import Avatar from '@/Shared/Avatar.vue';
import ContactCreated from '@/Shared/Modules/FeedItems/ContactCreated.vue';

export default {
  components: {
    Avatar,
    ContactCreated,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped>
.icon-avatar {
  top: 3px;
  left: -11px;
}
</style>
