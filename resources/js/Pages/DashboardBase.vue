<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

import AppLayoutAdmin from "@/Layouts/AppLayoutAdmin.vue";
import AppLayoutUser from "@/Layouts/AppLayoutUser.vue";
import AppLayoutEmpleado from "@/Layouts/AppLayoutEmpleado.vue";
import AppLayoutTecnico from "@/Layouts/AppLayoutTecnico.vue";

const { props } = usePage();

const layoutComponent = computed(() => {
  switch (props.user.admin) {
    case 0:
      return AppLayoutUser;
    case 1:
      return AppLayoutAdmin;
    case 2:
      return AppLayoutEmpleado;
    case 3:
      return AppLayoutTecnico;
    default:
      return AppLayoutUser; // Default layout
  }
});
</script>

<template>
  <component :is="layoutComponent" title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        <slot name="namePage"></slot>
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="overflow-hidden sm:rounded-lg"
          :class="{
            'bg-white shadow-xl sm:rounded-lg': applyStyles,
            'bg-transparent shadow-none': !applyStyles,
          }"
        >
          <slot name="content"></slot>
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