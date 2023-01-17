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
  parameter_id: 0,
  date: null,
  hours: null,
  note: null,
});

const loadingState = ref(false);
const editLifeEvent = ref(false);
const lifeEventModalShown = ref(false);
const localLifeEvents = ref([]);
const religion = ref('');
const selectedLifeEventCategory = ref([]);

onMounted(() => {
  localLifeEvents.value = props.data.religions;
  selectedLifeEventCategory.value = props.data.life_event_categories[0];
  form.date = props.data.current_date;
});

const loadTypes = (category) => {
  var id = props.data.life_event_categories.findIndex((x) => x.id === category.id);
  selectedLifeEventCategory.value = props.data.life_event_categories[id];
};

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
        <!-- choose life event categories/types -->
        <div class="border-b border-gray-200 dark:border-gray-700">
          <div class="grid grid-skeleton grid-cols-2 gap-2 justify-center p-3">
            <!-- choose a life event type -->
            <div>
              <p class="text-xs font-semibold mb-1">Categories</p>
              <ul class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <li @click="loadTypes(category)" v-for="category in data.life_event_categories" :key="category.id" class="item-list flex border-b border-gray-200 px-3 py-1 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800 cursor-pointer">
                  <div class="flex justify-between w-full" :class="category.id === selectedLifeEventCategory.id ? 'font-bold' : ''">
                    <!-- label category -->
                    <div>{{ category.label }}</div>

                    <!-- arrow -->
                    <span v-if="category.id !== selectedLifeEventCategory.id">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-600">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
                      </svg>
                    </span>
                  </div>
                </li>
              </ul>
            </div>

            <!-- list of life event types -->
            <div>
              <p class="text-xs font-semibold mb-1">Types</p>
              <ul class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
                <li v-for="lifeEventType in selectedLifeEventCategory.life_event_types" :key="lifeEventType.id" class="item-list flex justify-between border-b border-gray-200 px-3 py-1 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800 cursor-pointer">
                  <span>{{ lifeEventType.label }}</span>
                  <span class="text-blue-500 hover:underline text-sm">{{ $t('app.choose') }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="border-b border-gray-200 dark:border-gray-700 p-3">
          <p class="mb-2 block text-sm dark:text-gray-100">Date of the event</p>
          <v-date-picker v-model="form.date" :timezone="'UTC'" class="inline-block h-full" :model-config="modelConfig">
            <template #default="{ inputValue, inputEvents }">
              <input
                class="rounded border bg-white px-2 py-1 dark:bg-gray-900"
                :value="inputValue"
                v-on="inputEvents" />
            </template>
          </v-date-picker>
        </div>

        <!-- options -->
        <div class="border-b border-gray-200 dark:border-gray-700 p-3">
          <span
            class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300 dark:text-gray-900"
            @click="displayMiddleNameField">
            {{ $t('vault.create_contact_add_middle_name') }}
          </span>
        </div>
        <div class="flex justify-between p-5">
            <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="createGenderModalShown = false" />
            <pretty-button :text="'Create gender'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
      </form>
    </div>
  </div>
</template>

<style lang="scss" scoped>
.icon-sidebar {
  top: -2px;
}

.icon-search {
  left: 8px;
  top: 8px;
}

.grid-skeleton {
  grid-template-columns: 1fr 2fr;
}

@media (max-width: 480px) {
  .grid-skeleton {
    grid-template-columns: 1fr;
  }
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
