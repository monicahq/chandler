<template>
  <div class="mb-10">
    <h3 class="mb-3 border-b border-gray-200 pb-1 font-medium">
      <span class="relative">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="icon-sidebar relative inline h-4 w-4 text-gray-300 hover:text-gray-600"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor">
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
      </span>
      Reminders for the next 7 days
    </h3>

    <!-- list of reminders -->
    <div v-for="reminder in data" :key="reminder.id">
      <ul>
        <li class="text-xs text-gray-400">{{ reminder.scheduled_at }}</li>
        <li class="flex items-center text-sm">
          <div v-html="reminder.contact.avatar" class="mr-2 h-5 w-5"></div>

          <inertia-link :href="reminder.contact.url.show" class="text-blue-500 hover:underline">{{
            reminder.contact.name
          }}</inertia-link>
        </li>
      </ul>
      <p class="mb-3 text-sm">{{ reminder.label }}</p>
    </div>

    <!-- blank state -->
    <div v-if="data.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">{{ $t('settings.notification_channels_log_blank') }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      default: null,
    },
  },
};
</script>
