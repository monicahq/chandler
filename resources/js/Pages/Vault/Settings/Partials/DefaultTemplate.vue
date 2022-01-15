<style lang="scss" scoped>
</style>

<template>
  <div>
    <!-- title + cta -->
    <div class="sm:flex items-center justify-between mb-3 sm:mt-0 mt-8">
      <h3 class="mb-4 sm:mb-0">
        <span class="mr-1">
          📐
        </span> Default template to display contacts
      </h3>
    </div>

    <!-- help text -->
    <div class="border rounded mb-6 text-sm">
      <div class="px-3 py-2 rounded-t flex bg-slate-50 border-gray-200 border-b mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="grow h-6 pr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <div>
          <p>A template defines how contacts should be displayed. You can have as many templates as you want - they are defined in your <inertia-link :href="''" class="text-sky-500 hover:text-blue-900">Account settings</inertia-link>. However, you might want to define a default template so all your contacts in this vault have this template by default.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  components: {
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      form: {
        errors: [],
      },
    };
  },

  methods: {
    destroy() {
      if(confirm('Are you sure? This will delete all the data inside this vault.')) {

        axios.delete(this.data.url.destroy)
          .then(response => {
            localStorage.success = 'The vault has been deleted';
            this.$inertia.visit(response.data.data);
          })
          .catch(error => {
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
