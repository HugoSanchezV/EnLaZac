<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  router: {
    type: Object,
    required: true, // El usuario es obligatorio
  },
});
</script>

<template>
  <!-- Contenedor principal con estilo mejorado -->
  <div class="bg-white shadow-lg rounded-xl border border-gray-200 transition-transform transform hover:scale-105">
    <!-- Encabezado de la tarjeta con degradado -->
    <div class="px-6 py-5 bg-gradient-to-r from-cyan-500 to-emerald-500 rounded-t-xl">
      <h3 class="text-xl font-semibold text-white">Información de los Routers</h3>
      <p class="mt-1 text-sm text-cyan-200">Detalles completos sobre el router asignado</p>
    </div>
    <!-- Contenido del router -->
    <div class="border-t border-gray-200 px-6 py-6">
      <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <!-- ID del router -->
        <div>
          <dt class="text-sm font-medium text-gray-600">ID</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.id }}</dd>
        </div>

        <!-- Dirección IP -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Dirección IP</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.ip_address }}</dd>
        </div>

        <!-- Usuario -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Usuario</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.user }}</dd>
        </div>

        <!-- Sincronización -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Sincronización</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.sync }}</dd>
        </div>

        <!-- Dispositivos totales -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Dispositivos Totales</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.total_devices }}</dd>
        </div>

        <!-- Dispositivos habilitados -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Dispositivos Habilitados</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.enable_devices }}</dd>
        </div>

        <!-- Routers creados -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Creado el</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.created_at }}</dd>
        </div>

        <!-- Routers actualizados -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Última Actualización</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.updated_at }}</dd>
        </div>

        <!-- Routers eliminados -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Eliminado el</dt>
          <dd class="mt-1 text-lg text-gray-900">{{ router.deleted_at || 'No eliminado' }}</dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<style scoped>
/* Fondo degradado para el encabezado */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #06b6d4, #10b981); /* Degradado cyan a esmeralda */
}

/* Colores de texto */
.text-cyan-200 {
  color: #a5f3fc; /* Cyan claro */
}

.text-gray-900 {
  color: #1f2937; /* Texto gris oscuro */
}

.text-gray-600 {
  color: #4b5563; /* Texto gris medio */
}

/* Bordes y sombras */
.border-gray-200 {
  border-color: #e5e7eb; /* Borde gris */
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.1); /* Sombra más profunda */
}

/* Transiciones y efectos */
.transition-transform {
  transition: transform 0.3s ease;
}

.hover\:scale-105:hover {
  transform: scale(1.05);
}

/* Tipografía */
.font-semibold {
  font-weight: 600;
}

.text-lg {
  font-size: 1.125rem; /* 18px */
}

.text-sm {
  font-size: 0.875rem; /* 14px */
}
</style>
