<style lang="scss" scoped>
.icon-sidebar {
  color: #737e8d;
  top: -2px;
}

.icon-note {
  top: -1px;
}
</style>

<template>
  <div class="mb-10">
    <!-- <h3 class="font-medium mb-3 border-b border-gray-200">
      <span class="relative">
        <svg class="icon-sidebar h-4 w-4 inline relative" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M6 6C6 5.44772 6.44772 5 7 5H17C17.5523 5 18 5.44772 18 6C18 6.55228 17.5523 7 17 7H7C6.44771 7 6 6.55228 6 6Z" fill="currentColor" /><path d="M6 10C6 9.44771 6.44772 9 7 9H17C17.5523 9 18 9.44771 18 10C18 10.5523 17.5523 11 17 11H7C6.44771 11 6 10.5523 6 10Z" fill="currentColor" /><path d="M7 13C6.44772 13 6 13.4477 6 14C6 14.5523 6.44771 15 7 15H17C17.5523 15 18 14.5523 18 14C18 13.4477 17.5523 13 17 13H7Z" fill="currentColor" /><path d="M6 18C6 17.4477 6.44772 17 7 17H11C11.5523 17 12 17.4477 12 18C12 18.5523 11.5523 19 11 19H7C6.44772 19 6 18.5523 6 18Z" fill="currentColor" /><path fill-rule="evenodd" clip-rule="evenodd" d="M2 4C2 2.34315 3.34315 1 5 1H19C20.6569 1 22 2.34315 22 4V20C22 21.6569 20.6569 23 19 23H5C3.34315 23 2 21.6569 2 20V4ZM5 3H19C19.5523 3 20 3.44771 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44771 3 5 3Z" fill="currentColor" />
        </svg>
      </span>

      Notes

      <pretty-button v-if="!createAddressTypeModalShown" :text="'Add an address type'" :icon="'plus'" @click="showAddressTypeModal" />
    </h3> -->

    <!-- title + cta -->
    <div class="sm:flex items-center justify-between pb-2 mb-3 border-b border-gray-200">
      <div>
        <span class="relative">
          <svg class="icon-sidebar h-4 w-4 inline relative" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M6 6C6 5.44772 6.44772 5 7 5H17C17.5523 5 18 5.44772 18 6C18 6.55228 17.5523 7 17 7H7C6.44771 7 6 6.55228 6 6Z" fill="currentColor" /><path d="M6 10C6 9.44771 6.44772 9 7 9H17C17.5523 9 18 9.44771 18 10C18 10.5523 17.5523 11 17 11H7C6.44771 11 6 10.5523 6 10Z" fill="currentColor" /><path d="M7 13C6.44772 13 6 13.4477 6 14C6 14.5523 6.44771 15 7 15H17C17.5523 15 18 14.5523 18 14C18 13.4477 17.5523 13 17 13H7Z" fill="currentColor" /><path d="M6 18C6 17.4477 6.44772 17 7 17H11C11.5523 17 12 17.4477 12 18C12 18.5523 11.5523 19 11 19H7C6.44772 19 6 18.5523 6 18Z" fill="currentColor" /><path fill-rule="evenodd" clip-rule="evenodd" d="M2 4C2 2.34315 3.34315 1 5 1H19C20.6569 1 22 2.34315 22 4V20C22 21.6569 20.6569 23 19 23H5C3.34315 23 2 21.6569 2 20V4ZM5 3H19C19.5523 3 20 3.44771 20 4V20C20 20.5523 19.5523 21 19 21H5C4.44772 21 4 20.5523 4 20V4C4 3.44772 4.44771 3 5 3Z" fill="currentColor" />
          </svg>
        </span>

        Notes
      </div>
      <pretty-button :text="'Add an address type'" :icon="'plus'" @click="showAddressTypeModal" />
    </div>

    <div class="border-gray-200 rounded border last:mb-0 mb-4">
      <div class="p-3">
        <p>Dolor aute adipisicing ad. Ullamco proident in occaecat. Ipsum aliquip amet reprehenderit ea sint anim deserunt et reprehenderit. Ullamco nostrud magna minim ut esse quis ea fugiat sint cillum tempor anim reprehenderit. Sunt nisi incididunt officia incididunt laborum ut cupidatat qui nisi amet. Enim duis esse eiusmod labore magna occaecat ex. Commodo fugiat adipisicing ipsum velit minim non mollit in fugiat ad eiusmod occaecat. Enim dolor culpa veniam tempor ad irure nulla sint. Ut sint nulla velit anim qui. Reprehenderit reprehenderit aliqua reprehenderit ipsum sunt incididunt.</p>
      </div>
      <div class="border-t border-gray-200 flex justify-between text-xs text-gray-600 px-3 py-2">
        <div>
          <!-- date -->
          <div class="inline mr-3 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline relative icon-note text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Sept 21, 2020
          </div>

          <!-- author -->
          <div class="inline mr-3 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline relative icon-note text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Regis Freyd
          </div>
        </div>
        <div>
          <hover-menu />
        </div>
      </div>
    </div>

    <div class="border-gray-200 rounded border last:mb-0 mb-4">
      <div class="p-3">
        <p>Dolor aute adipisicing ad. Ullamco proident in occaecat. Ipsum aliquip amet reprehenderit ea sint anim deserunt et reprehenderit. Ullamco nostrud magna minim ut esse quis ea fugiat sint cillum tempor anim reprehenderit. Sunt nisi incididunt officia incididunt laborum ut cupidatat qui nisi amet. Enim duis esse eiusmod labore magna occaecat ex. Commodo fugiat adipisicing ipsum velit minim non mollit in fugiat ad eiusmod occaecat. Enim dolor culpa veniam tempor ad irure nulla sint. Ut sint nulla velit anim qui. Reprehenderit reprehenderit aliqua reprehenderit ipsum sunt incididunt.</p>
      </div>
      <div class="border-t border-gray-200 flex justify-between text-xs text-gray-600 px-3 py-2">
        <div>
          <!-- date -->
          <div class="inline mr-3 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline relative icon-note text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
            Sept 21, 2020
          </div>

          <!-- author -->
          <div class="inline mr-3 relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline relative icon-note text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Regis Freyd
          </div>
        </div>
        <div>
          <hover-menu />
        </div>
      </div>
    </div>

    <p class="text-xs text-gray-400">Nov 09</p>
    <p class="mb-3 text-sm">Birthday John</p>
    <p class="text-xs text-gray-400">Nov 13</p>
    <p class="mb-3 text-sm">Wedding Henri</p>
  </div>
</template>

<script>
import HoverMenu from '@/Shared/HoverMenu';
import PrettyButton from '@/Shared/Form/PrettyButton';

export default {
  components: {
    HoverMenu,
    PrettyButton,
  },

  props: {
    data: {
      type: Object,
      default: null,
    },
  },

  data() {
    return {
    };
  },

  methods: {
  },
};
</script>
