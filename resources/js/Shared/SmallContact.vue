<style lang="scss" scoped></style>

<template>
  <div class="relative inline" :style="'top: ' + top">
    <a-popover placement="bottomLeft">
      <template #content>
        <div class="flex">
          <img
            class="mr-2 inline rounded-full"
            src="https://ucarecdn.com/2f86ee48-9258-4b5c-aaf5-82269edcf770/-/scale_crop/45x45/smart/"
            alt="" />
          <div>
            <p>Last talked to: Apr 20, 2012</p>
            <p>Last activity: Apr 20, 2012</p>
          </div>
        </div>
      </template>
      <template v-if="showName" #title>
        <span>{{ contact.name }}</span>
      </template>
      <div class="inline-flex items-center text-sm">
        <img
          class="inline rounded-full"
          :class="showName ? 'mr-2' : ''"
          :src="'https://ucarecdn.com/2f86ee48-9258-4b5c-aaf5-82269edcf770/-/scale_crop/' + previewContactSize + 'x' + previewContactSize + '/smart/'"
          alt="" />
        <a v-if="showName" class="colored-link" href="">{{ contact.name }}</a>
      </div>
    </a-popover>
  </div>
</template>

<script>
export default {
  components: {},

  props: {
    top: {
      type: String,
      default: '0px',
    },
    contact: {
      type: Object,
      default: null,
    },
    previewContactSize: {
      type: Number,
      default: 18,
    },
    showName: {
      type: Boolean,
      default: true,
    }
  },
};
</script>
