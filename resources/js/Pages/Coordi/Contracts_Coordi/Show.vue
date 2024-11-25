
<template>
    <dashboard-base :applyStyles="false">
      <template v-slot:namePage>
        <div class="flex justify-between">
          <h2>Información del contrato</h2>
          <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">Contrato No. {{
            contract.id
          }}</span>
          
        </div>
      </template>
      <template v-slot:content>
        <div class="flex justify-center items-center w-full h-full">
          <div class="rounded-lg w-full md:w-1/2">
            <CardInformation :contract="contract"/>
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
    contract: {
      type: Object,
      required: true,
    },
  },
};
</script>
