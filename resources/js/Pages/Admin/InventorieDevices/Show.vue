
<template>
    <dashboard-base :applyStyles="false">
      <template v-slot:namePage>
        <div class="flex justify-between">
          <h2>Información del dispositivo del inventario</h2>
          <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">Dispositivo del inventario No. {{
            inventorieDevices.id
          }}</span>
          
        </div>
      </template>
      <template v-slot:content>
        <div class="flex justify-center items-center w-full h-full">
          <div class="p-8 rounded-lg w-1/2">
            <CardInformation :inventorieDevices="inventorieDevices"/>
          </div>
        </div>
      </template>
    </dashboard-base>
  </template>
  




<script>
// import CardInformation from "./CardInformation2.vue";
import CardInformation from "./CardInformation.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    CardInformation,
    DashboardBase,
  },
  props: {
    inventorieDevices: {
      type: Object,
      required: true,
    },
  },
};
</script>
