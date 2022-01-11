<style lang="scss" scoped>
.item-list {
  &:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}
</style>

<template>
  <div>
    <div class="border-b mb-4 pb-3 sm:flex items-center justify-between sm:mt-0 mt-8">
      <h3>
        Modules in this page
      </h3>
      <pretty-button v-if="!createModuleModalShown" :text="'Add a module'" :icon="'plus'" @click="showModuleModal" />
    </div>

    <!-- list of all the existing modules -->
    <ul class="bg-white border border-gray-200 rounded-lg mb-6">
      <li class="border-b border-gray-200 hover:bg-slate-50 item-list flex items-center pl-2 pr-5 py-2 justify-between">
        <span>Uber</span>
        <span class="cursor-pointer inline text-sky-500 hover:text-blue-900">Add</span>
      </li>
      <li class="border-b border-gray-200 hover:bg-slate-50 item-list flex items-center pl-2 pr-5 py-2 justify-between">
        <span>Notes</span>
        <span class="cursor-pointer inline text-sky-500 hover:text-blue-900">Add</span>
      </li>
    </ul>

    <!-- list of modules -->
    <ul class="bg-white border border-gray-200 rounded-lg mb-6">
      <li v-for="module in localModules" :key="module.id" class="border-b border-gray-200 hover:bg-slate-50 item-list flex items-center pl-2 pr-5 py-2">
        <!-- anchor to move module -->
        <div class="mr-2">
          <svg class="cursor-move handle" width="24" height="24" viewBox="0 0 24 24" fill="none"
               xmlns="http://www.w3.org/2000/svg"
          >
            <path d="M7 7H9V9H7V7Z" fill="currentColor" />
            <path d="M11 7H13V9H11V7Z" fill="currentColor" />
            <path d="M17 7H15V9H17V7Z" fill="currentColor" />
            <path d="M7 11H9V13H7V11Z" fill="currentColor" />
            <path d="M13 11H11V13H13V11Z" fill="currentColor" />
            <path d="M15 11H17V13H15V11Z" fill="currentColor" />
            <path d="M9 15H7V17H9V15Z" fill="currentColor" />
            <path d="M11 15H13V17H11V15Z" fill="currentColor" />
            <path d="M17 15H15V17H17V15Z" fill="currentColor" />
          </svg>
        </div>

        <!-- detail of the module -->
        <div class="flex justify-between items-center w-full">
          <span>{{ module.name }}</span>

          <!-- actions -->
          <ul class="text-sm">
            <li class="cursor-pointer inline text-red-500 hover:text-red-900">Remove</li>
          </ul>
        </div>
      </li>
    </ul>

    <!-- blank state -->
    <div>
      <p class="p-5 text-center bg-white border border-gray-200 rounded-lg">Create at least one page to display contact's data.</p>
    </div>
  </div>
</template>

<script>
import PrettyButton from '@/Shared/PrettyButton';

export default {
  components: {
    PrettyButton,
  },

  props: {
    data: {
      type: Array,
      default: () => [],
    },
  },

  data() {
    return {
      loadingState: '',
      showModuleList: false,
      allModules: [],
      localModules: [],
      drag: false,
      form: {
        position: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localModules = this.data;
  },

  methods: {
    showModuleModal() {
      this.form.name = '';
      this.createModuleModalShown = true;

      this.$nextTick(() => {
        this.$refs.newPage.focus();
      });
    },

    updatePosition(event) {
      // the event object comes from the draggable component
      this.form.position = event.moved.newIndex + 1;

      axios.post(event.moved.element.url.order, this.form)
        .then(response => {
          this.flash('The order has been saved', 'success');
        })
        .catch(error => {
          this.loadingState = null;
          this.errors = error.response.data;
        });
    },
  },
};
</script>
