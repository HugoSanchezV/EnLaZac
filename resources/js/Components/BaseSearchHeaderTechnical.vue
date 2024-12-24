<template>
  <div class="relative w-full max-w-lg search-container">
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
        @keydown.down.prevent="moveSelection(1)"
        @keydown.up.prevent="moveSelection(-1)"
        @keydown.enter.prevent="selectOption"
        @keydown.esc.prevent="closeSearch"
      />

      <!-- Botón para limpiar la búsqueda -->
      <div
        :class="searchQuery.length > 0 ? 'block' : 'hidden'"
        class="flex justify-center items-center"
      >
        <span
          @click="clearSearch"
          class="material-symbols-outlined cursor-pointer"
          >close</span
        >
      </div>
    </div>

    <!-- Resultados filtrados -->
    <ul
      v-if="isDropdownVisible && filteredOptions.length > 0"
      class="absolute w-full mt-2 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto z-40"
    >
      <li
        v-for="(option, index) in filteredOptions"
        :key="index"
        :class="[
          'px-4 py-2 cursor-pointer flex gap-2',
          index === highlightedIndex ? 'bg-gray-200' : 'hover:bg-gray-200',
        ]"
      >
        <span v-if="option.icon" class="material-symbols-outlined">{{
          option.icon
        }}</span>
        <Link :href="route(option.route)">{{ option.label }}</Link>
      </li>
    </ul>
  </div>
</template>
  
  <script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import { onMounted, onBeforeUnmount } from "vue";

// Opciones disponibles en la aplicación
const options = [
  { label: "Dashboard", route: "dashboard", icon: "earthquake" },

  { label: "Routers", route: "technical.routers", icon: "router" },

  { label: "Conexiones", route: "technical.devices", icon: "router" },

  {
    label: "Inventario",
    route: "technical.inventorie.devices.index",
    icon: "store",
  },

  {
    label: "Historial de Inventario",
    route: "technical.historieDevices.index",
    icon: "history_toggle_off",
  },

  { label: "Tickets", route: "technical.tickets", icon: "description" },
];
const searchQuery = ref("");
const filteredOptions = ref([]);
const isDropdownVisible = ref(false);
const highlightedIndex = ref(-1); // Índice de la opción seleccionada

const filterOptions = () => {
  if (searchQuery.value.length > 0) {
    filteredOptions.value = options.filter((option) =>
      option.label.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
    isDropdownVisible.value = true;
    highlightedIndex.value = -1; // Resetear selección
  } else {
    filteredOptions.value = [];
    isDropdownVisible.value = false;
  }
};

const clearSearch = () => {
  searchQuery.value = "";
  filteredOptions.value = [];
  isDropdownVisible.value = false;
  highlightedIndex.value = -1;
};

const closeSearch = () => {
  clearSearch();
};

const moveSelection = (direction) => {
  if (!isDropdownVisible.value || filteredOptions.value.length === 0) return;

  const maxIndex = filteredOptions.value.length - 1;
  highlightedIndex.value =
    (highlightedIndex.value + direction + filteredOptions.value.length) %
    filteredOptions.value.length;
};

const selectOption = () => {
  if (
    highlightedIndex.value !== -1 &&
    filteredOptions.value[highlightedIndex.value]
  ) {
    const selectedOption = filteredOptions.value[highlightedIndex.value];
    window.location.href = route(selectedOption.route); // Redirigir
  }
};

const handleClickOutside = (event) => {
  const searchContainer = document.querySelector(".search-container");
  if (!searchContainer.contains(event.target)) {
    isDropdownVisible.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});
</script>
  
  <style scoped>
/* Estilos adicionales opcionales */
</style>
  