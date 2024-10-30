<script setup>
import { computed, onMounted } from "vue";
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import AppLayoutAdmin from "@/Layouts/AppLayoutAdmin.vue";
import AppLayoutUser from "@/Layouts/AppLayoutUser.vue";
import AppLayoutEmpleado from "@/Layouts/AppLayoutEmpleado.vue";
import AppLayoutTecnico from "@/Layouts/AppLayoutTecnico.vue";
import AppLayoutCoordinador from "@/Layouts/AppLayoutCoordinador.vue";

import useGeneralNotifications from "@/Components/Base/hooks/useGeneralFlashNotifications";


const { props } = usePage();
const authenticatedUser = props.auth.user; // Aquí accedes al usuario autenticado

const layoutComponent = computed(() => {
  switch (authenticatedUser.admin) {
    case 0:
      return AppLayoutUser;
    case 1:
      return AppLayoutAdmin;
    case 2:
      return AppLayoutCoordinador;
    case 4:
      return AppLayoutEmpleado;
    case 3:
      return AppLayoutTecnico;
    default:
      return AppLayoutUser;
  }
});



useGeneralNotifications();
</script>

<template>
  <component :is="layoutComponent" title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <slot name="namePage"></slot>
      </h2>
    </template>
    <div class="py-12 ">
     
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="overflow-hidden sm:rounded-lg"
          :class="{
            'bg-gray-100  sm:rounded-lg': applyStyles,
            'bg-transparent shadow-none': !applyStyles,
          }"
        >
          <slot name="content">
              
          </slot>
        </div>
      </div>
    </div>
  </component>
</template>

<script>
export default {
  props: {
    applyStyles: {
      type: Boolean,
      default: true,
    },
  },
};

</script>