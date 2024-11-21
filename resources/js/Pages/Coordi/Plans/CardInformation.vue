<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from 'vue';

const props = defineProps({
    plan: {
        type: Object,
        required: true,  
    },
});
</script>

<template>
  <!-- Contenedor principal con estilo mejorado -->
  <div class="bg-white shadow-lg rounded-xl border border-gray-200 transition-transform transform hover:scale-105">
    <!-- Encabezado de la tarjeta con color primario -->
    <div class="px-6 py-5 bg-blue-600 rounded-t-xl">
      <h3 class="text-xl font-semibold text-white">Información del Plan</h3>
      <p class="mt-1 text-sm text-blue-200">Detalles completos sobre el plan seleccionado</p>
    </div>
    <!-- Contenido del plan -->
    <div class="border-t border-gray-200 px-6 py-6">
      <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        
        <!-- Id del plan -->
        <div class="flex items-start">
          <FaRegIdBadge class="text-blue-600 w-6 h-6 mr-3 mt-1"/>
          <div>
            <dt class="text-sm font-medium text-gray-600">ID del Plan</dt>
            <dd class="mt-1 text-lg text-gray-800">{{ plan.id }}</dd>
          </div>
        </div>

        <!-- Nombre del plan -->
        <div class="flex items-start">
          <FaRegListAlt class="text-blue-600 w-6 h-6 mr-3 mt-1"/>
          <div>
            <dt class="text-sm font-medium text-gray-600">Nombre del Plan</dt>
            <dd class="mt-1 text-lg text-gray-800">{{ plan.name }}</dd>
          </div>
        </div>

        <!-- Descripción del plan -->
        <div class="flex items-start">
          <FaRegFileAlt class="text-blue-600 w-6 h-6 mr-3 mt-1"/>
          <div>
            <dt class="text-sm font-medium text-gray-600">Descripción</dt>
            <dd class="mt-1 text-lg text-gray-800">{{ plan.description }}</dd>
          </div>
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
      <!-- Botón de acción -->
      <div class="mt-6 text-right">
        <Link href="/planes/{{ plan.id }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
          Ver Detalles
        </Link>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Estilos personalizados adicionales */

.bg-blue-600 {
  background-color: #3b82f6; /* Azul vibrante */
}

.text-blue-600 {
  color: #3b82f6; /* Azul vibrante */
}

.text-blue-200 {
  color: #bfdbfe; /* Azul claro */
}

.transition-transform {
  transition: transform 0.3s ease;
}

.hover\:scale-105:hover {
  transform: scale(1.05);
}

.hover\:bg-blue-700:hover {
  background-color: #1d4ed8; /* Azul más oscuro */
}
</style>
