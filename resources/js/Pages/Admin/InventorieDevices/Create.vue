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
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormCreate/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
