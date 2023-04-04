<script setup>
import Layout from '@/Shared/Layout.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
  layoutData: Object,
  data: Object,
});
</script>

<template>
  <layout title="Calendar" :inside-vault="true" :layout-data="layoutData">
    <main class="relative sm:mt-24">
      <div class="max-w-8xl mx-auto py-2 sm:py-6 sm:px-6 lg:px-8">
        <div class="special-grid grid grid-cols-1 gap-6 sm:grid-cols-3">
          <!-- left -->
          <div class="p-3 sm:p-0">
            left
          </div>

          <!-- middle -->
          <div class="p-3 sm:p-0">
            <div v-for="week in data.weeks" :key="week.id" class="grid grid-cols-7 rounded-lg border-t border-l border-r last:border-b">
              <div v-for="day in week.days" :key="day.id" class="border-r last:border-r-0 h-32 p-2" :class="day.is_in_month ? '' : 'bg-slate-50'">
                <span class="text-xs p-1" :class="day.current_day ? 'rounded-lg bg-slate-200' : ''">{{ day.date }}</span>
              </div>
            </div>
          </div>

          <!-- right -->
          <div class="p-3 sm:p-0">
            sda
          </div>
        </div>
      </div>
    </main>
  </layout>
</template>

<style lang="scss" scoped>
.special-grid {
  grid-template-columns: 200px 1fr 200px;
}

@media (max-width: 480px) {
  .special-grid {
    grid-template-columns: 1fr;
  }
}
</style>
