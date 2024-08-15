<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormCreate from "./FormCreate.vue";

const props = defineProps({
  devices: {
    type: Object,
  },

  users: {
    type: Object,
  },

  router: {
    type: Object,
  },
});


</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Crear un Nuevo Dispositivo</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w-1/2">
          <FormCreate :devices="devices"  :users="users" :router="router" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
