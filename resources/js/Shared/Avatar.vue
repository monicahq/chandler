<template>
  <div>
    <div v-if="data.type === 'svg'" :class="classes" v-html="data.content" />
    <img v-else :class="classes" :src="data.content" />
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default: null,
    },
    classes: {
      type: String,
      default: null,
    },
  },
};
</script>
