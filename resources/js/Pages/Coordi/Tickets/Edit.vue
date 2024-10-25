<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar ticket</h2>
        <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">{{
          nombre
        }}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :ticket="ticket" :users="users" :technicals="technicals" />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import FormUpdate from "./FormUpdate.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    FormUpdate,
    DashboardBase,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },

    nombre: {
      type: Object,
      required: true,
    },

    users: {
      type: Array,
      default: () => [],
    },

    technicals: {
      type: Array,
      default: () => [],
    },
  },
};
</script>
