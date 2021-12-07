<style lang="scss" scoped>

</style>

<template>
  <Layout title="Dashboard" :inside-vault="true" :user="user">
    <main class="sm:mt-24 relative">
      d
    </main>
  </Layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import { Link } from '@inertiajs/inertia-vue3';
import PrettyLink from '@/Shared/PrettyLink';

export default {
  components: {
    Layout,
    Link,
    PrettyLink,
  },

  props: {
    user: {
      type: Object,
      default: null,
    },
    data: {
      type: Array,
      default: [],
    },
  },

  data() {
    return {
      addMode: false,
    };
  },

  methods: {
  },
};
</script>
