
<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    device: {
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
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        
        <!-- Muestra el nombre del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Id</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.id }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">id dispositivo interno</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.device_internal_id}}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">id router</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.router_id }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">lista</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.list }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Direccion</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.address }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">estado</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.disabled }}</dd>
        </div>

        <!-- Muestra el email del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">id dispositivo</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.inventorieDevice.id === null ?? 'Sin asignar'}}</dd>

        </div>

        
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">id usuario</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.user_id}}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">id usuario</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ device.user.name}}</dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<script>
</script>
<style scoped>
/* Estilos personalizados para el componente */
.bg-gray-50 {
  background-color: #f9fafb;  /* Fondo gris claro */
}

.text-gray-900 {
  color: #1f2937;  /* Texto gris oscuro */
}

.text-gray-600 {
  color: #4b5563;  /* Texto gris medio */
}

.border-gray-100 {
  border-color: #f3f4f6;  /* Borde gris claro */
}

.border-gray-200 {
  border-color: #e5e7eb;  /* Borde gris */
}

.shadow-md {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);  /* Sombra para la tarjeta */
}
</style>