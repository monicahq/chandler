<script setup>
import Errors from '@/Shared/Form/Errors.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import Dropdown from '@/Shared/Form/Dropdown.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
  data: Object,
});

const form = useForm({
  religion_id: '',
});

const loadingState = ref(false);
const editLifeEvent = ref(false);
const lifeEventModalShown = ref(false);
const localLifeEvents = ref([]);
const religion = ref('');

onMounted(() => {
  form.religion_id = props.data.religion ? props.data.religion.id : null;
  religion.value = props.data.religion ? props.data.religion.name : null;
  localLifeEvents.value = props.data.religions;
});

const update = () => {
  loadingState.value = 'loading';

  axios
    .put(props.data.url.update, form)
    .then((response) => {
      editLifeEvent.value = false;
      loadingState.value = '';
      religion.value = response.data.data.religion.name;
    })
    .catch(() => {
      loadingState.value = '';
    });
};

const showCreateLifeEventModal = () => {
  lifeEventModalShown.value = true;
};
</script>

<template>
  <div class="mb-10">
    <!-- title + cta -->
    <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 dark:border-gray-700 sm:flex">
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
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-sidebar relative inline h-4 w-4">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1A3.75 3.75 0 0012 18z" />
          </svg>

        </span>

        <span class="font-semibold"> Life events </span>
      </div>
      <pretty-button :text="'Add a life event'" :icon="'plus'" :classes="'sm:w-fit w-full'" @click="showCreateLifeEventModal" />
    </div>

    <div>
      <!-- add a life event -->
      <form
        class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900"
        @submit.prevent="submit()">
        <div class="border-b border-gray-200 dark:border-gray-700">

          <!-- choose life event categories -->
          <div class="border-b border-gray-200 px-5 pt-5 pb-3 dark:border-gray-700">
            <ul class="">
              <li class="mr-5 inline-block">
                <div class="flex items-center">
                  <input
                    id="object"
                    v-model="form.type"
                    value="object"
                    name="name-order"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                  <label
                    for="object"
                    class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
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
                    class="h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />
                  <label
                    for="monetary"
                    class="ml-3 block cursor-pointer text-sm font-medium text-gray-700 dark:text-gray-300">
                    The loan is monetary
                  </label>
                </div>
              </li>
            </ul>
          </div>

          <!-- name -->
          <div class="border-b border-gray-200 p-5 dark:border-gray-700">
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
              @esc-key-pressed="createLoanModalShown = false" />
          </div>

          <!-- amount + currency -->
          <div v-if="form.type === 'monetary'" class="flex border-b border-gray-200 p-5 dark:border-gray-700">
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
              @esc-key-pressed="createLoanModalShown = false" />

            <dropdown
              v-model="form.currency_id"
              :data="localCurrencies"
              :required="false"
              :div-outer-class="'ml-3 mb-5'"
              :placeholder="$t('app.choose_value')"
              :dropdown-class="'block'"
              :label="'Currency'" />
          </div>

          <!-- loaned at -->
          <div class="border-b border-gray-200 p-5 dark:border-gray-700">
            <p class="mb-2 block text-sm">When was the loan made?</p>

            <v-date-picker v-model="form.loaned_at" class="inline-block h-full" :model-config="modelConfig">
              <template #default="{ inputValue, inputEvents }">
                <input
                  class="rounded border bg-white px-2 py-1 dark:bg-gray-900"
                  :value="inputValue"
                  v-on="inputEvents" />
              </template>
            </v-date-picker>
          </div>

          <!-- loaned by or to -->
          <div class="flex items-center items-stretch border-b border-gray-200 dark:border-gray-700">
            <contact-selector
              v-model="form.loaners"
              :search-url="layoutData.vault.url.search_contacts_only"
              :most-consulted-contacts-url="layoutData.vault.url.get_most_consulted_contacts"
              :display-most-consulted-contacts="false"
              :label="'Who makes the loan?'"
              :add-multiple-contacts="true"
              :required="true"
              :div-outer-class="'p-5 flex-1 border-r border-gray-200 dark:border-gray-700'" />

            <contact-selector
              v-model="form.loanees"
              :search-url="layoutData.vault.url.search_contacts_only"
              :most-consulted-contacts-url="layoutData.vault.url.get_most_consulted_contacts"
              :display-most-consulted-contacts="true"
              :label="'Who the loan is for?'"
              :add-multiple-contacts="true"
              :required="true"
              :div-outer-class="'p-5 flex-1'" />
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
          <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="createLoanModalShown = false" />
          <pretty-button :text="'Add loan'" :state="loadingState" :icon="'plus'" :classes="'save'" />
        </div>
      </form>

      <!-- list of loans -->
      <div v-for="loan in localLifeEvents" :key="loan.id" class="mb-5 flex">
        <div v-if="editedLoanId != loan.id" class="mr-3 flex items-center">
          <div class="flex -space-x-2 overflow-hidden">
            <div v-for="loaner in loan.loaners" :key="loaner.id">
              <contact-card :contact="loaner" :avatarClasses="'h-7 w-7 rounded-full mr-2'" :displayName="false" />
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

          <div v-for="loanee in loan.loanees" :key="loanee.id">
            <contact-card :contact="loanee" :avatarClasses="'h-7 w-7 rounded-full mr-2'" :displayName="false" />
          </div>
        </div>

        <div
          v-if="editedLoanId != loan.id"
          class="item-list w-full rounded-lg border border-gray-200 bg-white hover:bg-slate-50 dark:border-gray-700 dark:bg-gray-900 hover:dark:bg-slate-800">
          <div class="border-b border-gray-200 px-3 py-2 dark:border-gray-700">
            <div class="flex items-center justify-between">
              <div>
                <span class="mr-2 block">
                  <span v-if="loan.amount_lent" class="mr-2">
                    <span v-if="loan.currency_name" class="mr-1 text-gray-500">
                      {{ loan.currency_name }}
                    </span>
                    {{ loan.amount_lent }}
                    <span class="ml-2"> • </span>
                  </span>
                  {{ loan.name }}
                </span>
                <span v-if="loan.description">
                  {{ loan.description }}
                </span>
              </div>
              <span v-if="loan.loaned_at_human_format" class="mr-2 text-sm text-gray-500">{{
                loan.loaned_at_human_format
              }}</span>
            </div>
          </div>

          <!-- actions -->
          <div class="flex items-center justify-between px-3 py-2">
            <ul class="text-sm">
              <!-- settle -->
              <li
                v-if="!loan.settled"
                class="mr-4 inline cursor-pointer text-blue-500 hover:underline"
                @click="toggle(loan)">
                Settle
              </li>
              <li v-else class="mr-4 inline cursor-pointer text-blue-500 hover:underline" @click="toggle(loan)">
                Revert
              </li>

              <!-- edit -->
              <li class="mr-4 inline cursor-pointer text-blue-500 hover:underline" @click="showEditLoanModal(loan)">
                Edit
              </li>

              <!-- delete -->
              <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(loan)">
                {{ $t('app.delete') }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- blank state -->
    <div
      v-if="localLifeEvents.length == 0"
      class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
      <img src="/img/contact_blank_loan.svg" :alt="$t('Loans')" class="mx-auto mt-4 h-14 w-14" />
      <p class="px-5 pb-5 pt-2 text-center">There are no loans yet.</p>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.icon-sidebar {
  top: -2px;
}
</style>
