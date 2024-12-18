<template>
  <button @click="goBack" class="flex items-center px-2 py-1 border border-gray-300 rounded-md shadow-sm text-gray-700 hover:bg-gray-200 transition duration-300">
    <span class="material-icons mr-2">arrow_back</span>
    <!-- <span>Volver</span> -->
  </button>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

// Guarda el historial manualmente en un array global
let routeHistory = JSON.parse(sessionStorage.getItem('routeHistory')) || [];

const updateHistory = () => {
  const currentUrl = window.location.pathname;
  if (!routeHistory.length || routeHistory[routeHistory.length - 1] !== currentUrl) {
    routeHistory.push(currentUrl);
    sessionStorage.setItem('routeHistory', JSON.stringify(routeHistory));
  }
};

updateHistory();

const goBack = () => {
  routeHistory.pop(); // Elimina la página actual
  const previousRoute = routeHistory.pop(); // Encuentra la página anterior diferente

  if (previousRoute) {
    sessionStorage.setItem('routeHistory', JSON.stringify(routeHistory));
    router.visit(previousRoute);
  } else {
    router.visit('/'); // Si no hay historial, regresa a la raíz
  }
};
</script>
