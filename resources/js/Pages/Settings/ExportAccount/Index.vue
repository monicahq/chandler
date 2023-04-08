<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/inertia-vue3';
import Button from '@/Components/Button.vue';
import JetSecondaryButton from '@/Components/Jetstream/SecondaryButton.vue';
import Layout from '@/Shared/Layout.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import DialogModal from '@/Components/Jetstream/DialogModal.vue';

defineProps({
  layoutData: null,
  data: null,
});

const form = useForm();
const showExport = ref(false);

const exportAccount = () => {
  form.post(route('settings.export.store'));

  showExport.value = false;
};

const download = (job) => {
  return axios.post(route('settings.export.download', { id: job.id }))
    .then((response) => {
      const filename = response.headers['content-disposition'].split('filename=')[1];
      const url = window.URL.createObjectURL(new Blob([JSON.stringify(response.data, null, 2)]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', filename);
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
};
</script>

<template>
  <layout :layout-data="layoutData">
    <Breadcrumb :items="[
    {
      name: $t('app.breadcrumb_settings'),
      url: data.url.settings
    }, {
      name: $t('Export account')
    }]" />

    <main class="relative sm:mt-24">
      <div class="mx-auto max-w-lg px-2 py-2 sm:py-6 sm:px-6 lg:px-8">

        <p>
          Export your account
        </p>

        <PrettyButton
                :text="'Export your account'"
                :state="loadingState"
                :icon="'check'"
                :classes="'save'" @click="showExport = true" />

        <table class="mt-4">
          <thead>
            <tr class="bg-slate-100 dark:bg-slate-700">
              <th class="px-5 py-2">
                {{ $t('Date') }}
              </th>
              <th class="px-5 py-2">
                {{ $t('Status') }}
              </th>
              <th class="px-5 py-2">
                {{ $t('Action') }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="data.jobs.length == 0">
              <td colspan="3">
                {{ $t('No export yet.') }}
              </td>
            </tr>

            <tr
              v-for="job in data.jobs"
              :key="job.id"
              class="odd:bg-white even:bg-slate-50 hover:bg-slate-100 dark:odd:bg-slate-900 dark:even:bg-slate-800 hover:dark:bg-slate-700">

              <td class="items-center justify-between px-5 py-2">
                {{ job.date }}
              </td>

              <td class="items-center justify-between px-5 py-2">
                {{ job.status }}
              </td>

              <td class="flex items-center justify-between px-5 py-2">
                <Link @click="download(job)" as="button" preserve-scroll>
                  {{ $t('Download') }}
                </Link>
              </td>

            </tr>
          </tbody>
        </table>

      </div>
    </main>

    <DialogModal :show="showExport">
      <template #title>
        {{ $t('Export your account') }}
      </template>

      <template #content>

      </template>

      <template #footer>
        <JetSecondaryButton @click="showExport = false">
          {{ $t('Cancel') }}
        </JetSecondaryButton>

        <Button
          class="ml-3"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          @click="exportAccount"
        >
          {{ $t('Export your account') }}
        </Button>
      </template>

    </DialogModal>
  </layout>
</template>
