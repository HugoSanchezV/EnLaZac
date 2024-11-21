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

  { label: "Pagos", route: "payment", icon: "payments" },
  { label: "Eliminar Histoial de Pago", route: "payment", icon: "payments" },

  { label: "Extender Contrato", route: "reaming.contracts", icon: "calendar_add_on" },

  { label: "Crear Usuarios", route: "usuarios.create", icon: "group_add" },
  { label: "Editar Usuarios", route: "usuarios", icon: "edit" },
  { label: "Eliminar Usuarios", route: "usuarios", icon: "group_remove" },

  { label: "Usuarios", route: "usuarios", icon: "group" },
  { label: "Crear Usuarios", route: "usuarios.create", icon: "group_add" },
  { label: "Editar Usuarios", route: "usuarios", icon: "edit" },
  { label: "Eliminar Usuarios", route: "usuarios", icon: "group_remove" },

  { label: "Pre-registro", route: "usuarios.pre.register", icon: "phone_iphone" },
  { label: "Crear Pre-registro", route: "usuarios.pre.register.create", icon: "phone_iphone" },
  { label: "Editar pre-registro", route: "usuarios.pre.register", icon: "phone_iphone" },
  { label: "Eliminarr pre-registro", route: "usuarios.pre.register", icon: "phone_iphone" },
 
  { label: "Cargos", route: "charges", icon: "attach_money" },
  { label: "Crear Cargo", route: "charges.create", icon: "attach_money" },
  { label: "Editar Cargo", route: "charges", icon: "attach_money" },
  { label: "Eliminar Cargo", route: "charges", icon: "attach_money" },

  { label: "Routers", route: "routers", icon:"router" },
  { label: "Crear Router", route: "routers.create", icon:"add_circle" },
  { label: "Editar Router", route: "routers", icon:"edit" },
  { label: "Eliminar Router", route: "routers", icon:"delete" },
  
  { label: "Dispositivos", route: "devices", icon:"router" },

  { label: "Inventario", route: "inventorie.devices.index", icon:"store" },
  { label: "Crear dispositivo en inventario", route: "inventorie.devices.create", icon:"add_circle" },
  { label: "Editar en inventario", route: "inventorie.devices.index", icon:"edit" },
  { label: "Eliminar del inventario", route: "inventorie.devices.index", icon:"delete" },

  { label: "Historial de Inventario", route: "historieDevices.index", icon:"history_toggle_off" },
  { label: "Eliminar historial del inventario", route: "historieDevices.index", icon:"history_toggle_off" },

  { label: "Tickets de coordinación", route: "tickets", icon:"description" },
  { label: "Crear Ticket", route: "tickets.create", icon:"note_add" },
  { label: "Editar Ticket", route: "tickets", icon:"note_alt" },
  { label: "Eliminar Ticket", route: "tickets", icon:"delete" },

  { label: "Planes", route: "plans", icon:"wifi" },
  { label: "Crear plan", route: "plans.create", icon:"add_circle" },
  { label: "Editar plan", route: "plans", icon:"edit" },
  { label: "Eliminar plan", route: "plans", icon:"delete" },

  { label: "Comunidades", route: "rural-community", icon:"pin_drop" },
  { label: "Crear comunidad", route: "rural-community.create", icon:"pin_drop" },
  { label: "Editar plan", route: "rural-community", icon:"pin_drop" },
  { label: "Eliminar plan", route: "rural-community", icon:"pin_drop" },

  { label: "Contratos", route: "contracts", icon:"contract" },
  { label: "Crear contrato", route: "contracts.create", icon:"receipt_long" },
  { label: "Editar contrato", route: "contracts", icon:"contract_edit" },
  { label: "Eliminar contrato", route: "contracts", icon:"contract_delete" },

  { label: "Configuración", route: "settings", icon:"settings" },
  { label: "Paypal", route: "settings.paypal.edit", icon:"settings" },
  { label: "Email", route: "settings.email.edit", icon:"mail" },
  { label: "BackUp", route: "backups", icon:"database_upload" },
  { label: "Intereses", route: "settings.interest", icon:"payments" },
  { label: "Tareas en segundo plano", route: "settings.background", icon:"auto_transmission" },
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
