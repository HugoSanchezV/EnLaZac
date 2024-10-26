<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Editar Pre-registro</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :register="register" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
    
    <script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import FormUpdate from "./FormUpdate.vue";
export default {
  components: {
    DashboardBase,
    FormUpdate,
  },

  props: {
    register: {
      type: Object,
    },
  },
};
</script>