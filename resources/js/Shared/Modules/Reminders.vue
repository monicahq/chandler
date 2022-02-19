<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.icon-note {
  top: -1px;
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
            class="icon-sidebar relative inline h-4 w-4"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path
              d="M15 17C16.1046 17 17 16.1046 17 15C17 13.8954 16.1046 13 15 13C13.8954 13 13 13.8954 13 15C13 16.1046 13.8954 17 15 17Z"
              fill="currentColor" />
            <path
              fill-rule="evenodd"
              clip-rule="evenodd"
              d="M6 3C4.34315 3 3 4.34315 3 6V18C3 19.6569 4.34315 21 6 21H18C19.6569 21 21 19.6569 21 18V6C21 4.34315 19.6569 3 18 3H6ZM5 18V7H19V18C19 18.5523 18.5523 19 18 19H6C5.44772 19 5 18.5523 5 18Z"
              fill="currentColor" />
          </svg>
        </span>

        <span class="font-semibold">Reminders</span>
      </div>
      <pretty-button
        :text="'Add a reminder'"
        :icon="'plus'"
        :classes="'sm:w-fit w-full'"
        @click="showCreateNoteModal" />
    </div>

    <!-- add a reminder modal -->

    <!-- reminders -->
    <div v-if="localReminders.length > 0">
      <ul class="mb-4 rounded-lg border border-gray-200 bg-white">
        <li
          v-for="reminder in localReminders"
          :key="reminder.id"
          class="item-list flex items-center justify-between border-b border-gray-200 hover:bg-slate-50 px-3 py-2">
          <div class="flex items-center">
            <span class="mr-2 text-sm text-gray-500">10 Aug 2020</span>
            <span class="mr-2">birthdate</span>

            <!-- recurring icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
          </div>

          <!-- actions -->
          <ul class="text-sm">
            <li
              class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
              @click="updateGenderModal(gender)">
              Edit
            </li>
            <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(gender)">Delete</li>
          </ul>
        </li>
      </ul>
    </div>

    <!-- blank state -->
    <div v-if="localReminders.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no reminders yet.</p>
    </div>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import TextArea from '@/Shared/Form/TextArea';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
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
      loadingState: '',
      titleFieldShown: false,
      emotionFieldShown: false,
      createNoteModalShown: false,
      localReminders: [],
      editedNoteId: 0,
      form: {
        title: '',
        body: '',
        emotion: '',
        errors: [],
      },
    };
  },

  created() {
    this.localReminders = this.data.notes;
  },

  methods: {
    showCreateNoteModal() {
      this.form.errors = [];
      this.form.title = '';
      this.form.body = '';
      this.createNoteModalShown = true;
    },

    showEmotionField() {
      this.form.emotion = '';
      this.emotionFieldShown = true;
    },

    showEditNoteModal(note) {
      this.editedNoteId = reminder.id;
      this.form.title = reminder.title;
      this.form.body = reminder.body;
      this.form.emotion = reminder.emotion.id;
    },

    showTitleField() {
      this.titleFieldShown = true;
      this.form.title = '';

      this.$nextTick(() => {
        this.$refs.newTitle.focus();
      });
    },

    showFullBody(note) {
      this.localReminders[this.localReminders.findIndex((x) => x.id === reminder.id)].show_full_content = true;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The note has been created', 'success');
          this.localReminders.unshift(response.data.data);
          this.loadingState = '';
          this.createNoteModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(note) {
      this.loadingState = 'loading';

      axios
        .put(reminder.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The note has been edited', 'success');
          this.localReminders[this.localReminders.findIndex((x) => x.id === reminder.id)] = response.data.data;
          this.editedNoteId = 0;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    destroy(note) {
      if (confirm('Are you sure? This will delete the note permanently.')) {
        axios
          .delete(reminder.url.destroy)
          .then((response) => {
            this.flash('The note has been deleted', 'success');
            var id = this.localReminders.findIndex((x) => x.id === reminder.id);
            this.localReminders.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
