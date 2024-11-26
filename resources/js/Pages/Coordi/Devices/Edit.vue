<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";

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

  device: {
    type: Object,
  },

  inv_devices: {
    type: Object,
  },
});
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div
        class="text-center flex justify-between md:items-center md:gap-2"
      >
        <h2>Editar Dispositivo</h2>
        <span class="bg-cyan-500 py-1 px-2 text-white rounded-md">{{
          props.device.address
        }}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate
            :devices="devices"
            :users="users"
            :router="router"
            :device="device"
            :inv_devices="inv_devices"
          />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
