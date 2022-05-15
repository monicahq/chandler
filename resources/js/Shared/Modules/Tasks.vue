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

<template>
  <div class="mb-10">
    <!-- title + cta -->
    <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 sm:flex">
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
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
          </svg>
        </span>

        <span class="font-semibold">Tasks</span>
      </div>
      <pretty-link :text="'Add a task'" :icon="'plus'" :href="data.url.create" :classes="'sm:w-fit w-full'" />
    </div>

    <!-- tasks -->
    <div class="mb-4"></div>
    <ul class="mb-6 rounded-lg border border-gray-200 bg-white">
      <li class="item-list border-b border-gray-200 hover:bg-slate-50">
        <div class="flex items-center p-3">
          <input
            id="remember-me"
            name="remember-me"
            type="checkbox"
            class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
          <label for="remember-me" class="ml-2 cursor-pointer text-sm text-gray-900">
            Remember mea sdfasdf asdf asdf asdf sdf
          </label>
        </div>
      </li>
      <li class="item-list border-b border-gray-200 hover:bg-slate-50">
        <div class="flex items-center p-3">
          <input
            id="remember-me"
            name="remember-me"
            type="checkbox"
            class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
          <label for="remember-me" class="ml-2 cursor-pointer text-sm text-gray-900">
            Remember mea sdfasdf asdf asdf asdf sdf
          </label>
        </div>
      </li>
    </ul>

    <!-- blank state -->
    <div v-if="localTasks == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no tasks yet.</p>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import PrettyLink from '@/Shared/Form/PrettyLink';
import TextInput from '@/Shared/Form/TextInput';
import TextArea from '@/Shared/Form/TextArea';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
    PrettyLink,
    TextInput,
    TextArea,
    Errors,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
    paginator: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      localTasks: [],
    };
  },

  created() {
    this.localTasks = this.data.tasks;
  },

  methods: {
    destroy(relationshipType) {
      if (confirm('Are you sure? This will delete the relationship.')) {
        axios
          .put(relationshipType.url.update)
          .then((response) => {
            this.flash('The relationship has been deleted', 'success');
            this.localTasks = response.data.data.relationship_group_types;
          })
          .catch((error) => {
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
