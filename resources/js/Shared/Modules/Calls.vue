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
          <svg xmlns="http://www.w3.org/2000/svg" class="icon-sidebar relative inline h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
          </svg>
        </span>

        <span class="font-semibold">Calls</span>
      </div>
      <pretty-span @click="showCreateCallModal()" :text="'Log a call'" :icon="'plus'" :classes="'sm:w-fit w-full'" />
    </div>

    <!-- add a task modal -->
    <form
      v-if="createCallModalShown"
      class="bg-form mb-6 rounded-lg border border-gray-200"
      @submit.prevent="submit()">
      <div class="border-b border-gray-200">
        <div v-if="form.errors.length > 0" class="p-5">
          <errors :errors="form.errors" />
        </div>

        <!-- name -->
        <div class="border-b border-gray-200 flex">
          <div class="p-5">
            <p class="mb-2 block text-sm">When did the call happened?</p>
            <v-date-picker class="inline-block h-full" v-model="form.date" :model-config="modelConfig">
              <template v-slot="{ inputValue, inputEvents }">
                <input class="rounded border bg-white px-2 py-1" :value="inputValue" v-on="inputEvents" />
              </template>
            </v-date-picker>
          </div>

          <div class="border-l border-gray-200 p-5">
            <p class="mb-2 block text-sm">Who called?</p>

            <div class="flex">
              <div class="flex items-center mr-6">
                <input
                  id="one_time"
                  v-model="form.reminderChoice"
                  value="one_time"
                  name="reminder-frequency"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label for="one_time" class="ml-2 block cursor-pointer text-sm font-medium text-gray-700">
                  I called
                </label>
              </div>

              <div class="flex items-center">
                <input
                  id="one_time"
                  v-model="form.reminderChoice"
                  value="one_time"
                  name="reminder-frequency"
                  type="radio"
                  class="h-4 w-4 border-gray-300 text-sky-500" />
                <label for="one_time" class="ml-2 block cursor-pointer text-sm font-medium text-gray-700">
                  Lorraine called
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="border-b border-gray-200 p-5 flex">
            <div class="flex items-center mr-6">
              <input
                id="one_time"
                v-model="form.reminderChoice"
                value="one_time"
                name="reminder-frequency"
                type="radio"
                class="h-4 w-4 border-gray-300 text-sky-500" />
              <label for="one_time" class="ml-2 block cursor-pointer text-sm font-medium text-gray-700">
                This was an audio-only call
              </label>
            </div>

            <div class="flex items-center">
              <input
                id="one_time"
                v-model="form.reminderChoice"
                value="one_time"
                name="reminder-frequency"
                type="radio"
                class="h-4 w-4 border-gray-300 text-sky-500" />
              <label for="one_time" class="ml-2 block cursor-pointer text-sm font-medium text-gray-700">
                This was a video call
              </label>
            </div>
          </div>

        <div class="border-b border-gray-200 p-5">
          <!-- cta to add a title -->
          <span
            v-if="!titleFieldShown"
            class="mr-2 inline-block cursor-pointer rounded-lg border bg-slate-200 px-1 py-1 text-xs hover:bg-slate-300"
            @click="showTitleField">
            + add description
          </span>

          <!-- cta to add emotion -->
          <span
            v-if="!emotionFieldShown"
            class="inline-block cursor-pointer rounded-lg border bg-slate-200 px-1 py-1 text-xs hover:bg-slate-300"
            @click="showEmotionField">
            + add emotion
          </span>
        </div>
      </div>

      <div class="flex justify-between p-5">
        <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createCallModalShown = false" />
        <pretty-button :text="'Add date'" :state="loadingState" :icon="'plus'" :classes="'save'" />
      </div>
    </form>

    <!-- calls -->
    <ul v-if="localCalls.length > 0" class="mb-2 rounded-lg border border-gray-200 bg-white">
      <li v-for="call in localCalls" :key="call.id" class="item-list border-b border-gray-200 hover:bg-slate-50">
        <div v-if="editedCallId !== call.id" class="flex items-center justify-between p-3">
          <div>

            <!-- icon phone cancel -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
            </svg>

            <!-- call accepted -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
          </div>

          <hover-menu :show-edit="true" :show-delete="true" @edit="showUpdateCallModal(call)" @delete="destroy(call)" />
        </div>

        <!-- edit call -->
        <form v-if="editedCallId === call.id" class="bg-form" @submit.prevent="update(call)">
          <errors :errors="form.errors" />

          <div class="border-b border-gray-200 p-5">
            <text-input
              :ref="'update' + call.id"
              v-model="form.label"
              :label="'Title'"
              :type="'text'"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="editedCallId = 0" />
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="editedCallId = 0" />
            <pretty-button :text="'Update'" :state="loadingState" :icon="'check'" :classes="'save'" />
          </div>
        </form>
      </li>
    </ul>

    <!-- blank state -->
    <div v-if="localCalls.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
      <p class="p-5 text-center">There are no calls logged yet.</p>
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
  },

  data() {
    return {
      createCallModalShown: false,
      localCalls: [],
      loadingState: '',
      editedCallId: 0,
      form: {
        label: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localCalls = this.data.calls;
  },

  methods: {
    showCreateCallModal() {
      this.form.errors = [];
      this.form.label = '';
      this.createCallModalShown = true;

      this.$nextTick(() => {
        this.$refs.label.focus();
      });
    },

    showUpdateCallModal(task) {
      this.form.errors = [];
      this.form.label = task.label;
      this.editedCallId = task.id;

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
          this.flash('The call has been created', 'success');
          this.localCalls.unshift(response.data.data);
          this.createCallModalShown = false;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    update(call) {
      this.loadingState = 'loading';

      axios
        .put(call.url.update, this.form)
        .then((response) => {
          this.loadingState = '';
          this.flash('The call has been edited', 'success');
          this.localCalls[this.localCalls.findIndex((x) => x.id === call.id)] = response.data.data;
          this.editedCallId = 0;
        })
        .catch((error) => {
          this.loadingState = '';
          this.form.errors = error.response.data;
        });
    },

    destroy(call) {
      if (confirm('Are you sure?')) {
        axios
          .delete(call.url.destroy)
          .then((response) => {
            this.flash('The call has been deleted', 'success');
            var id = this.localCalls.findIndex((x) => x.id === call.id);
            this.localCalls.splice(id, 1);
          })
          .catch((error) => {
            this.form.errors = error.response.data;
          });
      }
    },
  },
};
</script>
