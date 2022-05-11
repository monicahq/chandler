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
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
          </svg>
        </span>

        <span class="font-semibold">Relationships</span>
      </div>
      <pretty-link :text="'Add a relationship'" :icon="'plus'" :href="data.url.store" :classes="'sm:w-fit w-full'" />
    </div>

    <!-- relationships -->
    <div>
      <div v-for="relationshipGroupType in data.relationship_group_types" :key="relationshipGroupType.id" class="mb-4">
        <!-- group name -->
        <h3 v-if="relationshipGroupType.relationship_types.length > 0" class="mb-1 font-semibold">
          {{ relationshipGroupType.name }}
        </h3>

        <!-- list of relationship types in this group -->
        <ul
          v-if="relationshipGroupType.relationship_types.length > 0"
          class="mb-4 rounded-lg border border-gray-200 last:mb-0">
          <li
            v-for="relationshipType in relationshipGroupType.relationship_types"
            :key="relationshipType.id"
            class="item-list flex items-center justify-between border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
            <div class="flex">
              <div class="mr-2 flex items-center">
                <div v-html="relationshipType.contact.avatar" class="mr-2 h-5 w-5"></div>

                <!-- name -->
                <inertia-link
                  v-if="relationshipType.contact.url.show"
                  :href="relationshipType.contact.url.show"
                  class="text-sky-500 hover:text-blue-900">
                  {{ relationshipType.contact.name }}
                </inertia-link>
                <span v-else>{{ relationshipType.contact.name }}</span>
              </div>

              <!-- age -->
              <span class="mr-2 text-gray-400">(3)</span>

              <!-- relationship type -->
              <span class="mr-2 text-gray-400">{{ relationshipType.relationship_type.name }}</span>
            </div>

            <!-- actions -->
            <ul class="text-sm">
              <li
                class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900"
                @click="updateAdressTypeModal(addressType)">
                Rename
              </li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900" @click="destroy(addressType)">
                Delete
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>

    <!-- blank state -->
    <div v-if="data.number_of_defined_relations == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no relationships yet.</p>
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
      loadingState: '',
      titleFieldShown: false,
      emotionFieldShown: false,
      createNoteModalShown: false,
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

  created() {},

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
