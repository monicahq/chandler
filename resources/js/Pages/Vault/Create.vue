<style lang="scss" scoped>
</style>

<template>
  <Layout title="Dashboard" :user="user">
    <main class="sm:mt-24 relative">
      <div class="max-w-lg mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <div class="bg-white border border-gray-200 rounded-lg mb-6 p-5">
          <div>
            <h1>Create a new vault</h1>
            <p>Vaults contain all your contacts data.</p>
          </div>
          <div>
            <BreezeLabel for="first_name" value="Vault name" />
            <BreezeInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name" required autofocus autocomplete="first_name" />
          </div>

          <h2 class="text-lg text-center mb-6">Thanks for trying out Monica 👋</h2>
          <p class="mb-3">Monica is there to help you build better relationships.</p>
          <p class="mb-3">Contacts in Monica are stored in vaults. You can have as many vaults as you want: one vault for your personal life, one for your professional life, and/or one vault shared with your spouse.</p>
          <div class="text-center">
            <pretty-link :href="data.url.vault.new" :text="'Create a vault'" :icon="'plus'" :classes="'mr-3'" />
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
import PrettyLink from '@/Shared/PrettyLink';

export default {
  components: {
    Layout,
    PrettyLink,
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
