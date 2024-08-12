<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Crear Ticket</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        
        <div class="p-8 rounded-lg w-1/2">
            <Form/>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import Form from "./Form.vue";
export default {
  components: {
    DashboardBase,
    Form,
  },
};
</script>