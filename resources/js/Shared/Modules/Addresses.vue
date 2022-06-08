<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}
</style>

<template>
  <div class="mb-10">
    <!-- title + cta -->
    <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 sm:flex">
      <div class="mb-2 sm:mb-0">
        <span class="relative mr-1">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="icon-sidebar relative inline h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </span>

        <span class="font-semibold">Addresses</span>
      </div>
      <pretty-button
        :text="'Add an address'"
        :icon="'plus'"
        :classes="'sm:w-fit w-full'"
        @click="showCreateAddressModal" />
    </div>

    <div>
      <!-- add an address modal -->
      <form
        v-if="createAddressModalShown"
        class="bg-form mb-6 rounded-lg border border-gray-200"
        @submit.prevent="submit()">
        <div class="border-b border-gray-200">
          <!-- loan options -->
          <div class="border-b border-gray-200 px-5 pt-5 pb-3">
            <ul class="">
              <li class="mr-5 inline-block">
                <div class="flex items-center">
                  <input
                    id="object"
                    v-model="form.type"
                    value="object"
                    name="name-order"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500" />
                  <label for="object" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                    The loan is an object
                  </label>
                </div>
              </li>

              <li class="mr-5 inline-block">
                <div class="flex items-center">
                  <input
                    id="monetary"
                    v-model="form.type"
                    value="monetary"
                    name="name-order"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500" />
                  <label for="monetary" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                    The loan is monetary
                  </label>
                </div>
              </li>
            </ul>
          </div>

          <!-- name -->
          <div class="border-b border-gray-200 p-5">
            <text-input
              :ref="'name'"
              v-model="form.name"
              :label="'What is the loan?'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createAddressModalShown = false" />
          </div>

          <!-- amount + currency -->
          <div v-if="form.type === 'monetary'" class="flex border-b border-gray-200 p-5">
            <text-input
              :ref="'label'"
              v-model="form.amount_lent"
              :label="'How much money was lent?'"
              :help="'Write the amount with a dot if you need decimals, like 100.50'"
              :type="'number'"
              :autofocus="true"
              :input-class="'w-full'"
              :required="false"
              :min="0"
              :max="10000000"
              :autocomplete="false"
              @esc-key-pressed="createAddressModalShown = false" />

            <dropdown
              v-model="form.currency_id"
              :data="localCurrencies"
              :required="false"
              :div-outer-class="'ml-3 mb-5'"
              :placeholder="'Choose a value'"
              :dropdown-class="'block'"
              :label="'Currency'" />
          </div>

          <!-- loaned at -->
          <div class="border-b border-gray-200 p-5">
            <p class="mb-2 block text-sm">When was the loan made?</p>

            <v-date-picker class="inline-block h-full" v-model="form.loaned_at" :model-config="modelConfig">
              <template v-slot="{ inputValue, inputEvents }">
                <input class="rounded border bg-white px-2 py-1" :value="inputValue" v-on="inputEvents" />
              </template>
            </v-date-picker>
          </div>
        </div>

        <div v-if="form.errors.length > 0" class="p-5">
          <errors :errors="form.errors" />
        </div>

        <div class="flex justify-between p-5">
          <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createAddressModalShown = false" />
          <pretty-button :text="'Add loan'" :state="loadingState" :icon="'plus'" :classes="'save'" />
        </div>
      </form>

      <!-- list of addresses -->
      <div v-for="address in localAddresses" :key="address.id" class="mb-5 flex">
        <div v-if="editedAddressId != address.id" class="mr-3 flex items-center">
          <div class="flex -space-x-2 overflow-hidden">
            <div v-for="loaner in address.loaners" :key="loaner.id">
              <small-contact
                :div-outer-class="'inline-block rounded-full ring-2 ring-white'"
                :show-name="false"
                :preview-contact-size="30" />
            </div>
          </div>

          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>

          <div v-for="loanee in address.loanees" :key="loanee.id">
            <small-contact
              :div-outer-class="'inline-block rounded-full ring-2 ring-white'"
              :show-name="false"
              :preview-contact-size="30" />
          </div>
        </div>

        <div
          v-if="editedAddressId != address.id"
          class="item-list w-full rounded-lg border border-gray-200 bg-white hover:bg-slate-50">
          <div class="border-b border-gray-200 px-3 py-2">
            <div class="flex items-center justify-between">
              <div>
                <span class="mr-2 block">
                  <span v-if="address.amount_lent" class="mr-2">
                    <span v-if="address.currency_name" class="mr-1 text-gray-500">
                      {{ address.currency_name }}
                    </span>
                    {{ address.amount_lent }}
                    <span class="ml-2">•</span>
                  </span>
                  {{ address.name }}
                </span>
                <span v-if="address.description">{{ address.description }}</span>
              </div>
              <span v-if="address.loaned_at_human_format" class="mr-2 text-sm text-gray-500">{{
                address.loaned_at_human_format
              }}</span>
            </div>
          </div>

          <!-- actions -->
          <div class="flex items-center justify-between px-3 py-2">
            <!-- <small-contact /> -->
            <ul class="text-sm">
              <!-- settle -->
              <li
                v-if="!address.settled"
                @click="toggle(loan)"
                class="mr-4 inline cursor-pointer text-blue-500 hover:underline">
                Settle
              </li>
              <li v-else @click="toggle(loan)" class="mr-4 inline cursor-pointer text-blue-500 hover:underline">
                Revert
              </li>

              <!-- edit -->
              <li @click="showEditLoanModal(loan)" class="mr-4 inline cursor-pointer text-blue-500 hover:underline">
                Edit
              </li>

              <!-- delete -->
              <li @click="destroy(loan)" class="inline cursor-pointer text-red-500 hover:text-red-900">Delete</li>
            </ul>
          </div>
        </div>

        <!-- edit address modal -->
        <form
          v-if="editedAddressId === address.id"
          class="bg-form mb-6 w-full rounded-lg border border-gray-200"
          @submit.prevent="update(loan)">
          <div class="border-b border-gray-200">
            <!-- loan options -->
            <div class="border-b border-gray-200 px-5 pt-5 pb-3">
              <ul class="">
                <li class="mr-5 inline-block">
                  <div class="flex items-center">
                    <input
                      id="object"
                      v-model="form.type"
                      value="object"
                      name="name-order"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500" />
                    <label for="object" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                      The loan is an object
                    </label>
                  </div>
                </li>

                <li class="mr-5 inline-block">
                  <div class="flex items-center">
                    <input
                      id="monetary"
                      v-model="form.type"
                      value="monetary"
                      name="name-order"
                      type="radio"
                      class="h-4 w-4 border-gray-300 text-sky-500" />
                    <label for="monetary" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                      The loan is monetary
                    </label>
                  </div>
                </li>
              </ul>
            </div>

            <!-- name -->
            <div class="border-b border-gray-200 p-5">
              <text-input
                :ref="'name'"
                v-model="form.name"
                :label="'What is the loan?'"
                :type="'text'"
                :autofocus="true"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255"
                @esc-key-pressed="createAddressModalShown = false" />
            </div>

            <!-- amount + currency -->
            <div v-if="form.type === 'monetary'" class="flex border-b border-gray-200 p-5">
              <text-input
                :ref="'label'"
                v-model="form.amount_lent"
                :label="'How much money was lent?'"
                :help="'Write the amount with a dot if you need decimals, like 100.50'"
                :type="'number'"
                :autofocus="true"
                :input-class="'w-full'"
                :required="false"
                :min="0"
                :max="10000000"
                :autocomplete="false"
                @esc-key-pressed="createAddressModalShown = false" />

              <dropdown
                v-model="form.currency_id"
                :data="localCurrencies"
                :required="false"
                :div-outer-class="'ml-3 mb-5'"
                :placeholder="'Choose a value'"
                :dropdown-class="'block'"
                :label="'Currency'" />
            </div>

            <!-- loaned at -->
            <div class="border-b border-gray-200 p-5">
              <p class="mb-2 block text-sm">When was the loan made?</p>

              <v-date-picker class="inline-block h-full" v-model="form.loaned_at" :model-config="modelConfig">
                <template v-slot="{ inputValue, inputEvents }">
                  <input class="rounded border bg-white px-2 py-1" :value="inputValue" v-on="inputEvents" />
                </template>
              </v-date-picker>
            </div>

            <!-- loaned by or to -->
            <div class="flex items-center items-stretch border-b border-gray-200">
              <contact-selector
                :search-url="layoutData.vault.url.search_contacts_only"
                :most-consulted-contacts-url="layoutData.vault.url.get_most_consulted_contacts"
                :display-most-consulted-contacts="false"
                :label="'Who makes the loan?'"
                :add-multiple-contacts="true"
                :required="true"
                :div-outer-class="'p-5 flex-1 border-r border-gray-200'"
                v-model="form.loaners" />

              <contact-selector
                :search-url="layoutData.vault.url.search_contacts_only"
                :most-consulted-contacts-url="layoutData.vault.url.get_most_consulted_contacts"
                :display-most-consulted-contacts="true"
                :label="'Who the loan is for?'"
                :add-multiple-contacts="true"
                :required="true"
                :div-outer-class="'p-5 flex-1'"
                v-model="form.loanees" />
            </div>

            <!-- description -->
            <div class="p-5">
              <text-area
                v-model="form.description"
                :label="'Description'"
                :maxlength="255"
                :textarea-class="'block w-full'" />
            </div>
          </div>

          <div v-if="form.errors.length > 0" class="p-5">
            <errors :errors="form.errors" />
          </div>

          <div v-if="warning != ''" class="border-b p-3">⚠️ {{ warning }}</div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="editedAddressId = 0" />
            <pretty-button :text="'Save'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>
      </div>
    </div>

    <!-- blank state -->
    <div v-if="localAddresses.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no addresses yet.</p>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import TextArea from '@/Shared/Form/TextArea';
