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
  
    <div class="relative w-full max-w-lg">
      <!-- Contenedor del buscador -->
      <div
        class="flex items-center justify-start px-2 py-1 mt-2 rounded-full bg-gray-100"
      >
        <!-- Ícono de búsqueda -->
        <span class="material-symbols-outlined text-gray-500 text-md ml-4"
          >search</span
        >
  
        <!-- Campo de búsqueda -->
        <input
          type="text"
          placeholder="Escribe para buscar"
          class="px-4 focus:outline-none focus:ring-0 ml-2 w-full text-gray-600 text-md border-none bg-transparent"
          v-model="searchQuery"
          @input="filterOptions"
        />
      </div>
  
      <!-- Resultados filtrados: Solo se muestra si hay texto en el campo y coincidencias -->
      <ul
        v-if="searchQuery.length > 0 && filteredOptions.length > 0"
        class="absolute w-full mt-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto z-40"
      >
        <li
          v-for="(option, index) in filteredOptions"
          :key="index"
          class="px-4 py-2 cursor-pointer hover:bg-gray-200 flex gap-2"
        >
          <span v-if="option.icon" class="material-symbols-outlined">{{ option.icon }}</span>
          <Link :href="route(option.route)">{{ option.label }}</Link>
        </li>
      </ul>
    </div>
  </template>
  
  <script setup>
  import { ref } from "vue";
  import { Link } from "@inertiajs/vue3";
  
  // Opciones disponibles en la aplicación
  const options = [
    { label: "Dashboard", route: "dashboard", icon: "earthquake" },
  
    { label: "Routers", route: "technical.routers", icon:"router" },

    { label: "Dispositivos", route: "technical.devices", icon:"router" },
  
    { label: "Inventario", route: "technical.inventorie.devices.index", icon:"store" },
  
    { label: "Historial de Inventario", route: "technical.historieDevices.index", icon:"history_toggle_off" },
  
    { label: "Tickets", route: "technical.tickets", icon:"description" },
  ];
  
  const searchQuery = ref("");
  const filteredOptions = ref([]);
  
  // Filtra las opciones basado en la entrada del usuario
  const filterOptions = () => {
    // Filtrar solo si el texto del campo de búsqueda no está vacío
    if (searchQuery.value.length > 0) {
      filteredOptions.value = options.filter((option) =>
        option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
      );
    } else {
      // Si el campo está vacío, no mostrar opciones
      filteredOptions.value = [];
    }
  };
  </script>
  
  <style scoped>
  /* Estilos adicionales opcionales */
  </style>
  