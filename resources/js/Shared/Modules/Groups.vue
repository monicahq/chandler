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
              d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
          </svg>
        </span>

        <span class="font-semibold">Groups</span>
      </div>
      <pretty-link :text="'Add to group'" :icon="'plus'" :href="data.url.create" :classes="'sm:w-fit w-full'" />
    </div>

    <!-- groups -->
    <ul class="mb-4 rounded-lg border border-gray-200 last:mb-0">
      <li
        v-for="group in localGroups"
        :key="group.id"
        class="item-list flex items-center justify-between border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
        <div>
          <p>{{ group.name }}</p>

          <div v-if="group.contacts.length > 0" class="relative flex -space-x-2 overflow-hidden p-3">
            <div v-for="contact in group.contacts" :key="contact.id" class="inline-block">
              <inertia-link :href="contact.url.show"
                ><div v-html="contact.avatar" class="h-8 w-8 rounded-full ring-2 ring-white"></div
              ></inertia-link>
            </div>
          </div>
        </div>

        <!-- actions -->
        <ul class="text-sm">
          <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(relationshipType)">
            Remove
          </li>
        </ul>
      </li>
    </ul>

    <!-- blank state -->
    <div v-if="localGroups.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no groups yet.</p>
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
    moduleMode: {
      type: Boolean,
      default: true,
    },
  },

  data() {
    return {
      localGroups: [],
    };
  },

  created() {
    this.localGroups = this.data.groups;
  },

  methods: {
    destroy(relationshipType) {
      if (confirm('Are you sure? This will delete the relationship.')) {
        axios
          .put(relationshipType.url.update)
          .then((response) => {
            this.flash('The relationship has been deleted', 'success');
            this.localGroups = response.data.data.relationship_group_types;
          })
          .catch((error) => {
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
