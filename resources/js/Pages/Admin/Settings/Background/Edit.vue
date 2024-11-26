<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2 v-if="title === 'device-stats'">Configurar la obtención de estadísiticas de los dispositivos</h2>
        <h2 v-if="title === 'ping-routers'">Configurar el envio de pings a los routers</h2>
        <h2 v-if="title === 'check-contracts'">Configurar la verificación de los contratos</h2>
        <h2 v-if="title === 'backups'">Configurar backups automaticos</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :task="task" :title="title"/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import FormUpdate from "./FormUpdate.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    FormUpdate,
    DashboardBase,
  },
  props: {
    task: {
      type: Object,
      required: true,
    },
    title:{
      type: String,
      required: true,
    }
  },
};
</script>
