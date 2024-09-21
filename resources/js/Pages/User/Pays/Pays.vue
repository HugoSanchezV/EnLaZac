<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import resources_js_components_PaypalButton from "@/Components/Base/Pays/resources_js_components_PaypalButton.vue";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:content>
      <resources_js_components_PaypalButton></resources_js_components_PaypalButton>
    </template>
  </dashboard-base>
</template>

