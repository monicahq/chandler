<template>
  <button
    :class="'dark:box-s relative border-zinc-900 bg-white text-sm dark:border-zinc-100 dark:bg-gray-800 dark:text-gray-100'"
    :disabled="state === 'loading' || state === 'disabled'"
    type="submit">
    <span v-if="state === 'loading'"> {{ $t('Loading…') }} </span>

    <!-- + icon -->
    <svg
      v-if="icon === 'plus' && state != 'loading'"
      xmlns="http://www.w3.org/2000/svg"
      class="icon relative me-1 inline h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
    </svg>

    <!-- check icon -->
    <svg
      v-if="icon === 'check' && state != 'loading'"
      xmlns="http://www.w3.org/2000/svg"
      class="icon relative me-1 inline h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
    </svg>

    <!-- minus icon -->
    <svg
      v-if="icon === 'minus' && state != 'loading'"
      xmlns="http://www.w3.org/2000/svg"
      class="icon relative mx-1 inline h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
      stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>

    <span v-if="state !== 'loading'">
      {{ text }}
    </span>

    <!-- arrow icon -->
    <svg
      v-if="icon === 'arrow' && state !== 'loading'"
      xmlns="http://www.w3.org/2000/svg"
      class="icon relative mx-1 inline h-5 w-5"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
    </svg>
  </button>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: '',
    },
    icon: {
      type: String,
      default: '',
    },
    state: {
      type: String,
      default: '',
    },
  },
};
</script>

<style lang="scss" scoped>
.icon {
  top: -1px;
}

.save {
  background-color: #fcf27e;
}

.dark .save {
  background-color: #d0c10d !important;
  color: rgb(31 41 55) !important; // text-gray-800
}

button {
  --tw-shadow: 2px 2px 0 #191a1b !important;
  border-radius: 0.25rem !important;
  border-width: 1px !important;
  box-shadow: var(--tw-ring-offset-shadow, 0 0 transparent), var(--tw-ring-shadow, 0 0 transparent), var(--tw-shadow) !important;
  display: inline-block !important;
  position: relative !important;
  text-decoration: none !important;
  transition-duration: 0.15s !important;
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform, filter,
    backdrop-filter, -webkit-backdrop-filter !important;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1) !important;
  font-size: 0.875rem !important;
  line-height: 1.25rem !important;
  padding-left: 9px;
  padding-right: 9px;

  &:hover {
    box-shadow: none !important;
    transform: translate(2px, 2px);
  }

  &:disabled {
    box-shadow: none;
    transform: translate(0, 0);
  }
}

.dark button {
  --tw-shadow: 2px 2px 0 rgb(242, 242, 245) !important;
}
</style>
