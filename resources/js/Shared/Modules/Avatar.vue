<template>
  <div>
    <div class="mx-auto mb-6 sm:w-1/2" v-html="data" />
  </div>
</template>

<script>
import PrettyButton from '@/Shared/Form/PrettyButton';

export default {
  components: {
    PrettyButton,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped></style>
