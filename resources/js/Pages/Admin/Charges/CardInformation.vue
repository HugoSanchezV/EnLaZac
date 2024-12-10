
<script setup>
import { Link } from "@inertiajs/vue3";
import Charges from "./Charges.vue";

const props = defineProps({
    charge: {
        type: Object,
        required: true,  // El dispositivo es obligatorio
    },
});

const formatDescription = (tipo) => {
  // Convertimos la fecha ISO a un objeto Date

  switch (tipo){
    case 'fuera-corte': 
    return "No pagó antes del día de corte"
    break;
    
    case 'recargo-mes': 
    return "Recargo del mes"
    break;
    
    case 'renta-dispositivo': 
    return "Renta del dispositivo"
    break;
    
    case 'instalacion-inicial': 
    return "Instalación inicial"
    break;
    
    case 'cambio-domicilio': 
    return "Cambio de domicilio"
    break;
    
    default:
      return tipo;
  }
};

const formattedDate = (dateCreation) => {
      // Convertimos la fecha ISO a un objeto Date
      const date = new Date(dateCreation);
      
      // Formateamos como "DD/MM/YYYY HH:mm"
      const formattedDate = date.toLocaleDateString('en-GB') + ' ' + date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
     // alert("");
      return formattedDate;
    }
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="mb-10 flex items-center justify-center p-6">
    <!-- Tarjeta de información del dispositivo -->
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-3xl">
      <!-- Encabezado de la tarjeta con degradado -->
      <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl">
        <h3 class="text-2xl font-bold text-white">Información sobre los Cargos</h3>
        <p class="mt-2 text-sm text-indigo-200">
          Detalles sobre el cargo asignado
        </p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          
          <!-- ID del cargo -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ charge.id }}</dd>
          </div>

          <!-- ID del contrato  -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Id del contrato</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ charge.contract === null ? 'Sin asignar' : charge.contract.id }}
            </dd>
          </div>

           <!-- nombre del usuario  -->
           <div>
            <dt class="text-sm font-medium text-indigo-600">Nombre del usuario</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ charge.contract === null ? 'Sin asignar' : charge.contract.inventorie_device.device.user.name }}
            </dd>
          </div>

          <!-- Descripcion -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Descripcion</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{formatDescription( charge.description)}}</dd>
          </div>

          <!-- Cantidad -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Cantidad</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ charge.amount }}</dd>
          </div>

          <!-- Fecha de pago -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Fecha de pago</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ charge.date_paid }}</dd>
          </div>

          <!-- creado en -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Creado en</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ formattedDate(charge.created_at) }}</dd>

          </div>

          


            <div>
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
    background-image: linear-gradient(to right, #6366f1, #55b9f7);
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