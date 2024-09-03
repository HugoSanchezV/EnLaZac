<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";

const props = defineProps({
  device: {
    type: Object,
  },
});
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div
        class="text-center md:flex md:justify-between md:items-center md:gap-2"
      >
        <h2>Editar Dispositivo</h2>
        <span class="bg-cyan-500 py-1 px-2 text-white rounded-md">{{
          props.device.mac_address
        }}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w-1/2">
          <FormUpdate :device="device" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
