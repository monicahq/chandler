<script setup>
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref } from 'vue';

const props = defineProps({
  data: Object,
});

const createMoodEventModalShown = ref(false);

const form = useForm({
  show_group_tab: false,
});

</script>

<template>
  <div class="mb-10">
    <h3 class="mb-3 border-b border-gray-200 pb-1 font-medium dark:border-gray-700">
      <span class="relative">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-sidebar relative inline h-4 w-4 text-gray-300 hover:text-gray-600 dark:text-gray-400 hover:dark:text-gray-400">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59" />
        </svg>
      </span>

      Record your mood
    </h3>

    <!-- cta -->
    <div
      v-if="! createMoodEventModalShown"
      class="mb-4 flex items-center rounded-lg border border-gray-200 bg-white p-3 dark:border-gray-700 dark:bg-gray-900">
      <img src="/img/dashboard_blank_how_are_you.svg" :alt="$t('Reminders')" class="mr-2 h-14 w-14" />
      <div class="px-5 flex flex-col mb-2">
        <p class="mb-2">
          How are you?
        </p>
        <pretty-button :text="'Record your mood'" @click="createMoodEventModalShown = true" />
      </div>
    </div>

    <!-- add an address modal -->
    <form
      v-if="createMoodEventModalShown"
      class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900"
      @submit.prevent="submit()">
      <div class="border-b border-gray-200 dark:border-gray-700 p-5">
        <div v-if="form.errors.length > 0" class="p-5">
          <errors :errors="form.errors" />
        </div>

        <!-- mood tracking parameters -->
        <p class="mb-2 block text-sm dark:text-gray-100">How do you feel right now?</p>
        <ul>
          <li v-for="parameter in props.data.mood_tracking_parameters" :key="parameter.id" class="flex">
            <input
            :id="'input' + parameter.id"
            v-model="form.value"
            :value="parameter.value"
            name="date-format"
            type="radio"
            class="relative mr-3 h-4 w-4 border-gray-300 text-sky-500 dark:border-gray-700" />

            <label
              :for="'input' + parameter.id"
              class="block cursor-pointer font-medium text-gray-700 dark:text-gray-300">
              <div class="mr-2 inline-block h-4 w-4 rounded-full" :class="parameter.hex_color" />
              {{ parameter.label }}
            </label>
          </li>
        </ul>
      </div>

      <div class="flex justify-between p-5">
        <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="createAddressModalShown = false" />
        <pretty-button :text="$t('app.save')" :state="loadingState" :icon="'plus'" :classes="'save'" />
      </div>
    </form>
  </div>
</template>

<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
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
