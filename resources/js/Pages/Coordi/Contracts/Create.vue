<script setup>
const props = defineProps({
  devices: {
    type: Object,
  },
  plans: {
    type: Object,
  },
  community:{
    type: Array,
  },
  mapKey:{
    type: String,
  }
});
</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Crear Contrato</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <Form 
          :devices="devices" 
          :plans="plans" 
          :community="community"
          :mapKey="mapKey"/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import Form from "./FormCreate.vue";
export default {
  components: {
    DashboardBase,
    Form,
  },
};
</script>