<style lang="scss" scoped>
.vault-list {
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.vault-detail {
  height: 250px;
  grid-template-columns: 1fr;
  grid-template-rows: auto 1fr auto;
}

@media (max-width: 480px) {
  .vault-list {
    grid-template-columns: 1fr;
  }
}

.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

input[type=checkbox] {
  top: 3px;
  width: 12px;
}
</style>

<template>
  <Layout title="Dashboard" :layoutData="layoutData">
    <main class="sm:mt-24 relative">
      <div class="max-w-md mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 rounded-lg mb-6 p-5">
          <h2 class="text-lg text-center mb-6">Settings</h2>
          <ul>
            <li><span class="mr-1">🥸</span> Manage users</li>
            <li><span class="mr-1">🎃</span> Personalize your contacts data</li>
            <li><span class="mr-1">💩</span> Cancel your account</li>
          </ul>
        </div>
      </div>
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
    layoutData: {
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
