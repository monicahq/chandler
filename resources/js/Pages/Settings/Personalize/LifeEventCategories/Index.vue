<style lang="scss" scoped>
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
  <layout :layout-data="layoutData">
    <!-- breadcrumb -->
    <nav class="bg-white sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600">You are here:</li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.settings" class="text-blue-500 hover:underline">Settings</inertia-link>
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="data.url.personalize" class="text-blue-500 hover:underline"
                >Personalize your account</inertia-link
              >
            </li>
            <li class="relative mr-2 inline">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="icon-breadcrumb relative inline h-3 w-3"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Life event categories</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-20">
      <div class="mx-auto max-w-3xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="mb-6 mt-8 items-center justify-between sm:mt-0 sm:flex">
          <h3 class="mb-4 sm:mb-0"><span class="mr-1"> 🚲 </span> All the life event categories</h3>
          <pretty-button
            v-if="!createLifeEventCategoryModalShown"
            :text="'Add a new life event category'"
            :icon="'plus'"
            @click="showLifeEventCategoryModal" />
        </div>

        <!-- modal to create an activity type -->
        <form
          v-if="createLifeEventCategoryModalShown"
          class="mb-6 rounded-lg border border-gray-200 bg-white"
          @submit.prevent="submitLifeEventCategory()">
          <div class="border-b border-gray-200 p-5">
            <errors :errors="form.errors" />

            <text-input
              :ref="'newLifeEventCategory'"
              v-model="form.lifeEventCategoryName"
              :label="'Name of the life event category'"
              :type="'text'"
              :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createLifeEventCategoryModalShown = false" />
          </div>

          <div class="flex justify-between p-5">
            <pretty-span :text="'Cancel'" :classes="'mr-3'" @click="createLifeEventCategoryModalShown = false" />
            <pretty-button :text="'Save'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>

        <!-- list of life event categories -->
        <ul v-if="localLifeEventCategories.length > 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <li v-for="lifeEventCategory in localLifeEventCategories" :key="lifeEventCategory.id">
            <!-- detail of the life event category -->
            <div
              v-if="renameLifeEventCategoryModalShownId != lifeEventCategory.id"
              class="item-list flex items-center justify-between border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
              <span class="text-base font-semibold">{{ lifeEventCategory.label }}</span>

              <!-- actions -->
              <ul class="text-sm">
                <li
                  class="inline cursor-pointer text-blue-500 hover:underline"
                  @click="renameLifeEventCategoryModal(lifeEventCategory)">
                  Rename
                </li>
                <li
                  class="ml-4 inline cursor-pointer text-red-500 hover:text-red-900"
                  @click="destroyLifeEventCategory(lifeEventCategory)">
                  Delete
                </li>
              </ul>
            </div>

            <!-- rename an activity type modal -->
            <form
              v-if="renameLifeEventCategoryModalShownId == lifeEventCategory.id"
              class="item-list border-b border-gray-200 hover:bg-slate-50"
              @submit.prevent="updateLifeEventCategory(lifeEventCategory)">
              <div class="border-b border-gray-200 p-5">
                <errors :errors="form.errors" />

                <text-input
                  :ref="'rename' + lifeEventCategory.id"
                  v-model="form.lifeEventCategoryName"
                  :label="'Name of the new group type'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="renameLifeEventCategoryModalShownId = 0" />
              </div>

              <div class="flex justify-between p-5">
                <pretty-span
                  :text="'Cancel'"
                  :classes="'mr-3'"
                  @click.prevent="renameLifeEventCategoryModalShownId = 0" />
                <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>

            <!-- list of activities -->
            <div
              v-for="activity in lifeEventCategory.life_event_types"
              :key="activity.id"
              class="border-b border-gray-200 hover:bg-slate-50">
              <div
                v-if="renameLifeEventTypeModalId != activity.id"
                class="flex items-center justify-between px-5 py-2 pl-6">
                <span>{{ activity.label }}</span>

                <!-- actions -->
                <ul class="text-sm">
                  <li
                    class="inline cursor-pointer text-blue-500 hover:underline"
                    @click="renameLifeEventTypeModal(activity)">
                    Rename
                  </li>
                  <li
                    class="ml-4 inline cursor-pointer text-red-500 hover:text-red-900"
                    @click="destroyLifeEventType(lifeEventCategory, activity)">
                    Delete
                  </li>
                </ul>
              </div>

              <!-- rename the activity modal -->
              <form
                v-if="renameLifeEventTypeModalId == activity.id"
                class="item-list border-b border-gray-200 hover:bg-slate-50"
                @submiLifeEventType.prevent="updateActivity(lifeEventCategory, activity)">
                <div class="border-b border-gray-200 p-5">
                  <errors :errors="form.errors" />

                  <text-input
                    :ref="'rename' + activity.id"
                    v-model="form.label"
                    :label="'Label'"
                    :type="'text'"
                    :autofocus="true"
                    :input-class="'block w-full'"
                    :required="true"
                    :div-outer-class="'mb-4'"
                    :placeholder="'Wish good day'"
                    :autocomplete="false"
                    :maxlength="255"
                    @esc-key-pressed="renameLifeEventTypeModalId = 0" />
                </div>

                <div class="flex justify-between p-5">
                  <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="renameLifeEventTypeModalId = 0" />
                  <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
                </div>
              </form>
            </div>

            <!-- create a new activity -->
            <div
              v-if="createLifeEventCategoryModalId != lifeEventCategory.id"
              class="item-list border-b border-gray-200 px-5 py-2 pl-6 hover:bg-slate-50">
              <span
                class="cursor-pointer text-sm text-blue-500 hover:underline"
                @click="showLifeEventTypeModal(lifeEventCategory)"
                >Add a new activity</span
              >
            </div>

            <!-- create an activity -->
            <form
              v-if="createLifeEventCategoryModalId == lifeEventCategory.id"
              class="item-list border-b border-gray-200 hover:bg-slate-50"
              @submit.prevent="storeLifeEventType(lifeEventCategory)">
              <div class="border-b border-gray-200 p-5">
                <errors :errors="form.errors" />

                <text-input
                  :ref="'newLifeEventType'"
                  v-model="form.label"
                  :label="'Label'"
                  :type="'text'"
                  :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :placeholder="'Parent'"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="createLifeEventCategoryModalId = 0" />
              </div>

              <div class="flex justify-between p-5">
                <pretty-span :text="'Cancel'" :classes="'mr-3'" @click.prevent="createLifeEventCategoryModalId = 0" />
                <pretty-button :text="'Add'" :state="loadingState" :icon="'plus'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>

        <!-- blank state -->
        <div v-if="localLifeEventCategories.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
          <p class="p-5 text-center">
            Life events let you document what happened in your life, or the rich life of your contacts.
          </p>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyButton from '@/Shared/Form/PrettyButton';
