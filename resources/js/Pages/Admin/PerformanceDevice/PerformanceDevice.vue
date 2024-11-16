<script setup>
import { onMounted, nextTick, onBeforeUnmount, ref } from "vue";
import Graphics from "../Components/GraphicDevice.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
const props = defineProps({
  device: {
    type: Object,
  },
  user: {
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
  totalRateDownload: {
    type: Object,
  },
  totalRateUpload: {
    type: Object,
  },
  totalByteDownload: {
    type: Object,
  },
  totalByteUpload: {
    type: Object,
  },
});

onMounted( () => {
  console.log(props.weekPerformance);
  console.log(props.todayPerformance);
});
</script>
<style>
  .graficas{
    max-width: 100%;
    display: flex;
    flex-direction: column;
  }
  .graphic-container{
    margin-top: 2rem;
    /* justify-content: center; */
    width: 100%;
  }
  .rate, .byte{
    display: flex;
    justify-content: center;
    flex-direction: column;
    width: 99%;
  }
  .frame-content{
    width: 14rem;
    height: 14rem;
  }
@media (min-width: 1450px) {
  .graficas{
    display: flex;
    flex-direction: row;
    width: 100%;
  }
  .rate, .byte{
    width: 50%;
    display: flex;
    flex-direction: column;
  }
  .graphic-container{
   display: block;
   width: 100%;

  }
  canvas{
    width: 50%;
  }
}
</style>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="text-center flex justify-between md:items-center md:gap-2">
        <div class="">
          <h2>Consumo y velocidad del dispositivo</h2>

          <p>IP: {{ device.address }}</p>

            <p v-if="user !== null && user?.name">Usuario: {{ user?.name }}</p>
            <p v-else>Usuario: Sin asignar</p>
        </div>

        
        <div>
          <span class="bg-cyan-500 py-1 px-2 text-white rounded-md">
            {{ device.device_internal_id }}
          </span>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div class="container">
        <div class="bg-white p-6 flex justify-center">
          <div>

            <div class="bg-green-500 flex justify-center rounded">
              <p class="text-xl font-semibold text-white">TOTALIZACIÓN</p>
            </div>
            <br>
            <div class="">
              <h3 class="text-xl font-semibold text-gray-800">Transferencia Total</h3>
              <p class="text-green-500 flex items-center text-lg">
                      <p class="font-bold mr-1">{{ (totalRateUpload/1024).toFixed(5) }}</p>
                      <span  class="font-bold mr-1">GB</span>
                      <p class="text-gray-500">Subida</p>
                    </p>
                    <p class="text-red-500 flex items-center text-lg">
                        <p class="font-bold mr-1">{{ (totalRateDownload/1024).toFixed(5) }}</p>
                        <span  class="font-bold mr-1">GB</span>
                        <p class="text-gray-500">Descarga</p>
                    </p>
                    <h3 class="text-xl font-semibold text-gray-800">Tráfico Total</h3>
              <p class="text-green-500 flex items-center text-lg">
                      <p class="font-bold mr-1">{{ (totalByteUpload/1024).toFixed(5) }}</p>
                      <span  class="font-bold mr-1">GB</span>
                      <p class="text-gray-500">Subida</p>
                    </p>
                    <p class="text-red-500 flex items-center text-lg">
                        <p class="font-bold mr-1">{{ (totalByteDownload/1024).toFixed(5) }}</p>
                        <span  class="font-bold mr-1">GB</span>
                        <p class="text-gray-500">Descarga</p>
                    </p>
            </div>
          </div>
        </div>

        <!-- Gráfica para Hoy -->
        <div class="graphic-container bg-white pt-5 rounded-lg">
          <div class="flex justify-center bg-red-500">
            <h3 class="text-xl font-semibold text-white"><strong>GRAFICAS</strong></h3>
          </div>
          <br>
          <div class="flex justify-center">
            
            <h3 class="text-xl font-semibold text-gray-800">HOY</h3>
          </div>
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
        <div class="graphic-container bg-white pt-5 rounded-lg">
          <div class="flex justify-center">

            <h3 class="text-xl font-semibold text-gray-800">SEMANALMENTE</h3>
          </div>
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
        <div class="graphic-container bg-white pt-5 rounded-lg">
          <div class="flex justify-center">

            <h3 class="text-xl font-semibold text-gray-800">MENSUALMENTE</h3>
          </div>
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
        <div class="graphic-container bg-white pt-5 rounded-lg">
          <div class="flex justify-center">

            <h3 class="text-xl font-semibold text-gray-800">ANUALMENTE</h3>
          </div>
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
