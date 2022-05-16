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
      <pretty-span @click="showCreateTaskModal()" :text="'Add a task'" :icon="'plus'" :classes="'sm:w-fit w-full'" />
    </div>

    <!-- add a task modal -->
    <form v-if="createTaskModalShown" class="bg-form mb-6 rounded-lg border border-gray-200" @submit.prevent="submit()">
      <div class="border-b border-gray-200 p-5">
        <errors :errors="form.errors" />

        <!-- title -->
        <text-input
          :ref="'label'"
          v-model="form.label"
          :label="'Title'"
          :type="'text'"
          :input-class="'block w-full mb-3'"
          :required="true"
          :autocomplete="false"
          :maxlength="255"
          @esc-key-pressed="createTaskModalShown = false" />
      </div>

      <div class="flex justify-between p-5">
        <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createTaskModalShown = false" />
        <pretty-button :text="'Save'" :state="loadingState" :icon="'check'" :classes="'save'" />
      </div>
    </form>

    <!-- tasks -->
    <ul v-if="localTasks.length > 0" class="mb-2 rounded-lg border border-gray-200 bg-white">
      <li v-for="task in localTasks" :key="task.id" class="item-list border-b border-gray-200 hover:bg-slate-50">
        <div v-if="editedTaskId !== task.id" class="flex items-center p-3">
          <input
            :id="task.id"
            :name="task.id"
            type="checkbox"
            class="focus:ring-3 relative h-4 w-4 rounded border border-gray-300 bg-gray-50 focus:ring-blue-300 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800 dark:focus:ring-blue-600" />
          <label :for="task.id" class="ml-2 cursor-pointer text-gray-900">
            {{ task.label }}
          </label>
        </div>

        <!-- edit task -->
        <form v-if="editedTaskId === task.id" class="bg-form border-b border-gray-200 p-5" @submit.prevent="update(task)">
            <errors :errors="form.errors" />

            <div class="flex justify-between">
              <text-input
                :ref="'newTitle'"
                v-model="form.label"
                :label="'Title'"
                :type="'text'"
                :input-class="'block w-full'"
                :required="true"
                :autocomplete="false"
                :maxlength="255"
                @esc-key-pressed="editedTaskId = 0" />

                <hover-menu
                :show-edit="true"
                :show-delete="true"
                @edit="showUpdateTaskModal(task)"
                @delete="destroy(task)" />
            </div>
            <div class="flex justify-between p-5">
              <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="editedTaskId = 0" />
              <pretty-button :text="'Update'" :state="loadingState" :icon="'check'" :classes="'save'" />
            </div>
        </form>
      </li>
    </ul>
    <p class="mb-6 mx-4 text-xs">
      Show completed tasks
    </p>

    <!-- blank state -->
    <div v-if="localTasks.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no tasks yet.</p>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
    TextInput,
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
      createTaskModalShown: false,
      localTasks: [],
      loadingState: '',
      editedTaskId: 0,
      form: {
        label: '',
        errors: [],
      },
    };
  },

  created() {
    this.localTasks = this.data.tasks;
  },

  methods: {
    showCreateTaskModal() {
      this.form.errors = [];
      this.form.label = '';
      this.createTaskModalShown = true;

      this.$nextTick(() => {
        this.$refs.label.focus();
      });
    },

    showUpdateTaskModal(task) {
      this.form.errors = [];
      this.form.label = '';
      this.editedTaskId = task.id;

      this.$nextTick(() => {
        this.$refs[`update${task.id}`].focus();
      });
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The task has been created', 'success');
          this.localTasks.unshift(response.data.data);
          this.createTaskModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

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
