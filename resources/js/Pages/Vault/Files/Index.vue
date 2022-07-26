<style lang="scss" scoped>
.file-list {
  li:hover:first-child {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
  }

  li:last-child {
    border-bottom: 0;
  }

  li:hover:last-child {
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
  }
}

.special-grid {
  grid-template-columns: 200px 1fr;
}

@media (max-width: 480px) {
  .special-grid {
    grid-template-columns: 1fr;
  }
}
</style>

<template>
  <layout :layout-data="layoutData" :inside-vault="true">
    <main class="relative sm:mt-24">
      <div class="mx-auto max-w-6xl px-2 py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="special-grid grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div>
            <!-- filters -->
            <div>
              <ul class="mb-4">
                <li class="border-l-2 border-orange-500 pl-2">
                  All files <span class="text-sm text-gray-500">(12)</span>
                </li>
              </ul>

              <p class="mb-2 pl-2 text-sm text-gray-500">Or filter by type</p>
              <ul>
                <li class="border-l-2 pl-2 hover:border-l-2 hover:border-orange-500">
                  Documents <span class="text-sm text-gray-500">(12)</span>
                </li>
                <li class="border-l-2 pl-2 hover:border-l-2 hover:border-orange-500">
                  Photos <span class="text-sm text-gray-500">(12)</span>
                </li>
                <li class="border-l-2 pl-2 hover:border-l-2 hover:border-orange-500">
                  Avatars <span class="text-sm text-gray-500">(12)</span>
                </li>
              </ul>
            </div>
          </div>

          <!-- right -->
          <div class="p-3 sm:px-3 sm:py-0">
            <!-- file list -->
            <ul class="file-list mb-6 rounded-lg border border-gray-200 bg-white">
              <li
                v-for="file in data.files"
                :key="file.id"
                class="flex items-center border-b border-gray-200 px-5 py-2 hover:bg-slate-50">
                <!-- created at -->
                <p class="mr-2 text-sm text-gray-400">{{ file.created_at }}</p>

                <!-- file name -->
                <p class="mr-4">
                  <span class="">{{ file.name }}</span>

                  <span class="ml-2 rounded border bg-blue-50 px-1 py-0 font-mono text-xs text-blue-500">
                    {{ file.size }}
                  </span>
                </p>

                <!-- avatar -->
                <div class="flex items-center">
                  <div v-html="file.contact.avatar" class="mr-2 h-5 w-5"></div>
                  <inertia-link :href="file.contact.url.show" class="text-blue-500 hover:underline">
                    {{ file.contact.name }}
                  </inertia-link>
                </div>
              </li>
            </ul>

            <!-- pagination -->
            <div v-if="!moduleMode" class="flex justify-between text-center">
              <inertia-link
                v-show="paginator.previousPageUrl"
                class="fl dib"
                :href="paginator.previousPageUrl"
                title="Previous">
                &larr; {{ $t('app.previous') }}
              </inertia-link>
              <inertia-link v-show="paginator.nextPageUrl" class="fr dib" :href="paginator.nextPageUrl" title="Next">
                {{ $t('app.next') }} &rarr;
              </inertia-link>
            </div>

            <!-- blank state -->
            <div v-if="data.files.length == 0" class="mb-6 rounded-lg border border-gray-200 bg-white">
              <p class="p-5 text-center">{{ $t('settings.notification_channels_blank') }}</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';
import PrettyLink from '@/Shared/Form/PrettyLink';

export default {
  components: {
    Layout,
    PrettyLink,
  },

  props: {
    layoutData: {
      type: Object,
      default: null,
    },
    paginator: {
      type: Object,
      default: null,
    },
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {};
  },

  methods: {},
};
</script>
