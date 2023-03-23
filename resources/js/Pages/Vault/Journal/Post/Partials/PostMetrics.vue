<script setup>
import Errors from '@/Shared/Form/Errors.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import PrettySpan from '@/Shared/Form/PrettySpan.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted, ref, nextTick } from 'vue';

const props = defineProps({
  data: Object,
});

const form = useForm({
  journal_metric_id: '',
  label: '',
  value: '',
});

const loadingState = ref(false);
const localJournalMetrics = ref([]);
const journalMetricModal = ref(0);
const addModalShown = ref(false);

onMounted(() => {
  localJournalMetrics.value = props.data.journal_metrics;
});

const showAddMetricModal = (journalMetric) => {
  journalMetricModal.value = journalMetric.id;
  addModalShown.value = true;
  form.label = '';
  form.value = '';
};

const store = (journalMetric) => {
  loadingState.value = 'loading';
  form.journal_metric_id = journalMetric.id;

  axios
    .post(journalMetric.url.store, form)
    .then((response) => {
      loadingState.value = '';
      localJournalMetrics.value[localJournalMetrics.value.findIndex((x) => x.id === journalMetric.id)].post_metrics.push(response.data.data);
      addModalShown.value = false;
    })
    .catch(() => {
      loadingState.value = '';
    });
};

const update = () => {
  loadingState.value = 'loading';

  axios
    .put(props.data.url.slice_store, form)
    .then((response) => {
      editSlicesModalShown.value = false;
      loadingState.value = '';
      slice.value = response.data.data;
    })
    .catch(() => {
      loadingState.value = '';
    });
};

const reset = () => {
  form.slice_of_life_id = null;
  axios.delete(props.data.url.slice_reset, form).then(() => {
    editSlicesModalShown.value = false;
    slice.value = null;
  });
};
</script>

<template>
  <div class="mb-8">
    <p class="mb-2 flex items-center justify-between font-bold">
      <span>Post metrics</span>
    </p>

    <!-- journal metrics -->
    <div v-for="journalMetric in localJournalMetrics" :key="journalMetric.id" class="mb-3">
      <div class="flex">
        <div class="font-semibold">{{ journalMetric.label }}</div>
      </div>
      <ul v-if="journalMetric.post_metrics.length > 0" class="mb-2 rounded border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <li v-for="postMetric in journalMetric.post_metrics" :key="postMetric.id" class="flex items-center justify-between item-list px-3 py-1 border-b border-gray-200 hover:bg-slate-50 dark:border-gray-700 dark:bg-slate-900 hover:dark:bg-slate-800">
          <span>{{ postMetric.label }}</span>
          <span>{{ postMetric.value }}</span>
        </li>
      </ul>
      <p @click="showAddMetricModal(journalMetric)" v-if="!addModalShown" class="mb-6 text-sm text-blue-500 hover:underline cursor-pointer">+ add a new metric</p>

      <!-- modal to add a new post metric -->
      <div
        v-if="addModalShown && journalMetric.id === journalMetricModal"
        class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
        <form @submit.prevent="store(journalMetric)">
          <div class="border-b border-gray-200 p-2 dark:border-gray-700">
            <errors :errors="form.errors" />

            <text-input
              v-model="form.value"
              :autofocus="true"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="true"
              :type="'number'"
              :min="0"
              :max="1000000"
              :label="'Numerical value'"
              @esc-key-pressed="addModalShown = false" />

            <text-input
              v-model="form.label"
              :div-outer-class="'mb-5'"
              :input-class="'block w-full'"
              :required="false"
              :maxlength="255"
              :label="'More details'"
              @esc-key-pressed="addModalShown = false" />
          </div>

          <div class="flex justify-between p-2">
            <pretty-span :text="$t('app.cancel')" :classes="'mr-3'" @click="addModalShown = false" />
            <pretty-button
              :href="'data.url.vault.create'"
              :text="$t('app.save')"
              :state="loadingState"
              :icon="'check'"
              :classes="'save'" />
          </div>
        </form>
      </div>
    </div>

    <!-- blank state -->
    <p v-if="localJournalMetrics.length <= 0" class="text-sm text-gray-600 dark:text-gray-400">There are no journal metrics.</p>
  </div>
</template>

<style lang="scss" scoped>
.item-list {
  &:hover:first-child {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
  }

  &:last-child {
    border-bottom: 0;
  }

  &:hover:last-child {
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
  }
}
</style>
