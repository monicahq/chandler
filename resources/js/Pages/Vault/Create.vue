<style lang="scss" scoped>
</style>

<template>
  <layout :user="user">
    <main class="sm:mt-24 relative">
      <div class="max-w-lg mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <form @submit.prevent="submit()" class="bg-white border border-gray-200 rounded-lg mb-6">
          <div class="p-5 border-b border-gray-200 bg-blue-50">
            <h1 class="text-center text-2xl mb-2">Create a new vault</h1>
            <p class="text-center">Vaults contain all your contacts data.</p>
          </div>
          <div class="p-5 border-b border-gray-200">
            <text-input v-model="form.name" :autofocus="true" :div-outer-class="'mb-5'" :input-class="'block w-full'" :required="true" :maxlength="255" :label="'Vault name'" />
            <text-area v-model="form.description" :label="'Description'" :maxlength="255" :textarea-class="'block w-full'" />
         </div>

          <div class="p-5 flex justify-between">
            <pretty-link :href="data.url.back" :text="'Cancel'" :classes="'mr-3'" />
            <pretty-button :href="'data.url.vault.new'" :text="'Create a vault'" :state="loadingState" :icon="'check'" :classes="'save'" />
          </div>
        </form>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyLink from '@/Shared/PrettyLink';
import PrettyButton from '@/Shared/PrettyButton';
import TextInput from '@/Shared/TextInput';
import TextArea from '@/Shared/TextArea';

export default {
  components: {
    Layout,
    PrettyLink,
    PrettyButton,
    TextInput,
    TextArea,
  },

  props: {
    user: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      addMode: false,
      loadingState: '',
      form: {
        name: '',
        description: '',
      },
    };
  },

  methods: {
    showAddModal(type) {
      if (type == 'lifeEvent') {
        this.addMode = true;
      }
    },

    submit() {
      this.loadingState = 'loading';

      axios.post(this.data.url.store, this.form)
        .then(response => {
          //localStorage.success = this.$t('employee.edit_information_success');
          this.$inertia.visit(response.data.data);
        })
        .catch(error => {
          this.loadingState = null;
          //this.form.errors = error.response.data;
        });
    },
  },
};
</script>
