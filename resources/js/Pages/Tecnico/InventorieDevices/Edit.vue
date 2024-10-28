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
        class="text-center flex justify-between md:items-center md:gap-2"
      >
        <h2>Editar Dispositivo</h2>
        <span class="bg-cyan-500 py-1 px-2 text-white rounded-md">{{
          props.device.mac_address
        }}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :device="device" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
