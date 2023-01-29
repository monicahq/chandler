<script setup>
import Loading from '@/Shared/Loading.vue';
import Errors from '@/Shared/Form/Errors.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import Dropdown from '@/Shared/Form/Dropdown.vue';
import ContactSelector from '@/Shared/Form/ContactSelector.vue';
import Avatar from '@/Shared/Avatar.vue';
import ContactCard from '@/Shared/ContactCard.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref, watch } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
  openModal: Boolean,
  createTimelineEvent: Boolean,
  timelineEvent: Object,
});

const emit = defineEmits(['closeModal', 'timelineEventCreated', 'lifeEventCreated']);

const form = useForm({
  lifeEventTypeId: 0,
  label: null,
  started_at: null,
  participants: [],
});

const loadingState = ref(false);
const selectedLifeEventCategory = ref([]);
const selectedLifeEventType = ref(null);
const editDate = ref(false);
const modalShown = ref(false);

watch(
  () => props.openModal,
  (value) => {
    modalShown.value = value;
  },
);

onMounted(() => {
  modalShown.value = props.openModal;
  selectedLifeEventCategory.value = props.data.life_event_categories[0];
  form.started_at = props.data.current_date;
});

const loadTypes = (category) => {
  var id = props.data.life_event_categories.findIndex((x) => x.id === category.id);
  selectedLifeEventCategory.value = props.data.life_event_categories[id];
};

const chooseType = (type) => {
  selectedLifeEventType.value = type;
  form.lifeEventTypeId = type.id;
};

const resetType = () => {
  selectedLifeEventCategory.value = props.data.life_event_categories[0];
  selectedLifeEventType.value = null;
};

const store = () => {
  loadingState.value = 'loading';

  // we either called the Create life event modal from inside an existing timeline
  // event, or from a new timeline event
  // this changes the url we post to as we need to pass the right info back to
  // the parent (ie. if it needs to refresh a specific timeline event, or the entire
  // timeline)
  var url = '';
  if (props.createTimelineEvent) {
    url = props.data.url.store;
  } else {
    url = props.timelineEvent.url.store;
  }

  axios
    .post(url, form)
    .then((response) => {
      loadingState.value = '';
      emit('closeModal');

      if (props.createTimelineEvent) {
        emit('timelineEventCreated', response.data.data);
      } else {
        emit('lifeEventCreated', response.data.data);
      }
    })
    .catch(() => {
      loadingState.value = '';
    });
};
</script>

<template>
  <div>
    <form
      v-if="modalShown"
      class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900"
      @submit.prevent="store()">
      <!-- choose life event categories/types -->
      <div v-if="!selectedLifeEventType" class="border-b border-gray-200 dark:border-gray-700">
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
                <span @click="chooseType(lifeEventType)" class="text-blue-500 hover:underline text-sm">{{ $t('app.choose') }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- type has been selected -->
      <div v-else class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 p-3">
        <div>
          <span class="text-sm">Chosen type:</span> <span class="px-2 py-1 text-sm border bg-white font-mono rounded">{{ selectedLifeEventCategory.label }}</span> > <span class="px-2 py-1 text-sm border bg-white font-mono rounded">{{ selectedLifeEventType.label }}</span>
        </div>

        <p @click="resetType()" class="text-blue-500 hover:underline cursor-pointer text-sm">{{ $t('app.change') }}</p>
      </div>

      <!-- date of the event -->
      <div v-if="selectedLifeEventType" class="border-b border-gray-200 dark:border-gray-700 p-3">

        <!-- default date -->
        <div v-if="!editDate" class="flex items-center justify-between">
          <div>
            <span class="text-sm">Date of the event:</span> {{ props.data.current_date_human_format }}
          </div>

          <p @click="editDate = true" class="text-blue-500 hover:underline cursor-pointer text-sm">{{ $t('app.change') }}</p>
        </div>

        <!-- customize date -->
        <div v-if="editDate">
          <p class="mb-2 block text-sm dark:text-gray-100">Date of the event</p>
          <v-date-picker v-model="form.started_at" :timezone="'UTC'" class="inline-block h-full" :model-config="modelConfig">
            <template #default="{ inputValue, inputEvents }">
              <input
                class="rounded border bg-white px-2 py-1 dark:bg-gray-900"
                :value="inputValue"
                v-on="inputEvents" />
            </template>
          </v-date-picker>
        </div>
      </div>

      <!-- participants -->
      <div v-if="selectedLifeEventType" class="border-b border-gray-200 dark:border-gray-700 p-3">
        <p class="mb-2 block text-sm dark:text-gray-100">Participants</p>

        <!-- current contact -->
        <div class="flex items-center mb-4">
          <avatar :data="props.data.contact.avatar" :classes="'mr-2 h-5 w-5'" />

          <span>{{ props.data.contact.name }}</span>
        </div>

        <!-- all other participants -->
        <contact-selector
            v-model="form.participants"
            :search-url="layoutData.vault.url.search_contacts_only"
            :most-consulted-contacts-url="layoutData.vault.url.get_most_consulted_contacts"
            :display-most-consulted-contacts="true"
            :add-multiple-contacts="true"
            :required="true"
            :div-outer-class="'flex-1 border-gray-200 dark:border-gray-700'" />
      </div>

      <!-- options -->
      <div v-if="selectedLifeEventType" class="border-b border-gray-200 dark:border-gray-700 p-3">
        <span
          class="mr-2 mb-2 flex cursor-pointer flex-wrap rounded-lg border bg-slate-200 px-1 py-1 hover:bg-slate-300 dark:text-gray-900"
          @click="displayMiddleNameField">
          {{ $t('vault.create_contact_add_middle_name') }}
        </span>
      </div>
      <div class="flex justify-between p-5">
          <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="$emit('closeModal')" />
          <pretty-button v-if="selectedLifeEventType" :text="$t('app.save')" :state="loadingState" :icon="'plus'" :classes="'save'" />
        </div>
    </form>
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
