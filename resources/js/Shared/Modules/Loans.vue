<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.icon-note {
  top: -1px;
}

.item-list {
  &:hover {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
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
              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </span>

        <span class="font-semibold">Loans</span>
      </div>
      <pretty-button :text="'Record a loan'" :icon="'plus'" :classes="'sm:w-fit w-full'" @click="showCreateLoanModal" />
    </div>

    <div>
      <!-- add a loan modal -->
      <form
        v-if="createLoanModalShown"
        class="mb-6 rounded-lg border border-gray-200 bg-white"
        @submit.prevent="submit()">
        <div class="border-b border-gray-200">
          <div v-if="form.errors.length > 0" class="p-5">
            <errors :errors="form.errors" />
          </div>

          <!-- loan options -->
          <div class="border-b border-gray-200 p-5">
            <ul class="">
              <!-- show all -->
              <li class="mr-5 inline-block">
                <div class="flex items-center">
                  <input
                    id="nickname"
                    v-model="form.nameOrder"
                    value="%nickname%"
                    name="name-order"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500" />
                  <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700"> The loan is an object </label>
                </div>
              </li>

              <li class="mr-5 inline-block">
                <div class="flex items-center">
                  <input
                    id="nickname"
                    v-model="form.nameOrder"
                    value="%nickname%"
                    name="name-order"
                    type="radio"
                    class="h-4 w-4 border-gray-300 text-sky-500" />
                  <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                    The loan is monetary
                  </label>
                </div>
              </li>
            </ul>
          </div>

          <!-- name -->
          <div class="border-b border-gray-200 p-5">
            <text-input
              :ref="'label'"
              v-model="form.label"
              :label="'What is the loan?'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createLoanModalShown = false" />
          </div>

          <!-- amount + currency -->
          <div class="border-b border-gray-200 p-5 flex">
            <text-input
              :ref="'label'"
              v-model="form.label"
              :label="'How much money was lent?'"
              :type="'text'"
              :autofocus="true"
              :input-class="'mr-2'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createLoanModalShown = false" />

            <dropdown
              v-model="form.pronoun_id"
              :data="data.pronouns"
              :required="false"
              :div-outer-class="'mb-5'"
              :placeholder="'Choose a value'"
              :dropdown-class=""
              :label="'Currency'" />
          </div>

          <!-- loaned at -->
          <div class="border-b border-gray-200 p-5">
            <p class="mb-2 block text-sm">When was the loan made?</p>

            <v-date-picker class="inline-block h-full" v-model="form.date" :model-config="modelConfig">
              <template v-slot="{ inputValue, inputEvents }">
                <input class="rounded border bg-white px-2 py-1" :value="inputValue" v-on="inputEvents" />
              </template>
            </v-date-picker>
          </div>

          <div class="p-5">
            <p class="mb-1">How often should we remind you about this date?</p>
            <p class="mb-1 text-sm text-gray-600">
              If the date is in the past, the next occurence of the date will be next year.
            </p>

            <div class="mt-4 ml-4">
              <div class="mb-2 flex items-center">
                <input
                  id="one_time"
                  v-model="form.reminderChoice"
                  value="one_time"
                  name="reminder-frequency"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label for="one_time" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700">
                  Only once, when the next occurence of the date occurs.
                </label>
              </div>

              <div class="mb-2 flex items-center">
                <input
                  id="recurring"
                  v-model="form.reminderChoice"
                  value="recurring"
                  name="reminder-frequency"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label
                  for="recurring"
                  class="ml-3 block flex cursor-pointer items-center text-sm font-medium text-gray-700">
                  <span class="mr-2">Every</span>

                  <select
                    :id="id"
                    v-model="form.frequencyNumber"
                    class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm"
                    :required="required"
                    @change="change">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                  </select>

                  <select
                    :id="id"
                    v-model="form.frequencyType"
                    class="mr-2 rounded-md border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm"
                    :required="required"
                    @change="change">
                    <option value="recurring_day">day</option>
                    <option value="recurring_month">month</option>
                    <option value="recurring_year">year</option>
                  </select>

                  <span>after the next occurence of the date.</span>
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-between p-5">
          <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createLoanModalShown = false" />
          <pretty-button :text="'Add date'" :state="loadingState" :icon="'plus'" :classes="'save'" />
        </div>
      </form>

      <ul class="mb-4">
        <!-- show all -->
        <li class="mr-5 inline-block">
          <div class="mb-2 flex items-center">
            <input
              id="nickname"
              v-model="form.nameOrder"
              value="%nickname%"
              name="name-order"
              type="radio"
              class="h-4 w-4 border-gray-300 text-sky-500" />
            <label for="nickname" class="ml-3 block cursor-pointer text-sm font-medium text-gray-700"> Show all </label>
          </div>
        </li>

        <!-- only things I've borrewed -->
        <li class="mr-5 inline-block">
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

      <!-- list of loans -->
      <div class="flex mb-5">
        <div class="flex items-center mr-3">
          <small-contact :show-name="false" :preview-contact-size="30" />

          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>

          <small-contact :show-name="false" :preview-contact-size="30" />
        </div>

        <div class="item-list rounded-lg border border-gray-200 bg-white hover:bg-slate-50 w-full">
          <div class="px-3 py-2 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <span class="mr-2">ldaskjflasdkjf</span>
              <span class="mr-2 text-sm text-gray-500">30 nov 2022</span>
            </div>
          </div>

          <!-- actions -->
          <div class="flex items-center justify-between px-3 py-2">
            <!-- <small-contact /> -->
            <ul class="text-sm">
              <li class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">Settle</li>
              <li class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">Edit</li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900">Delete</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="flex">
        <div class="flex items-center mr-3">
          <small-contact :show-name="false" :preview-contact-size="30" />

          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
          </svg>

          <small-contact :show-name="false" :preview-contact-size="30" />
        </div>

        <div class="item-list rounded-lg border border-gray-200 bg-white hover:bg-slate-50 w-full">
          <div class="px-3 py-2 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <span class="mr-2">ldaskjflasdkjf</span>
              <span class="mr-2 text-sm text-gray-500">30 nov 2022</span>
            </div>
          </div>

          <!-- actions -->
          <div class="flex items-center justify-between px-3 py-2">
            <!-- <small-contact /> -->
            <ul class="text-sm">
              <li class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">Settle</li>
              <li class="mr-4 inline cursor-pointer text-sky-500 hover:text-blue-900">Edit</li>
              <li class="inline cursor-pointer text-red-500 hover:text-red-900">Delete</li>
            </ul>
          </div>
        </div>
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
import SmallContact from '@/Shared/SmallContact';
import Dropdown from '@/Shared/Form/Dropdown';

export default {
  components: {
    HoverMenu,
    PrettyButton,
    PrettySpan,
    TextInput,
    TextArea,
    Errors,
    SmallContact,
    Dropdown,
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
