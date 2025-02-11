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

  { label: "Pagos", route: "payment", icon: "payments" },
  { label: "Eliminar Histoial de Pago", route: "payment", icon: "payments" },

  {
    label: "Extender Contrato",
    route: "reaming.contracts",
    icon: "calendar_add_on",
  },

  { label: "Crear Usuarios", route: "usuarios.create", icon: "group_add" },
  { label: "Editar Usuarios", route: "usuarios", icon: "edit" },
  { label: "Eliminar Usuarios", route: "usuarios", icon: "group_remove" },

  { label: "Usuarios", route: "usuarios", icon: "group" },
  { label: "Crear Usuarios", route: "usuarios.create", icon: "group_add" },
  { label: "Editar Usuarios", route: "usuarios", icon: "edit" },
  { label: "Eliminar Usuarios", route: "usuarios", icon: "group_remove" },

  {
    label: "Pre-registro",
    route: "usuarios.pre.register",
    icon: "phone_iphone",
  },
  {
    label: "Crear Pre-registro",
    route: "usuarios.pre.register.create",
    icon: "phone_iphone",
  },
  {
    label: "Editar pre-registro",
    route: "usuarios.pre.register",
    icon: "phone_iphone",
  },
  {
    label: "Eliminarr pre-registro",
    route: "usuarios.pre.register",
    icon: "phone_iphone",
  },

  { label: "Cargos", route: "charges", icon: "attach_money" },
  { label: "Crear Cargo", route: "charges.create", icon: "attach_money" },
  { label: "Editar Cargo", route: "charges", icon: "attach_money" },
  { label: "Eliminar Cargo", route: "charges", icon: "attach_money" },

  { label: "Routers", route: "routers", icon: "router" },
  { label: "Crear Router", route: "routers.create", icon: "add_circle" },
  { label: "Editar Router", route: "routers", icon: "edit" },
  { label: "Eliminar Router", route: "routers", icon: "delete" },

  { label: "Dispositivos", route: "devices", icon: "router" },

  { label: "Inventario", route: "inventorie.devices.index", icon: "store" },
  {
    label: "Crear dispositivo en inventario",
    route: "inventorie.devices.create",
    icon: "add_circle",
  },
  {
    label: "Editar en inventario",
    route: "inventorie.devices.index",
    icon: "edit",
  },
  {
    label: "Eliminar del inventario",
    route: "inventorie.devices.index",
    icon: "delete",
  },

  {
    label: "Historial de Inventario",
    route: "historieDevices.index",
    icon: "history_toggle_off",
  },
  {
    label: "Eliminar historial del inventario",
    route: "historieDevices.index",
    icon: "history_toggle_off",
  },

  { label: "Tickets de coordinación", route: "tickets", icon: "description" },
  { label: "Crear Ticket", route: "tickets.create", icon: "note_add" },
  { label: "Editar Ticket", route: "tickets", icon: "note_alt" },
  { label: "Eliminar Ticket", route: "tickets", icon: "delete" },

  { label: "Planes", route: "plans", icon: "wifi" },
  { label: "Crear plan", route: "plans.create", icon: "add_circle" },
  { label: "Editar plan", route: "plans", icon: "edit" },
  { label: "Eliminar plan", route: "plans", icon: "delete" },

  { label: "Comunidades", route: "rural-community", icon: "pin_drop" },
  {
    label: "Crear comunidad",
    route: "rural-community.create",
    icon: "pin_drop",
  },
  { label: "Editar plan", route: "rural-community", icon: "pin_drop" },
  { label: "Eliminar plan", route: "rural-community", icon: "pin_drop" },

  { label: "Contratos", route: "contracts", icon: "contract" },
  { label: "Crear contrato", route: "contracts.create", icon: "receipt_long" },
  { label: "Editar contrato", route: "contracts", icon: "contract_edit" },
  { label: "Eliminar contrato", route: "contracts", icon: "contract_delete" },

  { label: "Configuración", route: "settings", icon: "settings" },
  { label: "Paypal", route: "settings.paypal.edit", icon: "settings" },
  { label: "Email", route: "settings.email.edit", icon: "mail" },
  { label: "BackUp", route: "backups", icon: "database_upload" },
  { label: "Intereses", route: "settings.interest", icon: "payments" },
  {
    label: "Tareas en segundo plano",
    route: "settings.background",
    icon: "auto_transmission",
  },
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

