<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";
import { defineProps } from "vue";

const props = defineProps({
  cutoffday: Number,
  exemptionPeriod: Object,
  equipmentDay: Number,
});

</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Configuración las variables del servicio</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate 
          :cutoffday="cutoffday" 
          :exemptionPeriod="exemptionPeriod"
          :equipmentDay = "equipmentDay"

          />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>