
<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
  ticket: {
    type: Array,
    required: false, // Los tickets son obligatorios
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
    type: Array,
    required: true,
  },
  mapKey: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <!-- Contenedor principal con estilo para mostrar la información del usuario -->
  <div class="bg-white shadow-md rounded-lg border border-gray-200 mt2">
    <!-- Encabezado de la tarjeta de información del usuario -->
    <div class="px-6 py-4 bg-gray-50">
      <div
        class="text-lg flex justify-between leading-6 font-semibold text-gray-800"
      >
        <div v-if="contract[0]?.id !== undefined">
          <h3>Contrato ID: {{ contract[0].id }}</h3>

          <Link
            :href="route('contracts.show', contract[0].id)"
            class="font-light bg-blue-500 text-white px-2 py-1 rounded-md"
            >Ver más...</Link
          >
        </div>

        <h3 v-else>Sin Asignar</h3>
      </div>
      <p class="mt-1 text-sm text-gray-500">
        Detalles sobre el contrato y el dispositivo asignado
      </p>
    </div>
    <!-- Contenido del contrato del usuario -->
    <div class="border-t border-gray-100 px-6 py-4" >
      <!-- Lista de detalles sobre el usuario en un diseño de rejilla -->
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <!-- Muestra el nombre del usuario -->
        <!-- Muestra la dirección del usuario -->
        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-600">Dirección</dt>
          <dd class="mt-1 text-sm text-gray-900">
            {{
              contract === null && contract[0]?.id !== undefined
                ? "Sin asignar"
                : contract[0]?.address
            }}
          </dd>
        </div>

        <!-- Muestra el teléfono del usuario -->
        <div
          v-if="contract != null && contract[0]?.id !== undefined"
          class="sm:col-span-2"
        >
          <div>
            <dt class="text-sm font-medium text-gray-600">Geolocalización</dt>
            <dd class="mt-1 text-sm text-gray-900">
              <GoogleMaps
                :lat="parseFloat(contract[0]?.geolocation.latitude)"
                :lng="parseFloat(contract[0]?.geolocation.longitude)"
                :clic="false"
                :mapKey="mapKey"
              />
            </dd>
          </div>
        </div>
        <div v-else class="sm:col-span-1">
          <div>
            <dt class="text-sm font-medium text-gray-600">Geolocalización</dt>
            <dd class="mt-1 text-sm text-gray-900">
              <p>Sin asignar</p>
            </dd>
          </div>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Contrato asignado</dt>
          <div v-if="contract !== null && contract[0]?.id !== undefined">
            <Link
              :href="route('contracts.show', contract[0]?.id)"
              class="cursor-pointer text-blue-500 underline"
            >
              <dd class="mt-1 text-sm text-gray-900">{{ contract[0].id }}</dd>
            </Link>
          </div>
          <div v-else>
            <dd class="mt-1 text-sm text-gray-900"><p>Sin asignar</p></dd>
          </div>
        </div>

        <!-- Muestra el costo del plan -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Plan afiliado</dt>
          <div v-if="plan !== null && contract[0]?.plan?.id !== undefined">
            <Link
              :href="route('plans.show', contract[0]?.plan?.id)"
              class="cursor-pointer text-blue-500 underline"
            >
              <dd class="mt-1 text-sm text-gray-900">
                {{
                  contract[0]?.plan === null
                    ? "Sin asignar"
                    : contract[0]?.plan.name
                }}
              </dd>
            </Link>
          </div>
          <div v-else>
            <dd class="mt-1 text-sm text-gray-900"><p>Sin asignar</p></dd>
          </div>
        </div>

        <!-- Muestra el número de tickets enviados por el usuario -->
        <!-- <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Tickets Enviados</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ ticket.length }}</dd>
        </div> -->

        <!-- Muestra el dispositivo asignado al usuario -->
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">IP Asignado</dt>
          <Link
            :href="route('devices.show', device.id)"
            class="mt-1 text-sm text-gray-900"
          >
            {{ device === null ? "Sin asignar" : device.address }}
          </Link>
        </div>
      </dl>
    </div>
  </div>
</template>

<script>
import GoogleMaps from "@/Components/GoogleMaps.vue";
export default {
  components: {
    GoogleMaps,
  },
  computed: {
    formattedDate() {
      // Convertimos la fecha ISO a un objeto Date
      const date = new Date(this.user.created_at);

      // Formateamos como "DD/MM/YYYY HH:mm"
      const formattedDate =
        date.toLocaleDateString("en-GB") +
        " " +
        date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });

      return formattedDate;
    },
  },

  methods: {
    createContact(user) {
      const url = route("usuarios.telegram.create.contact", user.id);
      this.$inertia.post(
        url,
        { user: user },
        { preserveState: true, replace: true }
      );
    },
  },
};
</script>
<style scoped>
/* Estilos personalizados para el componente */
.bg-gray-50 {
  background-color: #f9fafb; /* Fondo gris claro */
}

.text-gray-900 {
  color: #1f2937; /* Texto gris oscuro */
}

.text-gray-600 {
  color: #4b5563; /* Texto gris medio */
}

.border-gray-100 {
  border-color: #f3f4f6; /* Borde gris claro */
}

.border-gray-200 {
  border-color: #e5e7eb; /* Borde gris */
}

.shadow-md {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -2px rgba(0, 0, 0, 0.1); /* Sombra para la tarjeta */
}
</style>