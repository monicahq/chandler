<style lang="scss" scoped>
</style>

<template>
  <div v-html="data">
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
