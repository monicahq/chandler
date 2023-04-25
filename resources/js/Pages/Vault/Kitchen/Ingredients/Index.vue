<script setup>
import Layout from '@/Shared/Layout.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref, nextTick } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
});

const loadingState = ref(false);
const localIngredients = ref([]);
const nameField = ref(null);
const createIngredientModalShown = ref(false);
const editedIngredientId = ref(0);

onMounted(() => {
  localIngredients.value = props.data.ingredients;
});

const form = useForm({
  name: null,
});

const showAddModal = () => {
  form.name = null;
  createIngredientModalShown.value = true;

  nextTick(() => {
    nameField.value.focus();
  });
};

const showUpdateModal = (ingredient) => {
  form.name = ingredient.name;
  editedIngredientId.value = ingredient.id;

  nextTick(() => {
    nameField.value.focus();
  });
};

const store = () => {
  loadingState.value = 'loading';

  axios
    .post(props.data.url.store, form)
    .then((response) => {
      loadingState.value = '';
      createIngredientModalShown.value = false;
      localIngredients.value.push(response.data.data);
    })
    .catch(() => {
      loadingState.value = '';
    });
};

const update = (ingredient) => {
  loadingState.value = 'loading';

  axios
    .put(ingredient.url.update, form)
    .then((response) => {
      loadingState.value = '';
      editedIngredientId.value = 0;
      localIngredients.value[localIngredients.value.findIndex((x) => x.id === ingredient.id)] = response.data.data;
    })
    .catch(() => {
      loadingState.value = '';
    });
};

const destroy = (ingredient) => {
  if (confirm('Are you sure? This will delete the journal, and the entries, permanently.')) {
    axios.delete(ingredient.url.destroy).then(() => {
      var id = localIngredients.value.findIndex((x) => x.id === ingredient.id);
      localIngredients.value.splice(id, 1);
    });
  }
};
</script>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.journals" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_journal_index') }}
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
            <li class="inline">
              {{ data.name }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-18 relative">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:px-6 sm:py-6 lg:px-8">
        <!-- tabs -->
        <div class="flex justify-center">
          <div class="mb-8 inline-flex rounded-md shadow-sm">
            <inertia-link
              :href="data.url.show"
              class="inline-flex items-center rounded-l-lg border border-gray-200 bg-gray-100 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-gray-100 hover:text-blue-700 dark:border-gray-600 dark:bg-gray-400 dark:font-bold dark:text-white dark:hover:bg-gray-600 dark:hover:text-white dark:focus:text-white dark:focus:ring-blue-500">
              Meals
            </inertia-link>

            <inertia-link
              :href="data.url.photo_index"
              :class="{ 'bg-gray-100 text-blue-700 dark:bg-gray-400 dark:font-bold': defaultTab === 'life_events' }"
              class="inline-flex items-center rounded-r-md border-b border-r border-t border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:hover:text-white dark:focus:text-white dark:focus:ring-blue-500">
              Ingredients
            </inertia-link>
          </div>
        </div>

        <!-- title + cta -->
        <div class="mb-6 mt-8 items-center justify-between sm:mt-0 sm:flex">
          <h3 class="mb-4 sm:mb-0">
            <span class="mr-1"> 🥕 </span>
            All the ingredients
          </h3>
          <pretty-button
            v-if="!createIngredientModalShown"
            :text="'Add an ingredient'"
            :icon="'plus'"
            @click="showAddModal" />
        </div>

        <!-- modal to create a ingredient -->
        <form
          v-if="createIngredientModalShown"
          class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
          @submit.prevent="store()">
          <div class="border-b border-gray-200 p-5 dark:border-gray-700">
            <errors :errors="form.errors" />

            <text-input
              ref="nameField"
              v-model="form.name"
              :label="'Name'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createIngredientModalShown = false" />
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="createIngredientModalShown = false" />
            <pretty-button :text="'Create'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>

        <!-- list of ingredients -->
        <ul
          v-if="localIngredients.length > 0"
          class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
          <li
            v-for="ingredient in localIngredients"
            :key="ingredient.id"
            class="item-list border-b border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800">
            <!-- detail of the ingredient -->
            <div v-if="editedIngredientId != ingredient.id" class="flex items-center justify-between px-5 py-2">
              <span class="text-base">{{ ingredient.name }}</span>

              <!-- actions -->
              <ul class="text-sm">
                <li class="mr-4 inline" @click="showUpdateModal(ingredient)">
                  <span class="cursor-pointer text-blue-500 hover:underline">Rename</span>
                </li>
                <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(ingredient)">
                  Delete
                </li>
              </ul>
            </div>

            <!-- rename a ingredient modal -->
            <form
              v-if="editedIngredientId == ingredient.id"
              class="item-list border-b border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800"
              @submit.prevent="update(ingredient)">
              <div class="border-b border-gray-200 p-5 dark:border-gray-700">
                <errors :errors="form.errors" />

                <text-input
                  ref="nameField"
                  v-model="form.name"
                  :label="'Name'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="editedIngredientId = 0" />
              </div>

              <div class="flex justify-between p-5">
                <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click.prevent="editedIngredientId = 0" />
                <pretty-button :text="$t('app.rename')" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>

        <!-- blank state -->
        <div
          v-if="localIngredients.length == 0"
          class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
          <p class="p-5 text-center">
            Pet categories let you add types of pets that contacts can add to their profile.
          </p>
        </div>
      </div>
    </main>
  </layout>
</template>

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
