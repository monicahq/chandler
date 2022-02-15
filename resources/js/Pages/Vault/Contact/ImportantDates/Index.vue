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

.ant-calendar-picker {
  -tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
  --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
  box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
  --tw-border-opacity: 1;
  border-color: rgb(209 213 219 / var(--tw-border-opacity));
  border-radius: 0.375rem;
  padding-top: 0.5rem;
  padding-right: 0.75rem;
  padding-bottom: 0.5rem;
  padding-left: 0.75rem;
  font-size: 1rem;
  line-height: 1.5rem;
  border-width: 1px;
  appearance: none;
background-color: #fff;
}
</style>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600">You are here:</li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.contacts" class="text-sky-500 hover:text-blue-900">
                Contacts
              </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.contact" class="text-sky-500 hover:text-blue-900">
                Profile of {{ data.contact.name }}
              </inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">All the important dates</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-6 mt-8 items-center justify-between sm:mt-0 sm:flex">
          <h3 class="mb-4 sm:mb-0"><span class="mr-1">🗓</span> All the important dates</h3>
          <pretty-button
            v-if="!createDateModalShown"
            :text="'Add a date'"
            :icon="'plus'"
            @click="showCreateModal" />
        </div>

        <!-- list of dates -->
        <ul v-if="localDates.length > 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <li
            v-for="date in localDates"
            :key="date.id"
            class="item-list border-b border-gray-200 hover:bg-slate-50">
            <!-- detail of the group type -->
            <div
              v-if="editDateModalShownId != date.id"
              class="flex items-center justify-between px-5 py-2">

              <span class="text-base">{{ date.label }}: <span>{{ date.date }}</span></span>

              <!-- actions -->
              <ul class="text-sm">
                <li
                  class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                  @click="updateAdressTypeModal(date)">
                  Rename
                </li>
                <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(date)">
                  Delete
                </li>
              </ul>
            </div>

            <!-- rename a addressType modal -->
            <form
              v-if="editDateModalShownId == date.id"
              class="item-list border-b border-gray-200 hover:bg-slate-50"
              @submit.prevent="update(addressType)">
              <div class="border-b border-gray-200 p-5">
                <errors :errors="form.errors" />

                <text-input
                  :ref="'rename' + date.id"
                  v-model="form.name"
                  :label="'Name'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="editDateModalShownId = 0" />
              </div>

              <div class="flex justify-between p-5">
                <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="editDateModalShownId = 0" />
                <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>

        <!-- modal to create a new date -->
        <form
          v-if="createDateModalShown"
          class="mb-6 rounded-lg border border-gray-200 bg-white"
          @submit.prevent="submit()">
          <div class="border-b border-gray-200">

            <div v-if="form.errors.length > 0" class="p-5"><errors :errors="form.errors" /></div>

            <div class="border-b border-gray-200 p-5">
              <text-input
              :ref="'name'"
              v-model="form.name"
              :label="'Name of the date'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createAddressTypeModalShown = false" />
            </div>

            <div class="border-b border-gray-200 p-5">
              <!-- case: I know the exact date -->
              <div class="mb-2 flex items-center">
                <input
                  id="exactDate"
                  v-model="form.choice"
                  value="exactDate"
                  name="name-order"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label
                  for="exactDate"
                  class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                  I know the exact date, including the year
                </label>
              </div>
              <div v-if="form.choice == 'exactDate'" class="ml-6 mb-4">
                <a-date-picker v-model:value="form.date" class="" />
              </div>

              <!-- case: I know the exact date -->
              <div class="mb-2 flex items-center">
                <input
                  id="monthDay"
                  v-model="form.choice"
                  value="monthDay"
                  name="name-order"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label
                  for="monthDay"
                  class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                  I only know the date and month, not the year
                </label>
              </div>
              <div v-if="form.choice == 'monthDay'" class="ml-6">
                <dropdown
              v-model="form.gender_id"
              :data="data.months"
              :required="false"
              :div-outer-class="'mb-5'"
              :placeholder="'Choose a value'"
              :dropdown-class="'block w-full'"
              :label="'Month'" />
              </div>

              <!-- case: I know the age -->
              <div class="mb-2 flex items-center">
                <input
                  id="age"
                  v-model="form.choice"
                  @selected="showAge"
                  value="age"
                  name="name-order"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label
                  for="age"
                  class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                  I think this person is… (years old)
                </label>
              </div>
              <div v-if="form.choice == 'age'" class="ml-6">
                <text-input
                  :ref="'age'"
                  v-model="form.name"
                  :type="'number'"
                  :min="0"
                  :max="120"
                  :autofocus="true"
                  :input-class="'block'"
                  :required="true"
                  :autocomplete="false" />
              </div>
            </div>
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createAddressTypeModalShown = false" />
            <pretty-button :text="'Create address type'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import NameOrder from '@/Pages/Settings/Preferences/Partials/NameOrder';
import DateFormat from '@/Pages/Settings/Preferences/Partials/DateFormat';
import PrettyButton from '@/Shared/Form/PrettyButton';
import TextInput from '@/Shared/Form/TextInput';
import Dropdown from '@/Shared/Form/Dropdown';

export default {
  components: {
    Layout,
    NameOrder,
    DateFormat,
    PrettyButton,
    Dropdown,
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
      loadingState: '',
      editDateModalShownId: 0,
      createDateModalShown: false,
      localDates: [],
      form: {
        choice: '',
        label: '',
        date: '',
        age: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localDates = this.data.dates;
  },

  methods: {
    showCreateModal() {
      this.form.name = '';
      this.createDateModalShown = true;

      this.$nextTick(() => {
        this.$refs.name.focus();
      });
    },

    showAge() {
      this.$nextTick(() => {
        this.$refs.age.focus();
      });
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.address_type_store, this.form)
        .then((response) => {
          this.flash('The address type has been created', 'success');
          this.localAddressTypes.unshift(response.data.data);
          this.loadingState = null;
          this.createAddressTypeModalShown = false;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    update(addressType) {
      this.loadingState = 'loading';

      axios
        .put(addressType.url.update, this.form)
        .then((response) => {
          this.flash('The address type has been updated', 'success');
          this.localAddressTypes[this.localAddressTypes.findIndex((x) => x.id === addressType.id)] = response.data.data;
          this.loadingState = null;
          this.renameAddressTypeModalShownId = 0;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroy(addressType) {
      if (
        confirm(
          "Are you sure? This will remove the address types from all contacts, but won't delete the contacts themselves.",
        )
      ) {
        axios
          .delete(addressType.url.destroy)
          .then((response) => {
            this.flash('The address type has been deleted', 'success');
            var id = this.localAddressTypes.findIndex((x) => x.id === addressType.id);
            this.localAddressTypes.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
