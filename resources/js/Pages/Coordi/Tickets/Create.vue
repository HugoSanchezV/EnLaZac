<script setup>
const props = defineProps({
  users: {
    type: Object,
  },

  technicals: {
    type: Object,
  },
});
</script>
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div>
        <h2>Crear Ticket</h2>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center w-full">
        <div class="rounded-lg w-full md:w-1/2 md:max-w-2xl">
          <Form :users="users" :technicals="technicals" />
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