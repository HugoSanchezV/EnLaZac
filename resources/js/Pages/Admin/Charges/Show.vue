
<template>
    <dashboard-base :applyStyles="false">
      <template v-slot:namePage>
        <div class="flex justify-between">
          <h2>Información sobre los cargos</h2>
          <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">Cargo No. {{
            charge.id
          }}</span>
          
        </div>
      </template>
      <template v-slot:content>
        <div class="flex justify-center items-center w-full h-full">
          <div class="rounded-lg w-full md:w-1/2">
            <CardInformation :charge="charge"/>
          </div>
        </div>
      </template>
    </dashboard-base>
  </template>
  




<script>
import CardInformation from "./CardInformation.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    CardInformation,
    DashboardBase,
  },
  props: {
    charge: {
      type: Object,
      required: true,
    },
  },
};
</script>
