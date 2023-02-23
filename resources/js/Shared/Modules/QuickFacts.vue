<script setup>
import Loading from '@/Shared/Loading.vue';
import Errors from '@/Shared/Form/Errors.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref, nextTick } from 'vue';

const props = defineProps({
  data: Object,
});

const form = useForm({
  content: '',
});

const loading = ref(false);
const loadingState = ref(false);
const createQuickFactModalShown = ref(false);
const openState = ref(false);
const localQuickFacts = ref([]);
const localTemplate = ref([]);
const contentField = ref(null);

onMounted(() => {
  openState.value = props.data.show_quick_facts;
  localQuickFacts.value = props.data.quick_facts.quick_facts;
  localTemplate.value = props.data.quick_facts.template;
});

const toggle = () => {
  axios.put(props.data.url.toggle).then((response) => {
    openState.value = !openState.value;
  });
};

const showCreateQuickModal = () => {
  createQuickFactModalShown.value = true;
  form.content = '';

  nextTick(() => {
    contentField.value.focus();
  });
};

const get = (template) => {
  loading.value = true;
  localTemplate.value = template;

  axios
    .get(template.url.show)
    .then((response) => {
      localQuickFacts.value = response.data.data.quick_facts;
      loading.value = false;
    });
};

const store = () => {
  loadingState.value = 'loading';

  axios
    .post(localTemplate.value.url.store, form)
    .then((response) => {
      loadingState.value = '';
      createQuickFactModalShown.value = false;
      localQuickFacts.value.push(response.data.data);
    })
    .catch(() => {
      loadingState.value = '';
    });
};
</script>

<template>
  <div class="p-3 mb-8 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm bg-gray-50 dark:bg-gray-800">
    <div @click="toggle()" class="flex items-center justify-between cursor-pointer" :class="openState ? ' mb-4' : ''">
      <div class="flex items-center mr-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="relative inline h-4 w-4 mr-1">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-5.25m0 0a6.01 6.01 0 001.5-.189m-1.5.189a6.01 6.01 0 01-1.5-.189m3.75 7.478a12.06 12.06 0 01-4.5 0m3.75 2.383a14.406 14.406 0 01-3 0M14.25 18v-.192c0-.983.658-1.823 1.508-2.316a7.5 7.5 0 10-7.517 0c.85.493 1.509 1.333 1.509 2.316V18" />
        </svg>

        <p class="text-sm font-bold">Quick facts</p>
      </div>

      <!-- chevrons -->
      <div>
        <svg
          v-if="!openState"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="mr-2 h-4 w-4 text-gray-400 cursor-pointer">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>

        <svg
          v-else
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="mr-2 h-4 w-4 text-gray-400 cursor-pointer">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
      </div>
    </div>

    <div v-if="openState">
      <!-- templates -->
      <div class="flex mb-4">
        <ul class="list">
          <li v-for="template in data.templates" :key="template.id" class="inline mr-2">
            <span @click="get(template)" :class="localTemplate.id === template.id ? 'border border-gray-200 rounded bg-white font-semibold' : ''"  class="px-2 py-1 text-sm cursor-pointer">{{ template.label }}</span>
          </li>
        </ul>
      </div>

      <!-- content -->
      <ul v-if="localQuickFacts.length > 0 && !loading">
        <li v-for="quickFact in localQuickFacts" :key="quickFact.id" class="flex items-center mb-1">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-green-600 mr-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>

          <span>{{ quickFact.content }}</span>
        </li>
        <li>edit</li>
      </ul>

      <!-- blank state -->
      <div v-if="localQuickFacts.length == 0 && !loading && !createQuickFactModalShown" class="mb-2">
        <p class="px-5 pb-2 text-center">There are no quick facts here yet. <span @click="showCreateQuickModal" class="text-blue-500 hover:underline cursor-pointer">Add one now</span></p>
        <img src="/img/contact_blank_quick_facts.svg" :alt="$t('Activity feed')" class="mx-auto h-20 w-20" />
      </div>

      <!-- loading mode -->
      <div v-if="loading"  class="px-20 py-5 text-center">
        <Loading />
      </div>

      <!-- modal to create a quick fact -->
      <form
        v-if="createQuickFactModalShown"
        class="mb-6 rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900"
        @submit.prevent="store()">
        <div class="border-b border-gray-200 p-5 dark:border-gray-700">
          <errors :errors="form.errors" />

          <text-input
            ref="contentField"
            v-model="form.content"
            :label="'Content'"
            :type="'text'"
            :autofocus="true"
            :input-class="'block w-full'"
            :required="true"
            :autocomplete="false"
            :maxlength="255"
            @esc-key-pressed="createQuickFactModalShown = false" />
        </div>

        <div class="flex justify-between p-5">
          <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="createQuickFactModalShown = false" />
          <pretty-button :text="'Save'" :state="loadingState" :icon="'plus'" :classes="'save'" />
        </div>
      </form>

    </div>
  </div>
</template>

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
