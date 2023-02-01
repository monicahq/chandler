<script setup>
import Layout from '@/Shared/Layout.vue';
import LastUpdated from '@/Pages/Vault/Dashboard/Partials/LastUpdated.vue';
import UpcomingReminders from '@/Pages/Vault/Dashboard/Partials/UpcomingReminders.vue';
import Favorites from '@/Pages/Vault/Dashboard/Partials/Favorites.vue';
import DueTasks from '@/Pages/Vault/Dashboard/Partials/DueTasks.vue';
import MoodTrackingEvents from '@/Pages/Vault/Dashboard/Partials/MoodTrackingEvents.vue';
import Feed from '@/Shared/Modules/Feed.vue';
import LifeEvent from '@/Shared/Modules/LifeEvent.vue';
import { onMounted, ref } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';

const props = defineProps({
  layoutData: Object,
  data: Object,
  lastUpdatedContacts: Object,
  upcomingReminders: Object,
  favorites: Object,
  url: Array,
  dueTasks: Object,
  moodTrackingEvents: Object,
  lifeEvents: Object,
  activityTabShown: String,
});

const defaultTab = ref('activity');

const form = useForm({
  show_activity_tab_on_dashboard: null,
});

onMounted(() => {
  if (props.activityTabShown) {
    defaultTab.value = 'activity';
  } else {
    defaultTab.value = 'life_events';
  }
});

const changeTab = (tab) => {
  defaultTab.value = tab;

  if (defaultTab.value === 'activity') {
    form.show_activity_tab_on_dashboard = 1;
  } else {
    form.show_activity_tab_on_dashboard = 0;
  }

  axios.put(props.url.default_tab, form);
};

</script>

<template>
  <layout title="Dashboard" :inside-vault="true" :layout-data="layoutData">
    <main class="relative sm:mt-24">
      <div class="max-w-8xl mx-auto py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            <!-- favorites -->
            <favorites v-if="favorites.length > 0" :data="favorites" />

            <!-- last updated contacts -->
            <last-updated :data="lastUpdatedContacts" />
          </div>

          <!-- middle -->
          <div class="p-3 sm:p-0">
            <div class="mb-8 w-full border-b border-gray-200 dark:border-gray-700">
              <div class="text-center flex overflow-x-auto">
                <div class="mr-2 flex-none">
                  <span
                    @click="changeTab('activity')"
                    :class="{ 'border-orange-500 hover:border-orange-500': defaultTab === 'activity' }"
                    class="cursor-pointer inline-block border-b-2 border-transparent px-2 pb-2 hover:border-gray-200 hover:dark:border-gray-700">
                    <span class="mb-0 block rounded-sm px-3 py-1 hover:bg-gray-100 hover:dark:bg-gray-900">Activity in this vault</span>
                  </span>
                </div>
                <div class="mr-2 flex-none">
                  <span
                    @click="changeTab('life_events')"
                    :class="{ 'border-orange-500 hover:border-orange-500': defaultTab === 'life_events' }"
                    class="cursor-pointer inline-block border-b-2 border-transparent px-2 pb-2 hover:border-gray-200 hover:dark:border-gray-700">
                    <span class="mb-0 block rounded-sm px-3 py-1 hover:bg-gray-100 hover:dark:bg-gray-900">Your life events</span>
                  </span>
                </div>
              </div>
            </div>

            <life-event v-if="defaultTab == 'life_events'" :data="lifeEvents" :layout-data="layoutData" />

            <!-- feed tab -->
            <div v-if="defaultTab == 'activity'">
              <feed :url="url.feed" :contact-view-mode="false" />
            </div>
          </div>

          <!-- right -->
          <div class="p-3 sm:p-0">
            <!-- mood tracking -->
            <mood-tracking-events :data="moodTrackingEvents" />

            <!-- upcoming reminders -->
            <upcoming-reminders :data="upcomingReminders" />

            <!-- tasks -->
            <due-tasks :data="dueTasks" />
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
.grid {
  grid-template-columns: 200px 1fr 400px;
}

@media (max-width: 480px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
