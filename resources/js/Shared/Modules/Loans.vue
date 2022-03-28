<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.icon-note {
  top: -1px;
}
</style>

<template>
  <div class="mb-10">
    <!-- title + cta -->
    <div class="mb-3 items-center justify-between border-b border-gray-200 pb-2 sm:flex">
      <div class="mb-2 sm:mb-0">
        <span class="relative mr-1">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-sidebar relative inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </span>

        <span class="font-semibold">Loans</span>
      </div>
      <pretty-button :text="'Record a loan'" :icon="'plus'" :classes="'sm:w-fit w-full'" @click="showCreateLoanModal" />
    </div>

    <div>
      <ul class="mb-4">
        <!-- show all -->
        <li class="inline-block mr-5">
          <div class="mb-2 flex items-center">
            <input
              id="nickname"
              v-model="form.nameOrder"
              value="%nickname%"
              name="name-order"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500" />
            <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
              Show all
            </label>
          </div>
        </li>

        <!-- only things I've borrewed -->
        <li class="inline-block mr-5">
          <div class="mb-2 flex items-center">
            <input
              id="nickname"
              v-model="form.nameOrder"
              value="%nickname%"
              name="name-order"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500" />
            <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
              Only what Lorraine owes
            </label>
          </div>
        </li>

        <!-- only things I've borrewed -->
        <li class="inline-block">
          <div class="mb-2 flex items-center">
            <input
              id="nickname"
              v-model="form.nameOrder"
              value="%nickname%"
              name="name-order"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500" />
            <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
              Only what Lorraine is due
            </label>
          </div>
        </li>
      </ul>

      <ul class="mb-4 rounded-lg border border-gray-200 bg-white">
        <li class="item-list border-b border-gray-200 hover:bg-slate-50">

          <div class="flex items-center justify-between px-3 py-2">
            <div class="flex items-center">
              <span class="mr-2 text-sm text-gray-500">30 nov 2022</span>
              <span class="mr-2">ldaskjflasdkjf</span>
            </div>

            <!-- actions -->
            <ul class="text-sm">
              <li
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">
                Edit
              </li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900">Delete</li>
            </ul>
          </div>
        </li>
      </ul>
    </div>

    <!-- loans -->
    <div v-if="localNotes.length > 0">
      <div v-for="note in localNotes" :key="note.id" class="mb-4 rounded border border-gray-200 last:mb-0">
        <!-- body of the note, if not being edited -->
        <div v-if="editedNoteId !== note.id">
          <div v-if="note.title" class="font-semibol mb-1 border-b border-gray-200 p-3 text-xs text-gray-600">
            {{ note.title }}
          </div>

          <!-- excerpt, if it exists -->
          <div v-if="!note.show_full_content && note.body_excerpt" class="p-3">
            {{ note.body_excerpt }}
            <span class="cursor-pointer text-sky-500 hover:text-blue-900" @click="showFullBody(note)"> View all </span>
          </div>
          <!-- full body -->
          <div v-else class="p-3">
            {{ note.body }}
          </div>

          <!-- details -->
          <div
            class="flex justify-between border-t border-gray-200 px-3 py-1 text-xs text-gray-600 hover:rounded-b hover:bg-slate-50">
            <div>
              <!-- emotion -->
              <div v-if="note.emotion" class="relative mr-3 inline">
                {{ note.emotion.name }}
              </div>

              <!-- date -->
              <div class="relative mr-3 inline">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="icon-note relative inline h-3 w-3 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ note.written_at }}
              </div>

              <!-- author -->
              <div class="relative mr-3 inline">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  class="icon-note relative inline h-3 w-3 text-gray-400"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ note.author }}
              </div>
            </div>
            <div>
              <hover-menu
                :show-edit="true"
                :show-delete="true"
                @edit="showEditNoteModal(note)"
                @delete="destroy(note)" />
            </div>
          </div>
        </div>

        <!-- edit modal form -->
        <form v-if="editedNoteId === note.id" class="bg-white" @submit.prevent="update(note)">
          <div class="border-b border-gray-200 p-5">
            <errors :errors="form.errors" />

            <text-area
              v-model="form.body"
              :label="'Body'"
              :rows="10"
              :required="true"
              :maxlength="65535"
              :textarea-class="'block w-full mb-3'" />

            <!-- title -->
            <text-input
              :ref="'newTitle'"
              v-model="form.title"
              :label="'Title'"
              :type="'text'"
              :input-class="'block w-full'"
              :required="false"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="editedNoteId = 0" />

            <!-- emotion -->
            <div v-if="form.emotion" class="mt-2 block w-full">
              <p class="mb-2">How did you feel?</p>
              <div v-for="emotion in data.emotions" :key="emotion.id" class="mb-2 flex items-center">
                <input
                  :value="emotion.id"
                  v-model="form.emotion"
                  :id="emotion.type"
                  name="emotion"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                <label :for="emotion.type" class="ml-2 block font-medium text-gray-700"> {{ emotion.name }} </label>
              </div>
            </div>
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="editedNoteId = 0" />
            <pretty-button :text="'Update'" :state="loadingState" :icon="'check'" :classes="'save'" />
          </div>
        </form>
      </div>

      <!-- view all button -->
      <div v-if="moduleMode" class="text-center">
        <inertia-link :href="data.url.index" class="text-sky-500 hover:text-blue-900"> View all notes </inertia-link>
      </div>

      <!-- pagination -->
      <div v-if="!moduleMode" class="flex justify-between text-center">
        <inertia-link
          v-show="paginator.previousPageUrl"
          class="fl dib"
          :href="paginator.previousPageUrl"
          title="Previous">
          &larr; Previous
        </inertia-link>
        <inertia-link v-show="paginator.nextPageUrl" class="fr dib" :href="paginator.nextPageUrl" title="Next">
          Next &rarr;
        </inertia-link>
      </div>
    </div>

    <!-- blank state -->
    <div v-if="localNotes.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no loans yet.</p>
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
      createLoanModalShown: false,
      localNotes: [],
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
    this.localNotes = this.data.notes;
  },

  methods: {
    showCreateLoanModal() {
      this.form.errors = [];
      this.form.title = '';
      this.form.body = '';
      this.createLoanModalShown = true;
    },

    showEmotionField() {
      this.form.emotion = '';
      this.emotionFieldShown = true;
    },

    showEditNoteModal(note) {
      this.editedNoteId = note.id;
      this.form.title = note.title;
      this.form.body = note.body;
      this.form.emotion = note.emotion.id;
    },

    showTitleField() {
      this.titleFieldShown = true;
      this.form.title = '';

      this.$nextTick(() => {
        this.$refs.newTitle.focus();
      });
    },

    showFullBody(note) {
      this.localNotes[this.localNotes.findIndex((x) => x.id === note.id)].show_full_content = true;
    },

    submit() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.store, this.form)
        .then((response) => {
          this.flash('The note has been created', 'success');
          this.localNotes.unshift(response.data.data);
          this.loadingState = '';
          this.createLoanModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(note) {
      this.loadingState = 'loading';

      axios
        .put(note.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The note has been edited', 'success');
          this.localNotes[this.localNotes.findIndex((x) => x.id === note.id)] = response.data.data;
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
          .delete(note.url.destroy)
          .then((response) => {
            this.flash('The note has been deleted', 'success');
            var id = this.localNotes.findIndex((x) => x.id === note.id);
            this.localNotes.splice(id, 1);
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
