<style lang="scss" scoped>
.icon-sidebar {
  top: -2px;
}

.icon-note {
  top: -1px;
}

.label-list {
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;

  li:last-child {
    border-bottom: 0;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }

  li:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}
</style>

<template>
  <div class="mb-4">
    <div class="mb-3 items-center justify-between border-b border-gray-200 sm:flex">
      <div class="mb-2 text-xs sm:mb-0">Job information</div>
      <span v-if="!editJobInformation" @click="showEditModal" class="relative cursor-pointer">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon-sidebar relative inline h-3 w-3 text-gray-300 hover:text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
      </span>

      <!-- close button -->
      <span v-if="editJobInformation" @click="editJobInformation = false" class="cursor-pointer text-xs text-gray-600">
        Close
      </span>
    </div>

    <!-- edit job information -->
    <div v-if="editJobInformation" class="mb-6 rounded-lg border border-gray-200 bg-form">
      <!-- filter list of existing companies -->
      <div class="border-b border-gray-200 p-2">
        <errors :errors="form.errors" />

        <!-- companies -->
        <dropdown
          v-model="form.company_id"
          :data="localCompanies"
          :required="false"
          :div-outer-class="'mb-2'"
          :placeholder="'Choose a value'"
          :dropdown-class="'block w-full'"
          :label="'Existing company'" />

        <p class="text-sm">Or create a new one</p>
      </div>

      <div class="border-b border-gray-200 p-2">
        <!-- job position -->
          <text-input
            v-model="form.job_position"
            :label="'Job position'"
            :type="'text'"
            :autofocus="true"
            :input-class="'block w-full'"
            :required="true"
            :autocomplete="false"
            :maxlength="255" />
      </div>

      <div class="flex justify-between p-5">
            <pretty-link @click="editJobInformation = false" :text="'Cancel'" :classes="'mr-3'" />
            <pretty-button
              :href="'data.url.vault.create'"
              :text="'Save'"
              :state="loadingState"
              :icon="'check'"
              :classes="'save'" />
          </div>

    </div>

    <!-- blank state -->
    <p v-if="!data.job_position && !data.company" class="text-sm text-gray-600">Not set</p>
  </div>
</template>

<script>
import TextInput from '@/Shared/Form/TextInput';
import Dropdown from '@/Shared/Form/Dropdown';
import PrettyLink from '@/Shared/Form/PrettyLink';
import PrettyButton from '@/Shared/Form/PrettyButton';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    TextInput,
    Errors,
    Dropdown,
    PrettyLink,
    PrettyButton,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      editJobInformation: false,
      localCompanies: [],
      localCompaniesInVault: [],
      form: {
        job_position: '',
        company_id: 0,
        errors: [],
      },
    };
  },

  created() {},

  computed: {
    filteredCompanies() {
      return this.localCompaniesInVault.filter((label) => {
        return label.name.toLowerCase().indexOf(this.form.search.toLowerCase()) > -1;
      });
    },
  },

  methods: {
    showEditModal() {
      this.form.name = '';
      this.editJobInformation = true;
      this.getCompanies();

      this.$nextTick(() => {
        this.$refs.label.focus();
      });
    },

    getCompanies() {
      if (this.localCompanies.length > 0) {
        return;
      }

      axios
        .get(this.data.url.index)
        .then((response) => {
          this.localCompanies.push(response.data.data);
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
    },

    store() {
      this.form.name = this.form.search;

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.form.search = '';
          this.localCompaniesInVault.push(response.data.data);
          this.localCompanies.push(response.data.data);
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
    },

    set(label) {
      if (label.taken) {
        this.remove(label);
        return;
      }

      axios
        .put(label.url.update)
        .then((response) => {
          this.localCompaniesInVault[this.localCompaniesInVault.findIndex((x) => x.id === label.id)] = response.data.data;
          this.localCompanies.push(response.data.data);
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
    },

    remove(label) {
      axios
        .delete(label.url.destroy)
        .then((response) => {
          this.localCompaniesInVault[this.localCompaniesInVault.findIndex((x) => x.id === label.id)] = response.data.data;

          var id = this.localCompanies.findIndex((x) => x.id === label.id);
          this.localCompanies.splice(id, 1);
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
    },
  },
};
</script>
