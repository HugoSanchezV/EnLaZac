<script setup>
import { Link } from "@inertiajs/vue3";
import { defineProps } from 'vue';

const props = defineProps({
    ruralcommunity: {
        type: Object,
        required: true,  
    },
});
</script>

<template>
  <!-- Contenedor principal con estilo mejorado -->
  <div class="bg-white shadow-lg rounded-xl border border-gray-200 transition-transform transform hover:scale-105">
    <!-- Encabezado de la tarjeta con color suave -->
    <div class="px-6 py-5 bg-green-500 rounded-t-xl">
      <h3 class="text-xl font-semibold text-white">Información de la Comunidad Rural</h3>
      <p class="mt-1 text-sm text-green-200">Detalles completos sobre la comunidad rural seleccionada</p>
    </div>
    <!-- Contenido de la comunidad rural -->
    <div class="border-t border-gray-200 px-6 py-6">
      <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        
        <!-- ID de la comunidad -->
        <div>
          <dt class="text-sm font-medium text-gray-600">ID de la Comunidad</dt>
          <dd class="mt-1 text-lg text-gray-800">{{ ruralcommunity.id }}</dd>
        </div>

        <!-- Nombre de la comunidad -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Nombre de la Comunidad</dt>
          <dd class="mt-1 text-lg text-gray-800">{{ ruralcommunity.name }}</dd>
        </div>

        <!-- Costo de instalación -->
        <div>
          <dt class="text-sm font-medium text-gray-600">Costo de Instalación</dt>
          <dd class="mt-1 text-lg text-gray-800">{{ ruralcommunity.installation_cost }}</dd>
        </div>

      </dl>
      <!-- Botón de acción -->
      <div class="mt-6 text-right">
        <Link href="/comunidades-rurales/{{ ruralcommunity.id }}" class="inline-block bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
          Ver Detalles
        </Link>
      </div>
    </div>
  </div>
</template>

    <style scoped>
    /* Estilos personalizados adicionales */

    .bg-green-500 {
      background-color: #10B981; /* Verde suave */
    }

    .text-green-500 {
      color: #10B981; /* Verde suave */
    }

    .text-green-200 {
      color: #6EE7B7; /* Verde claro */
    }

    .transition-transform {
      transition: transform 0.3s ease;
    }

    .hover\:scale-105:hover {
      transform: scale(1.05);
    }

    .hover\:bg-green-600:hover {
      background-color: #059669; /* Verde más oscuro */
    }

    /* Mejoras de Tipografía */
    .text-lg {
      font-size: 1.125rem; /* 18px */
    }

    .text-xl {
      font-size: 1.25rem; /* 20px */
    }

    .font-semibold {
      font-weight: 600;
    }

    .font-medium {
      font-weight: 500;
    }

    .text-sm {
      font-size: 0.875rem; /* 14px */
    }

    /* Mejoras de Espaciado */
    .px-6 {
      padding-left: 1.5rem;
      padding-right: 1.5rem;
    }

    .py-5 {
      padding-top: 1.25rem;
      padding-bottom: 1.25rem;
    }

    .py-6 {
      padding-top: 1.5rem;
      padding-bottom: 1.5rem;
    }

    .mt-1 {
      margin-top: 0.25rem;
    }

    .mt-6 {
      margin-top: 1.5rem;
    }

    .mr-3 {
      margin-right: 0.75rem;
    }

    /* Bordes y Sombras */
    .rounded-xl {
      border-radius: 0.75rem;
    }

    .border-gray-200 {
      border-color: #e5e7eb;
    }

    .border-gray-100 {
      border-color: #f3f4f6;
    }

    .shadow-lg {
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 
                  0 4px 6px -4px rgba(0, 0, 0, 0.1);
    }

    .bg-white {
      background-color: #ffffff;
    }
    </style>