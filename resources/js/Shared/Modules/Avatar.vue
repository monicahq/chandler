<template>
  <div>
    <div v-if="data.type === 'generated'" class="mx-auto mb-6 sm:w-1/2" v-html="data.content" />
    <img v-else class="mx-auto mb-6 rounded-full ring-2 ring-gray-200 sm:w-1/2" :src="data.content" />
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

<style lang="scss" scoped></style>
