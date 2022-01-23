<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

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
  <layout :layout-data="layoutData" :inside-vault="true">
    <main class="sm:mt-24 relative">
      <div class="max-w-4xl mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <form class="mb-8 bg-white border border-gray-200 rounded-lg" @submit.prevent="submit">
          <div class="p-5 border-b border-gray-200 bg-blue-50 section-head">
            <h1 class="text-center text-2xl font-medium">
              Search something
            </h1>
          </div>
          <div class="p-5">
            <text-input ref="searchField"
                        v-model="form.searchTerm"
                        :type="'text'" :autofocus="true"
                        :input-class="'block w-full'"
                        :placeholder="'Type something'"
                        :required="true"
                        :autocomplete="false"
                        :maxlength="255"
                        @keyup="search"
            />
          </div>
        </form>

        <div v-if="Object.keys(results).length !== 0">
          <!-- contacts -->
          <div class="sm:mb-1 mb-2">
            <span class="relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon-sidebar h-4 w-4 inline relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </span>

            Contacts
          </div>
          <ul v-if="results.contacts.length > 0" class="bg-white border border-gray-200 rounded-lg mb-6">
            <li v-for="contact in results.contacts" :key="contact.id" class="p-3 border-b border-gray-200 hover:bg-slate-50 item-list">
              <inertia-link :href="contact.url" class="text-sky-500 hover:text-blue-900">{{ contact.name }}</inertia-link>
            </li>
          </ul>

          <!-- notes -->
          <div class="sm:mb-1 mb-2">
            <span class="relative">
              <svg class="icon-sidebar h-4 w-4 inline relative" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6 6C6 5.44772 6.44772 5 7 5H17C17.5523 5 18 5.44772 18 6C18 6.55228 17.5523 7 17 7H7C6.44771 7 6 6.55228 6 6Z" fill="currentColor" /><path d="M6 10C6 9.44771 6.44772 9 7 9H17C17.5523 9 18 9.44771 18 10C18 10.5523 17.5523 11 17 11H7C6.44771 11 6 10.5523 6 10Z" fill="currentColor" /><path d="M7 13C6.44772 13 6 13.4477 6 14C6 14.5523 6.44771 15 7 15H17C17.5523 15 18 14.5523 18 14C18 13.4477 17.5523 13 17 13H7Z" fill="currentColor" /><path d="M6 18C6 17.4477 6.44772 17 7 17H11C11.5523 17 12 17.4477 12 18C12 18.5523 11.5523 19 11 19H7C6.44772 19 6 18.5523 6 18Z" fill="currentColor" /><path fill-rule="evenodd" clip-rule="evenodd" d="M2 4C2 2.34315 3.34315 1 5 1H19C20.6569 1 22 2.34315 22 4V20C22 21.6569 20.6569 23 19 23H5C3.34315 23 2 21.6569 2 20V4ZM5 3H19C19.5523 3 20 3.44771 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44771 3 5 3Z" fill="currentColor" />
              </svg>
            </span>

            Notes
          </div>
          <ul v-if="results.notes.length > 0">
            <li v-for="note in results.notes" :key="note.id">
              {{ note.body }}
            </li>
          </ul>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import TextInput from '@/Shared/Form/TextInput';

export default {
  components: {
    Layout,
    TextInput,
  },

  props: {
    layoutData: {
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
      processingSearch: false,
      form: {
        searchTerm: '',
        errors: [],
      },
      results: [],
    };
  },

  mounted() {
    this.$nextTick(() => {
      this.$refs.searchField.focus();
    });
  },

  methods: {
    search: _.debounce(

      function () {
        if (this.form.searchTerm != '') {
          this.processingSearch = true;

          axios.post(this.data.url.search, this.form)
            .then(response => {
              this.results = response.data.data;
              console.log(this.results.contacts.length);
              this.processingSearch = false;
            })
            .catch(error => {
              this.form.errors = error.response.data;
              this.processingSearch = false;
            });
        } else {
          this.results = [];
        }
      }, 300),
  },
};
</script>
