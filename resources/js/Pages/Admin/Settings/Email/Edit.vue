<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";
import { defineProps } from "vue";

const props = defineProps({
  account: Object,
});

const account = props.account;
</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Crendeciales de Email</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :account="account" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>