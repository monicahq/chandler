<script setup>
import Layout from '@/Shared/Layout.vue';
import ContactCard from '@/Shared/ContactCard.vue';
import { ref } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
});

const currentDay = ref([]);
const currentDayLoaded = ref(false);

const get = (day) => {
  if (day.is_in_month === false) {
    return;
  }

  axios
    .get(day.url.show)
    .then((response) => {
      currentDayLoaded.value = true;
      currentDay.value = response.data.data;
    });
};
</script>

<template>
  <layout title="Calendar" :inside-vault="true" :layout-data="layoutData">
    <main class="relative sm:mt-24">
      <div class="max-w-8xl mx-auto py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="special-grid grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            <!-- month browser -->
            <div class="mb-4 flex items-center justify-between">
              <!-- month name -->
              <p class="text-lg font-bold">{{ data.current_month }}</p>

              <!-- month next/previous -->
              <div class="flex justify-center">
                <div class="inline-flex rounded-md shadow-sm">
                  <inertia-link
                    :href="data.url.previous"
                    class="inline-flex items-center rounded-l-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:hover:text-white dark:focus:text-white dark:focus:ring-blue-500">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="mr-2 h-4 w-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>

                    {{ data.previous_month }}
                  </inertia-link>

                  <inertia-link
                    :href="data.url.next"
                    class="inline-flex items-center rounded-r-md border-t border-b border-r border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-900 hover:bg-gray-100 hover:text-blue-700 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:hover:text-white dark:focus:text-white dark:focus:ring-blue-500">
                    {{ data.next_month }}

                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke-width="1.5"
                      stroke="currentColor"
                      class="ml-2 h-4 w-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                  </inertia-link>
                </div>
              </div>
            </div>

            <!-- days -->
            <div class="grid grid-cols-7 rounded-t-lg border-t border-l border-r last:border-b">
              <div class="border-r p-2 text-center text-xs">{{ $t('app.monday') }}</div>
              <div class="border-r p-2 text-center text-xs">{{ $t('app.tuesday') }}</div>
              <div class="border-r p-2 text-center text-xs">{{ $t('app.wednesday') }}</div>
              <div class="border-r p-2 text-center text-xs">{{ $t('app.thursday') }}</div>
              <div class="border-r p-2 text-center text-xs">{{ $t('app.friday') }}</div>
              <div class="border-r p-2 text-center text-xs">{{ $t('app.saturday') }}</div>
              <div class="p-2 text-center text-xs">{{ $t('app.sunday') }}</div>
            </div>

            <!-- actual calendar -->
            <div
              v-for="week in data.weeks"
              :key="week.id"
              class="grid grid-cols-7 border-t border-l border-r last:rounded-b-lg last:border-b">
              <div
                v-for="day in week.days"
                :key="day.id"
                @click="get(day)"
                class="h-32 border-r p-2 last:border-r-0"
                :class="day.is_in_month ? 'cursor-pointer' : 'bg-slate-50'">

                <!-- date of the day -->
                <div class="flex items-center justify-between">
                  <span class="p-1 text-xs mb-1 inline-block" :class="day.current_day ? 'rounded-lg bg-slate-200' : ''">{{
                    day.date
                  }}</span>

                  <!-- mood for the day -->
                  <div class="flex">
                    <div v-for="mood in day.mood_events" :key="mood.id">
                      <a-tooltip placement="topLeft" :title="mood.mood_tracking_parameter.label" arrow-point-at-center>
                        <div class="mr-2 inline-block h-4 w-4 rounded-full" :class="mood.mood_tracking_parameter.hex_color" />
                      </a-tooltip>
                    </div>
                  </div>
                </div>

                <!-- important dates -->
                <div v-if="day.important_dates">
                  <div v-if="day.important_dates.length > 0" class="text-xs mb-1 text-gray-600">Important dates</div>
                  <div v-if="day.important_dates.length > 0" class="flex">
                    <div v-for="contact in day.important_dates" :key="contact.id">
                      <contact-card :contact="contact" :avatarClasses="'h-5 w-5 rounded-full mr-2'" :displayName="false" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- right part -->
          <!-- detail of a day -->
          <div v-if="currentDayLoaded" class="p-3 sm:p-0 border border-gray-200 rounded-lg">

            <!-- day name -->
            <div class="p-2 border-b border-gray-200 text-center text-sm font-semibold">
              {{ currentDay.day }}
            </div>

            <!-- important dates -->
            <div v-if="currentDay.important_dates" class="p-3">
              <div class="text-sm mb-2 text-gray-600">Important dates</div>
              <ul>
                <li v-for="importantDate in currentDay.important_dates" :key="importantDate.id" class="flex justify-between mb-1">
                  <span>{{ importantDate.label }}</span>
                  <span><contact-card :contact="importantDate.contact" :avatarClasses="'h-5 w-5 rounded-full mr-2'" :displayName="false" /></span>
                </li>
              </ul>
              <div class="flex">
              </div>
            </div>

          </div>

          <!-- blank state -->
          <div v-else class="p-3 sm:p-0 border border-gray-200 rounded-lg flex items-center bg-gray-50">
            <div>
              <img src="/img/calendar_day_blank.svg" :alt="$t('Groups')" class="mx-auto mt-4 h-36 w-36" />
              <p class="px-5 pb-5 pt-2 text-center">
                Click on a day to see the details
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
.special-grid {
  grid-template-columns: 1fr 250px;
}

@media (max-width: 480px) {
  .special-grid {
    grid-template-columns: 1fr;
  }
}
</style>
