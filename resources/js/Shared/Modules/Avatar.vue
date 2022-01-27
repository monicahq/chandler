<style lang="scss" scoped>
</style>

<template>
  <div v-html="data" class="mb-6" />
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
