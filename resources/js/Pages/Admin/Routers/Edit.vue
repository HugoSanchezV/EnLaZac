<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Editar Router</h2>
        <div class="flex gap-2">
          <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md">{{
            router.ip_address
          }}</span>
          <span class="bg-slate-500 text-md text-white py-2 px-3 rounded-md">{{
            router.user
          }}</span>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        <div class="p-8 rounded-lg w-1/2">
          <FormUpdate :router="router" />
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
    router: {
      type: Object,
      required: true,
    },
  },
};
</script>