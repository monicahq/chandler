<style lang="scss" scoped>
.grid {
  grid-template-columns: 200px 1fr 200px;
}

@media (max-width: 480px) {
  .grid {
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
  <Layout title="Dashboard" :user="user">
    <main class="sm:mt-24 relative">
      <div class="max-w-md mx-auto py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="bg-white border border-gray-200 rounded-lg mb-6 p-5">
          <h2 class="text-lg text-center mb-6">Thanks for trying out Monica 👋</h2>
          <p class="mb-3">Monica is there to help you build better relationships.</p>
          <p class="mb-3">Contacts in Monica are stored in vaults. You can have as many vaults as you want: one vault for your personal life, one for your professional life, and/or one vault shared with your spouse.</p>
          <div class="text-center">
            <pretty-button :text="'Create a vault'" :icon="'plus'" :classes="'mr-3'" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          lsdfjsl
        </div>
      </div>
    </main>
  </Layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyButton from '@/Shared/PrettyButton';

export default {
  components: {
    Layout,
    PrettyButton,
  },

  props: {
    user: {
      type: Object,
      default: null,
    }
  },

  data() {
    return {
      addMode: false,
    };
  },

  methods: {
    showAddModal(type) {
      if (type == 'lifeEvent') {
        this.addMode = true;
      }
    },
  },
};
</script>
