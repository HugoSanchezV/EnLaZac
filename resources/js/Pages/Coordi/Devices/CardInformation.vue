
<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  devices: {
    type: Object,
    required: true, // El dispositivo es obligatorio
  },
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="mb-10 flex items-center justify-center p-6">
    <!-- Tarjeta de información del dispositivo -->
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full">
      <!-- Encabezado de la tarjeta con degradado -->
      <div
        class="px-8 py-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl"
      >
        <h3 class="text-2xl font-bold text-white">
          Información del Dispositivo
        </h3>
        <p class="mt-2 text-sm text-indigo-200">
          Detalles sobre el dispositivo asignado
        </p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- ID del dispositivo -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.id }}
            </dd>
          </div>

          <!-- ID dispositivo interno -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">
              ID Dispositivo Interno
            </dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.device_internal_id }}
            </dd>
          </div>

          <!-- ID router -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID Router</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.router_id }}
            </dd>
          </div>

          <!-- Lista -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Lista</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.list }}
            </dd>
          </div>

          <!-- Dirección -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Dirección</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.address }}
            </dd>
          </div>

          <!-- Estado -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Estado</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ devices.disabled ? "Deshabilitado" : "Activo" }}
            </dd>
          </div>
          <!-- ID usuario -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID Usuario</dt>
            <div v-if="devices.user_id !== null && devices?.user_id !== undefined">
              <Link :href="route('usuarios.show', devices?.user_id)">
                <dd class="mt-1 text-lg font-semibold text-gray-900">
                  {{
                    devices?.user_id === null ? "Sin asignar" : devices?.user_id
                  }}
                </dd>
              </Link>
            </div>
            <div v-else>
              <dd class="mt-1 text-lg font-semibold text-gray-900">
                Sin asignar
              </dd>
            </div>
          </div>

          <div>
            <!-- Nombre del usuario -->
            <div>
              <dt class="text-sm font-medium text-indigo-600">
                Nombre del Usuario
              </dt>
              <dd class="mt-1 text-lg font-semibold text-gray-900">
                {{ devices.user === null ? "Sin asignar" : devices.user.name }}
              </dd>
            </div>
          </div>

          <div>
            <!-- Nombre del usuario -->
            <div>
              <dt class="text-sm font-medium text-indigo-600">
                Dispositivo
              </dt>
              <Link v-if="devices.inventorie_device?.mac_address !== null && devices.inventorie_device?.mac_address !== undefined" :href="route('historieDevices.show', devices.inventorie_device.mac_address)" class="mt-1 text-lg font-semibold text-gray-900">
                {{ devices.inventorie_device === null ? "Sin asignar" : devices.inventorie_device.mac_address }}
              </Link>
              <div v-else>
                Sin asignar
              </div>
            </div>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["router"],
  data() {
    return {
      modificarPassword: false,
    };
  },
};
</script>

<style scoped>
/* Estilos personalizados adicionales */

/* Fondo degradado para el contenedor principal */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #ebf8ff, #f3e8ff);
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
  font-size: 1.125rem;
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
