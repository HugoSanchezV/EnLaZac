<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    plan: {
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
      <h3 class="text-lg leading-6 font-semibold text-gray-800">Información del Plan</h3>
      <p class="mt-1 text-sm text-gray-500">Detalles sobre el plan</p>
    </div>
    <!-- Contenido del contrato del usuario -->
    <div class="border-t border-gray-100 px-6 py-4">
      <!-- Lista de detalles sobre el usuario en un diseño de rejilla -->
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        
        <!-- Muestra el nombre del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Id</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ plan.id }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Nombre del plan</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ plan.name}}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Descripsion</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ plan.description }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Rafaga limite</dt>
          <dd class="mt-1 text-sm text-gray-900">Subida: {{ plan.burst_limit.upload_limits }}</dd>
          <dd class="mt-1 text-sm text-gray-900">Bajada: {{ plan.burst_limit.download_limits }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">tiempo rafaga</dt>
          <dd class="mt-1 text-sm text-gray-900">Subida: {{ plan.burst_time.upload_limits }}</dd>
          <dd class="mt-1 text-sm text-gray-900">Bajada: {{ plan.burst_time.download_limits }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">limite ent</dt>
          <dd class="mt-1 text-sm text-gray-900">Subida: {{ plan.limite_at.upload_limits}}</dd>
          <dd class="mt-1 text-sm text-gray-900">Bajada: {{ plan.limite_at.download_limits}}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">limite maximo</dt>
          <dd class="mt-1 text-sm text-gray-900">Subida: {{ plan.max_limit.upload_limits}}</dd>
          <dd class="mt-1 text-sm text-gray-900">Bajada: {{ plan.max_limit.download_limits}}</dd>
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