<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from "vue";

// Definición de las props
const props = defineProps({
  ruralcommunity: {
    type: Object,
    required: true, // Se espera un objeto definido
  },
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="min-h-screen flex items-center justify-center p-6">
    <!-- Tarjeta de información de la comunidad rural -->
    <div v-if="ruralcommunity" class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-3xl">
      <!-- Encabezado de la tarjeta con degradado -->
      <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl">
        <h3 class="text-2xl font-bold text-white">Información de la Comunidad Rural</h3>
        <p class="mt-2 text-sm text-indigo-200">Detalles sobre la comunidad rural seleccionada</p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- ID de la comunidad -->
          <div v-if="ruralcommunity.id">
            <dt class="text-sm font-medium text-indigo-600">ID de la Comunidad</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ruralcommunity.id }}</dd>
          </div>

          <!-- Nombre de la comunidad -->
          <div v-if="ruralcommunity.name">
            <dt class="text-sm font-medium text-indigo-600">Nombre de la Comunidad</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ruralcommunity.name }}</dd>
          </div>

          <!-- Costo de instalación -->
          <div v-if="ruralcommunity.installation_cost">
            <dt class="text-sm font-medium text-indigo-600">Costo de Instalación</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ruralcommunity.installation_cost }}</dd>
          </div>
        </dl>
      </div>
    </div>

    <!-- Mensaje si no hay datos de la comunidad -->
    <div v-else>
      <p class="text-red-500 text-center">No se encontró información de la comunidad rural.</p>
    </div>
  </div>
</template>

<style scoped>
/* Fondo degradado para el contenedor principal */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #6366f1, #a855f7);
}

/* Colores de texto personalizados */
.text-indigo-600 {
  color: #4f46e5;
}

.text-indigo-200 {
  color: #a5b4fc;
}

.text-indigo-800:hover {
  color: #3730a3;
}

/* Degradados personalizados para el encabezado */
.bg-gradient-to-r.from-indigo-500.to-purple-600 {
  background-image: linear-gradient(to right, #6366f1, #a855f7);
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

.text-gray-900 {
  color: #111827;
}

.text-gray-700 {
  color: #4b5563;
}

/* Efecto de transición para enlaces */
a:hover {
  text-decoration: underline;
}

/* Ajustes para mejorar la responsividad y apariencia */
@media (min-width: 640px) {
  .sm\:grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
  }
}
</style>
