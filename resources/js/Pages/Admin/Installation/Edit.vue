<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar Instalación</h2>
        <span
          class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md"
          >{{ installation.id}}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <FormUpdate :contracts="contracts" :installation="installation"/>
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
    installation: {
      type: Object,
      required: true,
    },
    contracts: {
      type: Array,
      required: true,
    },
  },
};
</script>