import Errors from '@/Shared/Form/Errors';
import SmallContact from '@/Shared/SmallContact';
import Dropdown from '@/Shared/Form/Dropdown';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
    TextInput,
    TextArea,
    Errors,
    SmallContact,
    Dropdown,
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
      createAddressModalShown: false,
      localAddresses: [],
      localCurrencies: [],
      editedAddressId: 0,
      warning: '',
      form: {
        type: '',
        name: '',
        description: '',
        loaned_at: null,
        amount_lent: '',
        currency_id: '',
        loaners: [],
        loanees: [],
        errors: [],
      },
      modelConfig: {
        type: 'string',
        mask: 'YYYY-MM-DD',
      },
    };
  },

  created() {
    this.localAddresses = this.data.addresses;
    this.form.loaned_at = this.data.current_date;
  },

  methods: {
    showCreateAddressModal() {
      this.getCurrencies();
      this.form.errors = [];

      this.form.name = '';
      this.form.description = '';
      this.form.amount_lent = '';
      this.form.currency_id = '';
      this.createAddressModalShown = true;

      this.$nextTick(() => {
        this.$refs.name.focus();
      });
    },

    showEditLoanModal(loan) {
      this.getCurrencies();
      this.form.errors = [];
      this.form.type = address.amount_lent ? 'monetary' : 'object';
      this.form.name = address.name;
      this.form.description = address.description;
      this.form.loaned_at = address.loaned_at;
      this.form.amount_lent = address.amount_lent_int;
      this.form.currency_id = address.currency_id;
      this.form.loaners = address.loaners;
      this.form.loanees = address.loanees;
      this.editedAddressId = address.id;
    },

    getCurrencies() {
      if (this.localCurrencies.length == 0) {
        axios
          .get(this.data.url.currencies, this.form)
          .then((response) => {
            this.localCurrencies = response.data.data;
          })
          .catch((error) => {});
      }
    },

    submit() {
      if (this.form.loaners.length == 0 || this.form.loanees.length == 0) {
        this.warning = 'Please indicate the contacts.';
        return;
      }

      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The loan has been created', 'success');
          this.localAddresses.unshift(response.data.data);
          this.loadingState = '';
          this.createAddressModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(loan) {
      this.loadingState = 'loading';

      axios
        .put(address.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The loan has been edited', 'success');
          this.localAddresses[this.localAddresses.findIndex((x) => x.id === address.id)] = response.data.data;
          this.editedAddressId = 0;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    destroy(loan) {
      if (confirm('Are you sure? This will delete the loan permanently.')) {
        axios
          .delete(address.url.destroy)
          .then((response) => {
            this.flash('The loan has been deleted', 'success');
            var id = this.localAddresses.findIndex((x) => x.id === address.id);
            this.localAddresses.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },

    toggle(loan) {
      axios
        .put(address.url.toggle, this.form)
        .then((response) => {
          this.flash('The loan has been settled', 'success');
          this.localAddresses[this.localAddresses.findIndex((x) => x.id === address.id)] = response.data.data;
        })
        .catch((error) => {
          this.form.errors = error.response.data;
        });
    },
  },
};
</script>
