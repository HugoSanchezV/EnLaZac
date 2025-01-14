<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar contrato</h2>
        <span
          class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md"
          >{{ charge.id }}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate 
          :charge="charge" 
          :contracts="contracts" 
          :interestFuera="interestFuera"
          :interestRecargo="interestRecargo"
          />
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
    contracts: {
      type: Array,
    },
    charge:{
      type: Object,
    },
    interestFuera: {
    type: Number
    },
    interestRecargo: {
      type: Number
    }
  },
};
</script>
