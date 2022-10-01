<script setup>
import Layout from '@/Shared/Layout.vue';
import PrettyButton from '@/Shared/Form/PrettyButton.vue';
import TextInput from '@/Shared/Form/TextInput.vue';
import TextArea from '@/Shared/Form/TextArea.vue';
import { useForm } from '@inertiajs/inertia-vue3';
import { onMounted } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
});

const form = useForm({
  title: '',
  sections: [],
});

onMounted(() => {
  form.title = props.data.title;

  props.data.sections.forEach((section) => {
    form.sections.push({
      id: section.id,
      label: section.label,
      content: section.content,
    });
  });
});

const update = () => {
  axios
    .put(props.data.url.update, form)
    .then(() => {
      console.log('done');
    })
    .catch(() => {});
};
</script>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <!-- breadcrumb -->
    <nav class="bg-white dark:bg-gray-900 sm:mt-20 sm:border-b">
      <div class="max-w-8xl mx-auto hidden px-4 py-2 sm:px-6 md:block">
        <div class="flex items-baseline justify-between space-x-6">
          <ul class="text-sm">
            <li class="mr-2 inline text-gray-600 dark:text-gray-400">
              {{ $t('app.breadcrumb_location') }}
            </li>
            <li class="mr-2 inline">
              <inertia-link :href="layoutData.vault.url.journals" class="text-blue-500 hover:underline">
                {{ $t('app.breadcrumb_journal_index') }}
              </inertia-link>
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
            <li class="inline">
              <inertia-link :href="data.url.back" class="text-blue-500 hover:underline">
                {{ data.journal.name }}
              </inertia-link>
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
            <li class="inline">
              {{ $t('app.breadcrumb_post_create_template') }}
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <main class="relative sm:mt-16">
      <div class="mx-auto max-w-6xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="special-grid grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="">
            <form class="bg-form mb-6 rounded-lg border border-gray-200 dark:border-gray-700 dark:bg-gray-900">
              <div class="border-gray-200 p-5 dark:border-gray-700">
                <!-- title -->
                <text-input
                  :ref="'newTitle'"
                  v-model="form.title"
                  :label="'Title'"
                  :type="'text'"
                  :input-class="'block w-full mb-6'"
                  :required="true"
                  :autocomplete="false"
                  :maxlength="255"
                  @esc-key-pressed="createNoteModalShown = false" />

                <div v-for="section in form.sections" :key="section.id">
                  <text-area
                    v-model="section.content"
                    :label="section.label"
                    :rows="10"
                    :required="true"
                    :maxlength="65535"
                    :textarea-class="'block w-full mb-8'" />
                </div>
              </div>
            </form>
          </div>

          <!-- right -->
          <div class="">
            <!-- Publish action -->
            <div class="mb-2 rounded-lg border border-gray-200 text-center dark:border-gray-700 dark:bg-gray-900">
              <div class="border-b border-gray-200 p-2 text-sm dark:border-gray-700">Post status: draft</div>

              <div class="bg-form rounded-b-lg p-5">
                <pretty-button
                  @click="update()"
                  :text="'Publish'"
                  :state="loadingState"
                  :icon="'check'"
                  :classes="'save'" />
              </div>
            </div>

            <!-- auto save -->
            <div class="mb-6 flex items-center justify-center text-sm">
              <svg
                class="mr-2 h-4 w-4 text-green-700"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor">
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
              </svg>

              <span>Auto saved a few seconds ago</span>
            </div>

            <!-- contacts -->
            <p class="mb-2 flex items-center font-bold">
              <span>Contacts in this post</span>
            </p>
            <div class="bg-form mb-6 rounded-lg border border-gray-200 p-5 dark:border-gray-700 dark:bg-gray-900">
              This post is about
            </div>

            <!-- categories -->
            <p class="mb-2 flex items-center font-bold">
              <span>Categories</span>
            </p>
            <div class="mb-6">Jeux videos</div>

            <!-- stats -->
            <p class="mb-2 font-bold">Statistics</p>
            <ul class="mb-6 text-sm">
              <li class="mb-2 flex items-center">
                <svg
                  class="mr-2 h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                </svg>

                <span>3432 words</span>
              </li>
              <li class="mb-2 flex items-center">
                <svg
                  class="mr-2 h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <span>21min reading time</span>
              </li>
              <li class="flex items-center">
                <svg
                  class="mr-2 h-4 w-4"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>

                <span>Read 21 times</span>
              </li>
            </ul>

            <!-- delete -->
            <div class="cursor-pointer text-red-500 hover:text-red-900">{{ $t('app.delete') }}</div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
.special-grid {
  grid-template-columns: 1fr 300px;
}

@media (max-width: 480px) {
  .special-grid {
    grid-template-columns: 1fr;
  }
}
</style>
