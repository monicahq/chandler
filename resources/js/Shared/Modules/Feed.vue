<style lang="scss" scoped>
.icon-avatar {
  top: -1px;
  left: -12px;
}
</style>

<template>
  <div class="mb-4">
    <div class="ml-4 border-l border-gray-200">
      <div v-for="feedItem in data.items" :key="feedItem.id" class="mb-4">
        <!-- action & user -->
        <div class="flex mb-3">
          <div class="relative w-6">
            <div v-html="feedItem.author.avatar" class="icon-avatar h-6 w-6 relative"></div>
          </div>

          <div>
            <p class="inline mr-2 text-gray-400"><span class="font-medium text-gray-800">{{ feedItem.author.name }}</span> {{ feedItem.sentence }}</p>
            <p class="inline mr-2">•</p>
            <p class="inline text-gray-400 text-sm">{{ feedItem.created_at }}</p>
          </div>
        </div>

        <!-- details -->
        <div v-if="feedItem.object" class="ml-6">
          <div class="px-3 py-2 border border-gray-300 rounded-lg">
            Dolores aliquam qui necessitatibus praesentium eum cum. Eos natus et nemo impedit reprehenderit. Voluptatem nulla commodi voluptatum commodi eos. Dignissimos sequi cumque voluptatibus voluptatem consectetur et.
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
import Note from '@/Pages/Vault/Dashboard/Partials/Feed/Note';
import FeedItem from '@/Pages/Vault/Dashboard/Partials/Feed/FeedItem';

export default {
  components: {
    Note,
    FeedItem,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
