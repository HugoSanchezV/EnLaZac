
<script setup>
import { Link } from "@inertiajs/vue3";
import GoogleMaps from "@/Components/GoogleMaps.vue";

const props = defineProps({
  user: {
    type: Object,
    required: true, // El usuario es obligatorio
  },
  ticket: {
    tcdype: Array,
    required: true, // Los tickets son obligatorios
  },
  plan: {
    type: [Object, null],
    required: true, // El plan puede ser null si no está asignado
  },
  device: {
    type: [Object, null],
    required: true, // El dispositivo puede ser null si no está asignado
  },
  contract: {
    type: [Object, null],
    required: true, // El contrato puede ser null si no está asignado
  },
});
</script>

<template>
  <!-- Contenedor principal estilizado con sombra y bordes redondeados -->
  <div class="bg-white shadow-lg rounded-lg border border-gray-200 overflow-hidden">
    <!-- Encabezado con diseño atractivo -->
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white">
      <h3 class="text-xl leading-6 font-semibold">Información del Contrato</h3>
      <p class="mt-1 text-sm opacity-90">Detalles sobre el contrato y el dispositivo asignado</p>
    </div>
    <!-- Contenido con detalles -->
    <div class="border-t border-gray-100 px-6 py-4">
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Id del contrato -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">ID del Contrato</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ contract.contract_id }}</dd>
        </div>

        <!-- Id del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">ID del Usuario</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ contract.user_id || 'Sin asignar' }}</dd>
        </div>

        <!-- Nombre del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Nombre de Usuario</dt>
          <Link :href="route('usuarios.show', contract.user_id)" class="mt-1 text-sm text-gray-900">{{ contract.user_name || 'Sin asignar' }}</Link :href="route('usuarios.show', contract.user_id)">
        </div>

        <!-- Email del usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Correo Electrónico</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ contract.user_email || 'Sin asignar' }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Comunidad</dt>
          <dd 
            class="text-gray-600 font-bold"
          >
            {{ contract.name }}
          </dd>
          <!-- <span v-else class="text-gray-500">Sin asignar</span> -->
        </div>

        <!-- Link al contrato asignado -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Mensualidad Contrato</dt>
          <dd  
            class="text-green-600 font-bold hover:underline"
          >
           $ {{ contract.price }} 
          </dd>
        </div>

        <!-- Geolocalización -->
        <div v-if="contract" class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-600">Geolocalización</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <GoogleMaps 
              :lat="parseFloat(contract.geolocation.latitude)" 
              :lng="parseFloat(contract.geolocation.longitude)" 
              :clic="false" 
            />
          </dd>
        </div>
        <div v-else class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-600">Geolocalización</dt>
          <dd class="mt-1 text-sm text-gray-900">Sin asignar</dd>
        </div>

        <!-- Plan afiliado con link -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Plan Afiliado</dt>
          <Link 
            :href="route('plans.show', contract.plan_id)" 
            class="text-indigo-600 hover:underline font-bold"
          >
            {{ contract.plan_name }}
          </Link>
          <!-- <span v-else class="text-gray-500">Sin asignar</span> -->
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Dispositivo Asignado</dt>
          <Link 
            :href="route('historieDevices.show', contract.mac_address)" 
            class="text-gray-600 hover:underline font-bold"
          >
            {{ contract.mac_address }}
          </Link>
          <!-- <span v-else class="text-gray-500">Sin asignar</span> -->
        </div>


        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">IP Asignado</dt>
          <Link 
            :href="route('devices.show', contract.device_id)" 
            class="text-gray-600 hover:underline font-bold"
          >
            {{ contract.address }}
          </Link>
          <!-- <span v-else class="text-gray-500">Sin asignar</span> -->
        </div>

        <!-- Número de tickets -->
        <!-- <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Tickets Enviados</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ ticket.length }}</dd>
        </div> -->

        <!-- Dispositivo asignado -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Comentario</dt>
          <dd class="mt-1 text-sm text-gray-900">
            {{ contract.comment }}
          </dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<script>
export default {
  components: {
    GoogleMaps,
    Link,
  },
  computed: {
    formattedDate() {
      const date = new Date(this.user.created_at);
      const formattedDate =
        date.toLocaleDateString("en-GB") +
        " " +
        date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
      return formattedDate;
    },
  },
};
</script>

<style scoped>
/* Estilos personalizados para un diseño más atractivo */
.bg-gradient-to-r {
  background-image: linear-gradient(to right, #4f46e5, #7c3aed);
}

.text-indigo-600 {
  color: #5b21b6;
}

.text-gray-900 {
  color: #111827;
}

.text-gray-600 {
  color: #4b5563;
}

.border-gray-100 {
  border-color: #f3f4f6;
}

.shadow-lg {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
    0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

.hover\:underline:hover {
  text-decoration: underline;
}

.cursor-pointer {
  cursor: pointer;
}

.overflow-hidden {
  overflow: hidden;
}

.text-sm {
  font-size: 0.875rem;
}
</style>