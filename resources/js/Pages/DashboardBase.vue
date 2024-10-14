<script setup>
import { computed, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";
import AppLayoutAdmin from "@/Layouts/AppLayoutAdmin.vue";
import AppLayoutUser from "@/Layouts/AppLayoutUser.vue";
import AppLayoutEmpleado from "@/Layouts/AppLayoutEmpleado.vue";
import AppLayoutTecnico from "@/Layouts/AppLayoutTecnico.vue";
import AppLayoutCoordinador from "@/Layouts/AppLayoutCoordinador.vue";
import useGeneralNotifications from "@/Components/Base/hooks/useGeneralFlashNotifications";
import Graphics from "./Admin/Components/Graphics.vue";

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
    case 3:
      return AppLayoutEmpleado;
    case 4:
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
            'bg-gray-100 shadow-xl sm:rounded-lg': applyStyles,
            'bg-transparent shadow-none': !applyStyles,
          }"
        >
          <slot name="content">
            <div class="frame-stats flex gap-4">
               <div class="frame">
                  <div class="w-fit rounded-[25px] bg-white p-8 aspect">
                      <div class="h-12">
                        <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">person</span>
                      </div>
                      <div class="my-2">
                          <h2 class="text-4xl font-bold"><span>{{ morrosos }}</span></h2>
                      </div>

                      <div>
                          <p class="mt-2 font-sans text-base font-medium text-gray-700">Usuarios deudores (Morrosos)</p>
                      </div>
                  </div>
                </div>

                <div class="frame ">
                  <div class="w-fit rounded-[25px] bg-white p-8 aspect">
                      <div class="h-12">
                        <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">contract</span>
                      </div>
                      <div class="my-2">
                          <h2 class="text-4xl font-bold"><span>{{ activeContract }}</span></h2>
                      </div>

                      <div>
                          <p class="mt-2 font-sans text-base font-medium text-gray-700">Contratos activos</p>
                      </div>
                  </div>
                </div>

                <div class="frame">
                  <div class="w-fit rounded-[25px] bg-white p-8 aspect">
                      <div class="h-12">
                        <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">confirmation_number</span>
                      </div>
                      <div class="my-2">
                          <h2 class="text-4xl font-bold"><span>{{ new_tickets }}</span></h2>
                      </div>

                      <div>
                          <p class="mt-2 font-sans text-base font-medium text-gray-700">Tickets nuevos</p>
                      </div>
                  </div>
                </div>
                <div class="frame">
                  <div class="w-fit rounded-[25px] bg-white p-8 aspect">
                      <div class="h-12">
                        <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">confirmation_number</span>
                      </div>
                      <div class="my-2">
                          <h2 class="text-4xl font-bold"><span>{{ userCount }}</span></h2>
                      </div>

                      <div>
                          <p class="mt-2 font-sans text-base font-medium text-gray-700">Usuarios registrados</p>
                      </div>
                  </div>
                </div>
              
            </div>
            <div v-for="(targetItem, index) in target" :key="index" >
              <h2>Router</h2><span>{{ index }}</span>
              <Graphics
                :target = "targetItem"
                :upload_rate = "upload_rate[index]"
                :download_rate = "download_rate[index]"
                :upload_byte = "upload_byte[index]"
                :download_byte = "download_byte[index]"
                :index = "index"
              />
            </div>
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
    morrosos: {
      type: Number,
    },
    activeDevice: {
      type: Number,
    },
    new_tickets: {
      type: Number,
    },
    userCount: {
      type: Number,
    },
    activeContract: {
      type: Number,
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
    }
  },
};

</script>