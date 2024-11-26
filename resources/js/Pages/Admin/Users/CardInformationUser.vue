
<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    user: {
        type: Object,
        required: true,  // El usuario es obligatorio
    },
});


</script>

<template>
  <!-- Contenedor principal con estilo para mostrar la información del usuario -->
  <div class="bg-white shadow-md rounded-lg border border-gray-200">
    <!-- Encabezado de la tarjeta de información del usuario -->
    <div class="px-6 py-4 bg-gray-50">
      <h3 class="text-lg leading-6 font-semibold text-gray-800">Información del Usuario</h3>
      <p class="mt-1 text-sm text-gray-500">Detalles sobre el contrato y el dispositivo asignado</p>
    </div>
    <!-- Contenido del contrato del usuario -->
    <div class="border-t border-gray-100 px-6 py-4">
      <!-- Lista de detalles sobre el usuario en un diseño de rejilla -->
      <dl class="grid grid-cols-2 gap-4 sm:grid-cols-2">
        
        <!-- Muestra el nombre del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Id</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ user.id }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Nombre</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ user.name }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Alias</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ user.alias }}</dd>
        </div>

        <!-- Muestra el email del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Email</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ user.email }}</dd>
        </div>
        
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Creado en</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ formattedDate }}</dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<script>
import GoogleMaps from '@/Components/GoogleMaps.vue'
export default {
  components: {
        GoogleMaps,
        Link,
    },
    computed: {
    formattedDate() {
      // Convertimos la fecha ISO a un objeto Date
      const date = new Date(this.user.created_at);
      
      // Formateamos como "DD/MM/YYYY HH:mm"
      const formattedDate = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      
      return formattedDate;
    }
  }
};
</script>
<style scoped>
/* Estilos personalizados adicionales */

/* Degradados */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #3B82F6, #8B5CF6); /* Azul a púrpura */
}

.hover\:from-blue-600:hover {
  background-image: linear-gradient(to right, #2563EB, #7C3AED); /* Azul más oscuro a púrpura más oscuro */
}

.hover\:to-purple-700:hover {
  background-image: linear-gradient(to right, #3B82F6, #6D28D9); /* Azul a púrpura más oscuro */
}

/* Bordes Redondeados */
.rounded-2xl {
  border-radius: 1.5rem;
}

/* Tipografía */
.text-lg {
  font-size: 1.125rem; /* 18px */
}

.text-2xl {
  font-size: 1.5rem; /* 24px */
}

.font-semibold {
  font-weight: 600;
}

.font-medium {
  font-weight: 500;
}

.text-sm {
  font-size: 0.875rem; /* 14px */
}

/* Espaciado */
.px-6 {
  padding-left: 1.5rem;
  padding-right: 1.5rem;
}

.py-5 {
  padding-top: 1.25rem;
  padding-bottom: 1.25rem;
}

.py-6 {
  padding-top: 1.5rem;
  padding-bottom: 1.5rem;
}

.mt-1 {
  margin-top: 0.25rem;
}

.mt-2 {
  margin-top: 0.5rem;
}

.mt-8 {
  margin-top: 2rem;
}

/* Text Colors */
.text-gray-700 {
  color: #4B5563; /* Gris medio */
}

.text-gray-900 {
  color: #111827; /* Gris muy oscuro */
}

.text-blue-600 {
  color: #2563EB; /* Azul intenso */
}

.text-purple-200 {
  color: #E9D5FF; /* Púrpura claro */
}

.text-blue-600:hover {
  color: #1D4ED8; /* Azul más oscuro al hover */
}

/* Otros */
.flex {
  display: flex;
}

.justify-end {
  justify-content: flex-end;
}

.w-full {
  width: 100%;
}

.h-64 {
  height: 16rem;
}

.rounded-lg {
  border-radius: 0.5rem;
}

.rounded-md {
  border-radius: 0.375rem;
}

.overflow-hidden {
  overflow: hidden;
}

/* Eliminación de Sombras */
.shadow-md {
  box-shadow: none; /* Eliminado */
}
</style>