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
import DashboardAdmin from "./Admin/DashboardAdmin.vue";


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
        <slot name="namePage">
          
        </slot>
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
              <dashboard-admin v-if="props.auth.user.admin == 1"
              :morrosos="props.morrosos"
              :new_tickets="props.new_tickets"
              :currentUsers="props.currentUsers"
              :activeContract="props.activeContract"
              :target="props.target"
              :upload_rate="props.upload_rate"
              :download_rate="props.download_rate"
              :upload_byte="props.upload_byte"
              :download_byte="props.download_byte"
              :routers="props.routers"
              >

              </dashboard-admin>
          </slot>
        </div>
      </div>
    </div>
  </component>
</template>

<script>
import { Link } from "@inertiajs/vue3";
export default {
  components: {
    Link,
  },
  props: {
    applyStyles: {
      type: Boolean,
      default: true,
    },
    props: {
    applyStyles: {
      type: Boolean,
      default: true,
    },
    morrosos: {
      type: Object,
    },
    activeDevice: {
      type: Object,
    },
    new_tickets: {
      type: Object,
    },
    currentUsers: {
      type: Object,
    },
    activeContract: {
      type: Object,
    },
    target: {
      type: Array,
    },
    upload_rate:{
      type: Array,
    },
    download_rate: {
      type: Array,
    },
    upload_byte: {
      type: Array,
    },
    download_byte: {
      type: Array,
    },
    routers: {
      type: Array,
    }
  },
  },
};

</script>