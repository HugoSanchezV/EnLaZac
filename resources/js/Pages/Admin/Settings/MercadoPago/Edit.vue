<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";
import { defineProps } from "vue";

const props = defineProps({
  settings: String,
});

const settings = props.settings;
</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar Crendeciales de MercadoPago </h2>

        <!-- <div class="flex gap-2">
            <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">{{
              router.ip_address
            }}</span>
            <span class="bg-slate-500 text-md text-white py-2 px-3 rounded-md">{{
              router.user
            }}</span>
          </div> -->
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