import PrettySpan from '@/Shared/Form/PrettySpan';
import TextInput from '@/Shared/Form/TextInput';
import Errors from '@/Shared/Form/Errors';

export default {
  components: {
    Layout,
    PrettyButton,
    PrettySpan,
    TextInput,
    Errors,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      createLifeEventCategoryModalShown: false,
      renameLifeEventCategoryModalShownId: 0,
      createLifeEventCategoryModalId: 0,
      renameLifeEventTypeModalId: 0,
      localLifeEventCategories: [],
      form: {
        lifeEventCategoryName: '',
        label: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localLifeEventCategories = this.data.life_event_categories;
  },

  methods: {
    showLifeEventCategoryModal() {
      this.form.lifeEventCategoryName = '';
      this.createLifeEventCategoryModalShown = true;

      this.$nextTick(() => {
        this.$refs.newLifeEventCategory.focus();
      });
    },

    renameLifeEventCategoryModal(lifeEventCategory) {
      this.form.lifeEventCategoryName = lifeEventCategory.label;
      this.renameLifeEventCategoryModalShownId = lifeEventCategory.id;

      this.$nextTick(() => {
        this.$refs[`rename${lifeEventCategory.id}`].focus();
      });
    },

    showLifeEventTypeModal(lifeEventCategory) {
      this.createLifeEventCategoryModalId = lifeEventCategory.id;
      this.form.label = '';

      this.$nextTick(() => {
        this.$refs.newLifeEventType.focus();
      });
    },

    renameLifeEventTypeModal(type) {
      this.form.label = type.label;
      this.renameLifeEventTypeModalId = type.id;

      this.$nextTick(() => {
        this.$refs[`rename${type.id}`].focus();
      });
    },

    submitLifeEventCategory() {
      this.loadingState = 'loading';

      axios
        .post(this.data.url.activity_type_store, this.form)
        .then((response) => {
          this.flash('The life event category has been created', 'success');
          this.localLifeEventCategories.unshift(response.data.data);
          this.loadingState = null;
          this.createLifeEventCategoryModalShown = false;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    updateLifeEventCategory(lifeEventCategory) {
      this.loadingState = 'loading';

      axios
        .put(lifeEventCategory.url.update, this.form)
        .then((response) => {
          this.flash('The life event category has been updated', 'success');
          this.localLifeEventCategories[this.localLifeEventCategories.findIndex((x) => x.id === lifeEventCategory.id)] =
            response.data.data;
          this.loadingState = null;
          this.renameLifeEventCategoryModalShownId = 0;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroyLifeEventCategory(lifeEventCategory) {
      if (
        confirm(
          'Are you sure? This will delete all The life event categorys of this type for all the activities that were using it.',
        )
      ) {
        axios
          .delete(lifeEventCategory.url.destroy)
          .then((response) => {
            this.flash('The life event category has been deleted', 'success');
            var id = this.localLifeEventCategories.findIndex((x) => x.id === lifeEventCategory.id);
            this.localLifeEventCategories.splice(id, 1);
          })
          .catch((error) => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
      }
    },

    storeLifeEventType(lifeEventCategory) {
      this.loadingState = 'loading';

      axios
        .post(lifeEventCategory.url.store, this.form)
        .then((response) => {
          this.flash('The activity has been created', 'success');
          this.loadingState = null;
          this.createLifeEventCategoryModalId = 0;
          var id = this.localLifeEventCategories.findIndex((x) => x.id === lifeEventCategory.id);
          this.localLifeEventCategories[id].life_event_types.unshift(response.data.data);
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    updateLifeEventType(lifeEventCategory, activity) {
      this.loadingState = 'loading';

      axios
        .put(activity.url.update, this.form)
        .then((response) => {
          this.flash('The activity has been updated', 'success');
          this.loadingState = null;
          this.renameLifeEventTypeModalId = 0;
          var lifeEventCategoryId = this.localLifeEventCategories.findIndex((x) => x.id === lifeEventCategory.id);
          var typeId = this.localLifeEventCategories[lifeEventCategoryId].life_event_types.findIndex(
            (x) => x.id === activity.id,
          );
          this.localLifeEventCategories[lifeEventCategoryId].life_event_types[typeId] = response.data.data;
        })
        .catch((error) => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroyLifeEventType(lifeEventCategory, activity) {
      if (
        confirm(
          'Are you sure? This will delete all the life events of this type for all the contacts that were using it.',
        )
      ) {
        axios
          .delete(activity.url.destroy)
          .then((response) => {
            this.flash('The activity has been deleted', 'success');
            var lifeEventCategoryId = this.localLifeEventCategories.findIndex((x) => x.id === lifeEventCategory.id);
            var typeId = this.localLifeEventCategories[lifeEventCategoryId].life_event_types.findIndex(
              (x) => x.id === activity.id,
            );
            this.localLifeEventCategories[lifeEventCategoryId].life_event_types.splice(typeId, 1);
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
