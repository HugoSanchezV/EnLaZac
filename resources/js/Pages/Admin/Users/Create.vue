<template>
  <dashboard-base>
    <template v-slot:namePage>
      <div>
        <h2>Crear Usuario</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full">
        
        <div class="p-8 rounded-lg w-1/2">
            <register-admin></register-admin>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import RegisterAdmin from "@/Pages/Auth/RegisterAdmin.vue";

export default {
  components: {
    DashboardBase,
    RegisterAdmin,
  },
};
</script>