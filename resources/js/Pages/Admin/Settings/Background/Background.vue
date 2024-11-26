<script setup>
import { useToast, POSITION } from "vue-toastification";
import { Link } from "@inertiajs/vue3";

import DashboardBase from "@/Pages/DashboardBase.vue";
const props = defineProps({
  taskRouters: Object,
  taskStats: Object,
  taskContract: String,
});
</script>
<style>
@media (min-width: 768px) {
  .container {
    display: flex;
    justify-content: space-evenly;
  }
}
</style>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Configuración de las tareas en segundo plano</h4>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div class="container">
        <div class="w-full">
          <Link
            :href="route('settings.background.edit', (task = 'ping-routers'))"
          >
            <div
              class="m-2 flex justify-center items-center p-4 md:p-8 bg-gray-200 hover:bg-gray-300 text-2xl rounded-md gap-2"
            >
              <span class="material-symbols-outlined" style="font-size: 30px">
                Router
              </span>

              <span>Pings automaticos</span>
            </div>
          </Link>
        </div>
        <div class="w-full">
          <Link
            :href="
              route('settings.background.edit', (task = 'check-contracts'))
            "
          >
            <div
              class="m-2 flex justify-center items-center p-4 md:p-8 bg-gray-200 hover:bg-gray-300 text-2xl rounded-md gap-2"
            >
              <span class="material-symbols-outlined" style="font-size: 30px">
                contract
              </span>

              <span>Verificación de contrato</span>
            </div>
          </Link>
        </div>
        <div class="w-full">
          <Link
            :href="route('settings.background.edit', (task = 'device-stats'))"
          >
            <div
              class="m-2 flex justify-center items-center p-4 md:p-8 bg-gray-200 hover:bg-gray-300 text-2xl rounded-md gap-2"
            >
              <span class="material-symbols-outlined" style="font-size: 30px">
                leaderboard
              </span>

              <span>Obtención de estadísticas</span>
            </div>
          </Link>
        </div>

        <div class="w-full">
          <Link
            :href="route('settings.background.edit', (task = 'backups'))"
          >
            <div
              class="m-2 flex justify-center items-center p-4 md:p-8 bg-gray-200 hover:bg-gray-300 text-2xl rounded-md gap-2"
            >
              <span class="material-symbols-outlined" style="font-size: 30px">
                database
              </span>

              <span>Backups automaticos</span>
            </div>
          </Link>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>