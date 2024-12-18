<template>
  <head>
    <link
      href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"
      rel="stylesheet"
    />
  </head>

  <div
    class="fixed top-0 left-0 w-64 bg-slate-700 h-screen text-white z-50 block"
    ref="sidebar"
  >
    <!-- Logo -->
    <div class="flex lg:hidden justify-end m-5">
      <span
        class="material-symbols-outlined cursor-pointer hover:text-gray-300"
        @click="closeMenuEmit"
        >close</span
      >
    </div>
    <div class="p-4 mt-3 flex items-center justify-center">
      <!-- <span class="material-symbols-outlined">cell_tower</span>
      <span class="ml-2 text-lg font-bold">EnLaZac</span> -->
      <nuvira-side></nuvira-side>
    </div>

    <!-- Menu Items -->
    <nav class="mt-8">
      <ul>
        <li
          v-for="(item, index) in menuItems"
          :key="index"
          class="mb-2 cursor-pointer"
        >
          <div
            @click="toggleMenu(index)"
            class="flex items-center p-2 text-sm font-medium text-gray-200 hover:bg-slate-600 rounded-md m-2"
            :class="{ 'bg-slate-600': item.isOpen }"
          >
            <!-- SVG Icon -->
            <span class="material-symbols-outlined"> {{ item.icon }} </span>

            <span class="ml-3">{{ item.name }}</span>
            <svg
              class="ml-auto h-5 w-5 transition-transform"
              :class="{ 'rotate-180': item.isOpen }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>

          <!-- Subitems -->
          <transition name="slide-fade">
            <ul v-if="item.isOpen" class="ml-1 mt-1 space-y-1 overflow-hidden">
              <li
                v-for="subItem in item.subItems"
                :key="subItem.name"
                class="text-sm text-gray-100 hover:text-gray-200 rounded-md m-1 flex items-center pl-2"
                :class="{
                  'bg-gradient-to-r from-cyan-500 to-teal-400':
                    route().current().split('.')[0] ===
                    subItem.route.split('.')[0],
                }"
              >
                <span class="material-symbols-outlined">
                  fiber_manual_record
                </span>
                <Link
                  :href="
                    route(
                      subItem.route === 'default.dashboard'
                        ? 'dashboard'
                        : subItem.route
                    )
                  "
                  class="block p-2"
                >
                  {{ subItem.name }}
                </Link>
              </li>
            </ul>
          </transition>
        </li>
      </ul>
    </nav>
  </div>
</template>
  
  <script setup>
import { ref, onMounted, defineEmits } from "vue";
import { Link } from "@inertiajs/vue3";
import NuviraSide from "@/Components/Icons/NuviraSide.vue";

// Definición del menú con subitems y sus iconos
const menuItems = ref([
  {
    name: "Dashboard",
    route: "dashboard",
    isOpen: false,
    icon: "earthquake", // ejemplo de path de SVG
    subItems: [
      // { name: "Principal", route: "tickets" },
      { name: "Principal", route: "dashboard" },
      { name: "Pagos", route: "payment" },
      { name: "Cobro local", route: "local.pay.search" },
    ],
  },
  {
    name: "Usuarios",
    route: "usuarios",
    isOpen: false,
    icon: "supervisor_account", // ejemplo de path de SVG
    subItems: [
      { name: "Usuarios", route: "usuarios" },
      { name: "Contratos", route: "contracts" },
      { name: "Instalaciones", route: "installation" },
    ],
  },

  {
    name: "Gestión de red",
    route: "routers",
    isOpen: false,
    icon: "cloud", // ejemplo de path de SVG
    subItems: [
      { name: "Routers", route: "routers" },
      { name: "Conexiones", route: "devices" },
    ],
  },
  {
    name: "Reportes",
    route: "tickets",
    isOpen: false,
    icon: "flag_2", // ejemplo de path de SVG
    subItems: [
      { name: "Tickets", route: "tickets" },
      // { name: "Planes", route: "plans" },
    ],
  },
]);
const emit = defineEmits(["closeMenuEmit"]);
// Función para alternar la visibilidad de los subitems
const toggleMenu = (index) => {
  menuItems.value.forEach((item, idx) => {
    item.isOpen = idx === index ? !item.isOpen : false; // Cierra los demás elementos
  });

  // Esto forza la reactividad en Vue 3 al modificar un array de objetos
  menuItems.value = [...menuItems.value];
};

const closeMenuEmit = () => {
  emit("closeMenuEmit");
};

onMounted(() => {
  // Obtener la ruta actual y extraer el primer segmento antes del punto o barra
  const currentRoute = route().current().split(".")[0]; // Obtiene la ruta actual
  const firstSegment = currentRoute.split(".")[0]; // Divide por punto o barra y obtiene el primer segmento

  // Recorrer los items del menú
  menuItems.value.forEach((item) => {
    // Verificar si la ruta principal del item coincide con la ruta actual
    const isMainRouteMatch = item.route.split(".")[0] === firstSegment;

    // Verificar si la primera parte del route de los subItems coincide con el primer segmento de la ruta actual
    const subItemMatch = item.subItems.some((subItem) => {
      return subItem.route.split(".")[0] === firstSegment;
    });

    // Si alguno de los subItems coincide o la ruta principal coincide con el primer segmento de la ruta actual, abrimos el menú
    if (isMainRouteMatch || subItemMatch) {
      item.isOpen = true;
    }
  });

  // Esto es necesario para forzar la reactividad en Vue
  menuItems.value = [...menuItems.value];
});

</script>
  
  <style scoped>
/* Puedes agregar tus estilos personalizados aquí */
</style>
  