<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Información sobre la comuidad rural</h2>
        <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">RuralCommunity name{{
          ruralcommunity.id
        }}</span>
        
      </div>
    </template>
    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w'full md:w-1/2">
          
          <CardInformation :ruralcommunity="ruralcommunity"/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>





<script>
import CardInformation from "./CardInformation.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
import RuralCommunity from "./RuralCommunity.vue";

export default {
components: {
  CardInformation,
  DashboardBase,
},
props: {
  ruralcommunity: {
    type: Object,
    required: true,
  },
},
};
</script>
