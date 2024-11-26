<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";

const props = defineProps({
  plan: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="mb-81 flex items-center justify-center p-6">
    <!-- Tarjeta de información del plan -->
    <div v-if="plan" class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-3xl">
      <!-- Encabezado de la tarjeta con degradado -->
      <div class="px-8 py-6 bg-gradient-to-r from-blue-500 to-blue-700 rounded-t-3xl">
        <h3 class="text-2xl font-bold text-white">Información del Plan</h3>
        <p class="mt-2 text-sm text-blue-200">Detalles completos sobre el plan seleccionado</p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- ID del plan -->
          <div>
            <dt class="text-sm font-medium text-blue-600">ID del Plan</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ plan.id }}</dd>
          </div>

          <!-- Nombre del plan -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Nombre del Plan</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ plan.name }}</dd>
          </div>

          <!-- Descripción del plan -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Descripción</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ plan.description }}</dd>
          </div>

          <!-- Ráfaga límite -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Ráfaga Límite</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Subida: {{ plan.burst_limit.upload_limits }}</dd>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Bajada: {{ plan.burst_limit.download_limits }}</dd>
          </div>

          <!-- Tiempo de ráfaga -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Tiempo Ráfaga</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Subida: {{ plan.burst_time.upload_limits }}</dd>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Bajada: {{ plan.burst_time.download_limits }}</dd>
          </div>

          <!-- Límite entrada -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Límite Entrada</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Subida: {{ plan.limite_at.upload_limits }}</dd>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Bajada: {{ plan.limite_at.download_limits }}</dd>
          </div>

          <!-- Límite máximo -->
          <div>
            <dt class="text-sm font-medium text-blue-600">Límite Máximo</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Subida: {{ plan.max_limit.upload_limits }}</dd>
            <dd class="mt-1 text-lg font-semibold text-gray-900">Bajada: {{ plan.max_limit.download_limits }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Mensaje si no hay datos del plan -->
    <div v-else>
      <p class="text-red-500 text-center">No se encontró información del plan.</p>
    </div>
  </div>
</template>

<style scoped>
/* Fondo degradado para el encabezado */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #3b82f6, #1d4ed8); /* Degradado azul a azul oscuro */
}

/* Colores de texto personalizados */
.text-blue-600 {
  color: #2563eb; /* Azul intenso */
}

.text-blue-200 {
  color: #bfdbfe; /* Azul claro */
}

.text-gray-900 {
  color: #111827; /* Texto gris oscuro */
}

/* Bordes redondeados personalizados */
.rounded-3xl {
  border-radius: 1.5rem;
}

/* Sombras personalizadas */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Tipografía mejorada */
.font-semibold {
  font-weight: 600;
}

.text-lg {
  font-size: 1.125rem; /* 18px */
}

.text-sm {
  font-size: 0.875rem; /* 14px */
}

/* Ajustes para la rejilla responsiva */
@media (min-width: 640px) {
  .sm\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>

