<script setup>
import Loading from '@/Shared/Loading.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import ContactCard from '@/Shared/ContactCard.vue';
import CreateLifeEvent from '@/Shared/Modules/CreateLifeEvent.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
});

const createLifeEventModalShown = ref(false);
const loadingData = ref(false);
const paginator = ref([]);
const timeline = ref([]);
const showAddLifeEventModalForTimelineEventId = ref(0);

onMounted(() => {
  initialLoad();
});

const initialLoad = () => {
  loadingData.value = true;

  axios
    .get(props.data.url.load)
    .then((response) => {
      loadingData.value = false;
      response.data.data.timeline_events.forEach((entry) => {
        timeline.value.push(entry);
      });
      paginator.value = response.data.paginator;
    })
    .catch(() => {});
};

const refreshTimelineEvents = (timelineEvent) => {
  timeline.value.unshift(timelineEvent);
};

const refreshLifeEvents = (lifeEvent) => {
  var id = timeline.value.findIndex((x) => x.id === lifeEvent.timeline_event.id);
  timeline.value[id].life_events.unshift(lifeEvent);
};

const showCreateLifeEventModal = () => {
  createLifeEventModalShown.value = true;
};

const toggleTimelineEventVisibility = (timelineEvent) => {
  timelineEvent.collapsed = !timelineEvent.collapsed;
};

const toggleLifeEventVisibility = ( lifeEvent) => {
  lifeEvent.collapsed = !lifeEvent.collapsed;
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
      <!-- add a timeline event -->
      <create-life-event :data="props.data"
        :layout-data="props.layoutData"
        :open-modal="createLifeEventModalShown"
        :create-timeline-event="true"
        @close-modal="createLifeEventModalShown = false"
        @timeline-event-created="refreshTimelineEvents" />

      <!-- list of timeline events -->
      <div>
        <div v-for="timelineEvent in timeline" :key="timelineEvent.id" class="mb-4">

          <!-- timeline event name -->
          <div class="flex justify-between items-center border border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800 rounded-lg px-3 py-2 mb-2 cursor-pointer" @click="toggleTimelineEventVisibility(timelineEvent)">

            <!-- timeline date / label / number of events -->
            <div>
              <span class="mr-2 text-gray-500">{{ timelineEvent.happened_at }}</span>

              <span class="ml-3 whitespace-nowrap rounded-lg bg-slate-100 py-0.5 px-2 text-sm text-slate-400">{{ timelineEvent.life_events.length }}</span>
            </div>

            <!-- chevrons -->
            <svg v-if="!timelineEvent.collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
            </svg>

            <svg v-if="timelineEvent.collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
            </svg>

          </div>

          <!-- life events -->
          <div v-if="timelineEvent.collapsed">
            <div v-for="lifeEvent in timelineEvent.life_events" :key="lifeEvent.id" :class="!lifeEvent.collapsed ? 'border' : ''" class="ml-6 border-gray-200 rounded-lg mb-2">
              <!-- name of life event -->
              <div :class="lifeEvent.collapsed ? 'rounded-lg border' : ''" class="flex justify-between items-center border-b border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800 px-3 py-2 cursor-pointer" @click="toggleLifeEventVisibility(lifeEvent)">
                <!-- title -->
                <div class="flex items-center">
                  <p class="mr-4 text-sm font-bold">Activity #1</p>
                  <div>
                    <span class="px-2 py-1 text-sm border bg-white font-mono rounded">{{ lifeEvent.life_event_type.category.label }}</span> > <span class="px-2 py-1 text-sm border bg-white font-mono rounded">{{ lifeEvent.life_event_type.label }}</span>
                  </div>
                </div>

                  <!-- chevrons -->
                <svg v-if="lifeEvent.collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>

                <svg v-if="!lifeEvent.collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                </svg>
              </div>

              <!-- date of life event -->
              <div v-if="!lifeEvent.collapsed" class="flex items-center border-b border-gray-200 px-3 py-2 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500 mr-1">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                {{ lifeEvent.happened_at }}
              </div>

              <!-- participants -->
              <div v-if="!lifeEvent.collapsed" class="p-3 pb-1 flex">
                <div v-for="contact in lifeEvent.participants" :key="contact.id" class="mr-4">
                  <contact-card :contact="contact" :avatarClasses="'h-5 w-5 rounded-full mr-2'" :displayName="true" />
                </div>
              </div>
            </div>

            <!-- add a new life event to the timeline -->
            <div class="ml-6 mb-2">
              <span @click="showAddLifeEventModalForTimelineEventId = timelineEvent.id"
                v-if="showAddLifeEventModalForTimelineEventId != timelineEvent.id"
                class="text-sm text-blue-500 hover:underline cursor-pointer"
                >
                Add another life event
              </span>

              <create-life-event
                :data="props.data"
                :layout-data="props.layoutData"
                :open-modal="showAddLifeEventModalForTimelineEventId == timelineEvent.id"
                :create-timeline-event="false"
                :timeline-event="timelineEvent"
                @close-modal="showAddLifeEventModalForTimelineEventId = 0"
                @life-event-created="refreshLifeEvents" />
            </div>
          </div>

        </div>
      </div>

      <!-- loading mode -->
      <div v-if="loadingData" class="mb-5 rounded-lg border border-gray-200 p-20 text-center">
        <loading />
      </div>
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
