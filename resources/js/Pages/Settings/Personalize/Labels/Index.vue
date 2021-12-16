<style lang="scss" scoped>
.section-head {
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
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
  <Layout :layoutData="layoutData">
    <!-- breadcrumb -->
    <nav class="sm:border-b bg-white">
      <div class="max-w-8xl mx-auto px-4 sm:px-6 py-2 hidden md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="inline mr-2 text-gray-600">You are here:</li>
            <li class="inline mr-2">
              <Link :href="data.url.settings" class="text-sky-500 hover:text-blue-900">Settings</Link>
            </li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline mr-2"><Link :href="data.url.personalize" class="text-sky-500 hover:text-blue-900">Personalize your account</Link></li>
            <li class="inline mr-2 relative">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 inline relative icon-breadcrumb" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </li>
            <li class="inline">Labels</li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="sm:mt-20 relative">
      <div class="max-w-3xl mx-auto px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <!-- title + cta -->
        <div class="sm:flex items-center justify-between mb-6 sm:mt-0 mt-8">
          <h3 class="mb-4 sm:mb-0"><span class="mr-1">🏷</span> All the labels used in the account</h3>
          <pretty-button @click="showlabelModal" v-if="!createlabelModalShown" :text="'Add a new group type'" :icon="'plus'" />
        </div>

        <!-- modal to create a new group type -->
        <form v-if="createlabelModalShown" @submit.prevent="submitlabel()" class="bg-white border border-gray-200 rounded-lg mb-6">
          <div class="p-5 border-b border-gray-200">
            <errors :errors="form.errors" />

            <text-input v-model="form.labelName"
              :label="'Name of the new group type'"
              :type="'text'" :autofocus="true"
              :input-class="'block w-full'"
              :required="true"
              :ref="'newlabel'"
              :autocomplete="false"
              :maxlength="255"
              @esc-key-pressed="createlabelModalShown = false" />
          </div>

          <div class="p-5 flex justify-between">
            <pretty-link @click="createlabelModalShown = false" :text="'Cancel'" :classes="'mr-3'" />
            <pretty-button :text="'Create group type'" :state="loadingState" :icon="'plus'" :classes="'save'" />
          </div>
        </form>

        <!-- list of groups types -->
        <ul v-if="localLabels.length > 0" class="bg-white border border-gray-200 rounded-lg mb-6">
          <li v-for="label in localLabels" :key="label.id">
            <!-- detail of the group type -->
            <div v-if="renamelabelModalShownId != label.id" class="flex justify-between items-center px-5 py-2 border-b border-gray-200 hover:bg-slate-50 item-list">
              <span class="text-base font-semibold">{{ label.name }}</span>

              <!-- actions -->
              <ul class="text-sm">
                <li @click="renamelabelModal(label)" class="cursor-pointer inline mr-4 text-sky-500 hover:text-blue-900">Rename</li>
                <li @click="destroylabel(label)" class="cursor-pointer inline text-red-500 hover:text-red-900">Delete</li>
              </ul>
            </div>

            <!-- rename a group type modal -->
            <form v-if="renamelabelModalShownId == label.id" @submit.prevent="updatelabel(label)" class="border-b border-gray-200 hover:bg-slate-50 item-list">
              <div class="p-5 border-b border-gray-200">
                <errors :errors="form.errors" />

                <text-input v-model="form.labelName"
                  :label="'Name of the new group type'"
                  :type="'text'" :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :ref="'rename' + label.id"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="renamelabelModalShownId = 0" />
              </div>

              <div class="p-5 flex justify-between">
                <pretty-span @click.prevent="renamelabelModalShownId = 0" :text="'Cancel'" :classes="'mr-3'" />
                <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
              </div>
            </form>

            <!-- list of relationship types -->
            <div v-for="type in label.types" :key="type.id" class="px-5 py-2 border-b border-gray-200 hover:bg-slate-50 pl-6">

              <!-- detail of the relationship type -->
              <div v-if="renameRelationshipTypeModalId != type.id" class="flex justify-between items-center">
                <div class="relative">
                  <!-- relation type name -->
                  <span>{{ type.name }}</span>

                  <svg xmlns="http://www.w3.org/2000/svg" class="px-1 h-5 w-5 inline relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                  </svg>

                  <!-- relation type reverse name -->
                  <span>{{ type.name_reverse_relationship }}</span>
                </div>

                <!-- actions -->
                <ul class="text-sm">
                  <li @click="renameRelationTypeModal(type)" class="cursor-pointer inline mr-4 text-sky-500 hover:text-blue-900">Rename</li>
                  <li @click="destroyRelationshipType(label, type)" class="cursor-pointer inline text-red-500 hover:text-red-900">Delete</li>
                </ul>
              </div>

              <!-- rename the relationship type modal -->
              <form v-if="renameRelationshipTypeModalId == type.id" @submit.prevent="updateRelationType(label, type)" class="border-b border-gray-200 hover:bg-slate-50 item-list">
                <div class="p-5 border-b border-gray-200">
                  <errors :errors="form.errors" />

                  <text-input v-model="form.name"
                    :label="'Name of the relationship'"
                    :type="'text'" :autofocus="true"
                    :input-class="'block w-full'"
                    :required="true"
                    :ref="'rename' + type.id"
                    :div-outer-class="'mb-4'"
                    :placeholder="'Parent'"
                    :autocomplete="false"
                    :maxlength="255"
                    @esc-key-pressed="renameRelationshipTypeModalId = 0" />

                  <text-input v-model="form.nameReverseRelationship"
                    :label="'Name of the reverse relationship'"
                    :type="'text'" :autofocus="true"
                    :input-class="'block w-full'"
                    :required="true"
                    :autocomplete="false"
                    :placeholder="'Child'"
                    :maxlength="255"
                    @esc-key-pressed="renameRelationshipTypeModalId = 0" />
                </div>

                <div class="p-5 flex justify-between">
                  <pretty-span @click.prevent="renameRelationshipTypeModalId = 0" :text="'Cancel'" :classes="'mr-3'" />
                  <pretty-button :text="'Rename'" :state="loadingState" :icon="'check'" :classes="'save'" />
                </div>
              </form>

            </div>

            <!-- create a new relationship type line -->
            <div v-if="createRelationshipTypeModalId != label.id" class="px-5 py-2 border-b border-gray-200 hover:bg-slate-50 pl-6 item-list">
              <span @click="showRelationshipTypeModal(label)" class="text-sky-500 hover:text-blue-900 text-sm cursor-pointer">Add a new relationship type</span>
            </div>

            <!-- create a new relationship type -->
            <form v-if="createRelationshipTypeModalId == label.id" @submit.prevent="storeRelationshipType(label)" class="border-b border-gray-200 hover:bg-slate-50 item-list">
              <div class="p-5 border-b border-gray-200">
                <errors :errors="form.errors" />

                <text-input v-model="form.name"
                  :label="'Name of the relationship'"
                  :type="'text'" :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :ref="'newRelationshipType'"
                  :div-outer-class="'mb-4'"
                  :placeholder="'Parent'"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="createRelationshipTypeModalId = 0" />

                <text-input v-model="form.nameReverseRelationship"
                  :label="'Name of the reverse relationship'"
                  :type="'text'" :autofocus="true"
                  :input-class="'block w-full'"
                  :required="true"
                  :autocomplete="false"
                  :placeholder="'Child'"
                  :maxlength="255"
                  @esc-key-pressed="createRelationshipTypeModalId = 0" />
              </div>

              <div class="p-5 flex justify-between">
                <pretty-span @click.prevent="createRelationshipTypeModalId = 0" :text="'Cancel'" :classes="'mr-3'" />
                <pretty-button :text="'Add'" :state="loadingState" :icon="'plus'" :classes="'save'" />
              </div>
            </form>
          </li>
        </ul>

        <!-- blank state -->
        <div v-if="localLabels.length == 0" class="bg-white border border-gray-200 rounded-lg mb-6">
          <p class="p-5 text-center">Labels let you classify contacts using a system that matters to you.</p>
        </div>
      </div>
    </main>
  </Layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import { Link } from '@inertiajs/inertia-vue3';
import PrettyButton from '@/Shared/PrettyButton';
import PrettyLink from '@/Shared/PrettyLink';
import PrettySpan from '@/Shared/PrettySpan';
import TextInput from '@/Shared/TextInput';
import Errors from '@/Shared/Errors';

export default {
  components: {
    Layout,
    Link,
    PrettyButton,
    PrettyLink,
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
      createlabelModalShown: false,
      renamelabelModalShownId: 0,
      localLabels: [],
      form: {
        name: '',
        description: '',
        errors: [],
      },
    };
  },

  mounted() {
    this.localLabels = this.data.labels;
  },

  methods: {
    showlabelModal() {
      this.form.labelName = '';
      this.createlabelModalShown = true;

      this.$nextTick(() => {
        this.$refs.newlabel.focus();
      });
    },

    renamelabelModal(label) {
      this.form.labelName = label.name;
      this.renamelabelModalShownId = label.id;

      this.$nextTick(() => {
        this.$refs[`rename${label.id}`].focus();
      });
    },

    submit() {
      this.loadingState = 'loading';

      axios.post(this.data.url.label_store, this.form)
        .then(response => {
          this.flash('The label has been created', 'success');
          this.localLabels.unshift(response.data.data);
          this.loadingState = null;
          this.createlabelModalShown = false;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    update(label) {
      this.loadingState = 'loading';

      axios.put(label.url.update, this.form)
        .then(response => {
          this.flash('The label has been updated', 'success');
          this.localLabels[this.localLabels.findIndex(x => x.id === label.id)] = response.data.data;
          this.loadingState = null;
          this.renamelabelModalShownId = 0;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = error.response.data;
        });
    },

    destroy(label) {
      if(confirm("Are you sure? This will remove the labels from all contacts, but won't delete the contacts themselves.")) {

        axios.delete(label.url.destroy)
          .then(response => {
            this.flash('The label has been deleted', 'success');
            var id = this.localLabels.findIndex(x => x.id === label.id);
            this.localLabels.splice(id, 1);
          })
          .catch(error => {
            this.loadingState = null;
            this.form.errors = error.response.data;
          });
        }
    },
  },
};
</script>
