<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormCreate from "./FormCreate.vue";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Nuevo dispositivo Inventario</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w-1/2">
          <FormCreate/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
