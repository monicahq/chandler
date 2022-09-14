<template>
  <div class="relative inline" :class="divOuterClass" :style="'top: ' + top">
    <a-popover placement="bottomLeft">
      <template #content>
        <div class="flex">
          <avatar :data="contact.avatar" :classes="'h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900'" />
          <div>
            <p>Last talked to: Apr 20, 2012</p>
            <p>Last activity: Apr 20, 2012</p>
          </div>
        </div>
      </template>
      <template v-if="showName" #title>
        <span>FOU</span>
      </template>
      <div class="inline-flex items-center text-sm">
        <avatar :data="contact.avatar" :classes="'h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900'" />
        <a v-if="showName" class="colored-link" href="">FOU</a>
      </div>
    </a-popover>
  </div>
</template>

<script>
import Avatar from '@/Shared/Avatar.vue';

export default {
  components: {
    Avatar,
  },

  props: {
    top: {
      type: String,
      default: '0px',
    },
    contact: {
      type: Object,
      default: null,
    },
    previewContactSize: {
      type: Number,
      default: 18,
    },
    showName: {
      type: Boolean,
      default: true,
    },
    divOuterClass: {
      type: String,
      default: '',
    },
  },
};
</script>

<style lang="scss" scoped></style>
