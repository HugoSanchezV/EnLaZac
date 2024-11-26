<script setup>
import { Link } from "@inertiajs/vue3";
import GoogleMaps from "@/Components/GoogleMaps.vue";

const props = defineProps({
  ticket: {
    type: Array,
    required: true, // Los tickets son obligatorios
  },
  plan: {
    type: [Object, null],
    required: true,
  },
  device: {
    type: [Object, null],
    required: true,
  },
  contract: {
    type: Object, // Ajustado para manejar un contrato único
    required: true,
  },
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="min-h-screen flex items-center justify-center p-6">
    <!-- Tarjeta de información del contrato del usuario -->
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-4xl">
      <!-- Encabezado de la tarjeta con degradado -->
      <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl">
        <h3 class="text-2xl font-bold text-white">Información del Contrato</h3>
        <p class="mt-2 text-sm text-indigo-200">Detalles del contrato y dispositivo asignado</p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- ID del contrato -->
          <div v-if="contract.id">
            <dt class="text-sm font-medium text-indigo-600">ID del Contrato</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ contract.id }}</dd>
          </div>

          <!-- Dirección -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Dirección</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ contract.address || "Sin asignar" }}
            </dd>
          </div>

          <!-- Geolocalización -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Geolocalización</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              <div v-if="contract.geolocation">
                <GoogleMaps
                  :lat="parseFloat(contract.geolocation.latitude)"
                  :lng="parseFloat(contract.geolocation.longitude)"
                  :clic="false"
                />
              </div>
              <div v-else>Sin asignar</div>
            </dd>
          </div>

          <!-- Contrato asignado -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Contrato Asignado</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              <Link
                v-if="contract.id"
                :href="route('contracts.show', contract.id)"
                class="text-indigo-500 underline hover:text-indigo-700"
              >
                Ver Contrato
              </Link>
              <span v-else>Sin asignar</span>
            </dd>
          </div>

          <!-- Plan afiliado -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Plan Afiliado</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              <Link
                v-if="plan"
                :href="route('plans.show', plan.id)"
                class="text-indigo-500 underline hover:text-indigo-700"
              >
                {{ plan.name || "Sin asignar" }}
              </Link>
              <span v-else>Sin asignar</span>
            </dd>
          </div>

          <!-- Tickets enviados -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Tickets Enviados</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.length }}</dd>
          </div>

          <!-- Dispositivo asignado -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Dispositivo Asignado</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ device ? device.device_internal_id : "Sin asignar" }}
            </dd>
          </div>
        </dl>
      </div>
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

.text-indigo-500 {
  color: #6366f1;
}

.text-indigo-700 {
  color: #4338ca;
}

.text-gray-900 {
  color: #111827;
}

.font-semibold {
  font-weight: 600;
}

.text-lg {
  font-size: 1.125rem; /* 18px */
}

.rounded-3xl {
  border-radius: 1.5rem;
}

.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

a:hover {
  text-decoration: underline;
}
</style>
