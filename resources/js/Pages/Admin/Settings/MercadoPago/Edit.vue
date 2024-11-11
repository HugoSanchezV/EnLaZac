<!-- resources/js/Pages/Admin/Settings/MercadoPago/Edit.vue -->
<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";
import { defineProps } from "vue";

const props = defineProps({
  settings: Object, // Cambiado de String a Object
});
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar Configuraciones de MercadoPago</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :settings="settings" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
