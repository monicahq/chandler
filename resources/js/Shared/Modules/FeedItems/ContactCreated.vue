<template>
  <div class="flex rounded-lg border border-gray-300 px-3 py-2">
    <avatar :data="data.avatar" :classes="'rounded-full border-gray-200 border relative h-5 w-5'" />

    <span>data.name</span>
  </div>
</template>

<script>
import Avatar from '@/Shared/Avatar.vue';

export default {
  components: {
    Avatar,
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
