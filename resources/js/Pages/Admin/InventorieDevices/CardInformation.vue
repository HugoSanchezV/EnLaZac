
<script setup>
import { Link } from "@inertiajs/vue3";
const props = defineProps({
    inventorieDevices: {
        type: Object,
        required: true,  // El usuario es obligatorio
    },
});


</script>

<template>
  <!-- Contenedor principal con estilo para mostrar la información del usuario -->
  <div class="bg-white shadow-md rounded-lg border border-gray-200">
    <!-- Encabezado de la tarjeta de información del usuario -->
     
    <div v-if="inventorieDevices.state === 0" class="px-6 py-4 bg-blue-300">
      <h3 class="text-lg leading-6 font-semibold">Dispositivo del inventario</h3>
      <p class="mt-1 text-sm text-gray-650">Detalles sobre el dispositivo del inventario</p>
    </div>
    <div v-if="inventorieDevices.state === 1" class="px-6 py-4 bg-green-300">
      <h3 class="text-lg leading-6 font-semibold text-gray-800">Dispositivo del inventario</h3>
      <p class="mt-1 text-sm text-gray-700">Detalles sobre el dispositivo del inventario</p>
    </div>
    <div v-if="inventorieDevices.state === 2" class="px-6 py-4 bg-yellow-300">
      <h3 class="text-lg leading-6 font-semibold text-gray-900">Dispositivo del inventario</h3>
      <p class="mt-1 text-sm text-gray-600">Detalles sobre el dispositivo del inventario</p>
    </div>
    <!-- Contenido del contrato del usuario -->
    <div class="border-t border-gray-100 px-6 py-4">
      <!-- Lista de detalles sobre el usuario en un diseño de rejilla -->
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Dirección MAC</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ inventorieDevices.mac_address}}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Estado</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div v-if="inventorieDevices.state === 0" class="bg-blue-300 rounded-md">
                  <div class="flex justify-center items-center gap-2">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      class="size-4"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.5 1A4.5 4.5 0 0 0 10 5.5V9H3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-1.5V5.5a3 3 0 1 1 6 0v2.75a.75.75 0 0 0 1.5 0V5.5A4.5 4.5 0 0 0 14.5 1Z"
                        clip-rule="evenodd"
                      />
                    </svg>

                    <span>Disponible</span>
                  </div>
                </div>
                <div v-else-if="inventorieDevices.state === 1" class="bg-green-300 rounded-md">
                  <div class="flex justify-center items-center gap-2">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      class="size-4"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z"
                        clip-rule="evenodd"
                      />
                    </svg>
                    En Uso
                    <span></span>
                  </div>
                </div>
                <div v-else-if="inventorieDevices.state === 2" class="bg-yellow-300 rounded-md">
                  <div class="flex justify-center items-center gap-2">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      fill="currentColor"
                      class="size-4"
                    >
                      <path
                        fill-rule="evenodd"
                        d="M14.5 10a4.5 4.5 0 0 0 4.284-5.882c-.105-.324-.51-.391-.752-.15L15.34 6.66a.454.454 0 0 1-.493.11 3.01 3.01 0 0 1-1.618-1.616.455.455 0 0 1 .11-.494l2.694-2.692c.24-.241.174-.647-.15-.752a4.5 4.5 0 0 0-5.873 4.575c.055.873-.128 1.808-.8 2.368l-7.23 6.024a2.724 2.724 0 1 0 3.837 3.837l6.024-7.23c.56-.672 1.495-.855 2.368-.8.096.007.193.01.291.01ZM5 16a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"
                        clip-rule="evenodd"
                      />
                      <path
                        d="M14.5 11.5c.173 0 .345-.007.514-.022l3.754 3.754a2.5 2.5 0 0 1-3.536 3.536l-4.41-4.41 2.172-2.607c.052-.063.147-.138.342-.196.202-.06.469-.087.777-.067.128.008.257.012.387.012ZM6 4.586l2.33 2.33a.452.452 0 0 1-.08.09L6.8 8.214 4.586 6H3.309a.5.5 0 0 1-.447-.276l-1.7-3.402a.5.5 0 0 1 .093-.577l.49-.49a.5.5 0 0 1 .577-.094l3.402 1.7A.5.5 0 0 1 6 3.31v1.277Z"
                      />
                    </svg>
                    <span>En Reparación</span>
                  </div>
                </div>
          </dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Descripcion</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ inventorieDevices.description }}</dd>
        </div>

        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Marca</dt>
          <dd class="mt-1 text-sm text-gray-900">{{ inventorieDevices.brand }}</dd>
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