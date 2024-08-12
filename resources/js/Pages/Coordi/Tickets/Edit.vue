<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar</h2>
        <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">{{user.name}}</span>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w-1/2">
          <FormUpdate :user="user"/>
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
    user: {
      type: Object,
      required: true,
    },
  },
};
</script>
