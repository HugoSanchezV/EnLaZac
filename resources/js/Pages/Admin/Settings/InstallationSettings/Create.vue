<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import Form from "./FormCreate.vue";
import Installation from "../../Installation/Installation.vue";
const props = defineProps({
  installations: {
    type: Array,
  },
});

</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Crear Instalación</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <Form :installations="installations"/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>