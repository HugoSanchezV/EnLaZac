<script setup>
import Graphics from "../Components/GraphicDevice.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
const props = defineProps({
  device: {
    type: Object,
  },
  todayPerformance: {
    type: Object,
  },
  weekPerformance: {
    type: Object,
  },
  monthPerformance: {
    type: Object,
  },
  yearPerformance: {
    type: Object,
  },
});
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="text-center flex justify-between md:items-center md:gap-2">
        <h2>Consumo y velocidad del dispositivo</h2>
        <span class="bg-cyan-500 py-1 px-2 text-white rounded-md">
          {{ device.device_internal_id }}
        </span>
      </div>
    </template>

    <template v-slot:content>
      <div class="container">
        <!-- Gráfica para Hoy -->
        <div class="graphic">
          <Graphics
            :target="todayPerformance.labels"
            :upload_rate="todayPerformance.rateUpload"
            :download_rate="todayPerformance.rateDownload"
            :upload_byte="todayPerformance.byteUpload"
            :download_byte="todayPerformance.byteDownload"
            type="Today"
          />
        </div>

        <!-- Gráfica para Semana -->
        <div class="graphic">
          <Graphics
            :target="weekPerformance.labels"
            :upload_rate="weekPerformance.rateUpload"
            :download_rate="weekPerformance.rateDownload"
            :upload_byte="weekPerformance.byteUpload"
            :download_byte="weekPerformance.byteDownload"
            type="Week"
          />
        </div>

        <!-- Gráfica para Mes -->
        <div class="graphic">
          <Graphics
            :target="monthPerformance.labels"
            :upload_rate="monthPerformance.rateUpload"
            :download_rate="monthPerformance.rateDownload"
            :upload_byte="monthPerformance.byteUpload"
            :download_byte="monthPerformance.byteDownload"
            type="Month"
          />
        </div>

        <!-- Gráfica para Año -->
        <div class="graphic">
          <Graphics
            :target="yearPerformance.labels"
            :upload_rate="yearPerformance.rateUpload"
            :download_rate="yearPerformance.rateDownload"
            :upload_byte="yearPerformance.byteUpload"
            :download_byte="yearPerformance.byteDownload"
            type="Year"
          />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
