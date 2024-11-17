<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    paymentHistorie: {
        type: Object,
        required: true,  // El dispositivo es obligatorio
    },
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="min-h-screen bg-gradient-to-r from-blue-100 to-purple-200 flex items-center justify-center p-6">
    <!-- Tarjeta de información del dispositivo -->
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-3xl">
      <!-- Encabezado de la tarjeta con degradado -->
      <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-3xl">
        <h3 class="text-2xl font-bold text-white">Información del Historial de pagos</h3>
        <p class="mt-2 text-sm text-indigo-200">
          Detalles sobre el Historial de pagos
        </p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          
          <!-- ID del dispositivo -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ paymentHistorie.id }}</dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">ID Del usuario</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ paymentHistorie.user === null ? 'Sin asignar' : paymentHistorie.user.id }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">ID Del contrato</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ paymentHistorie.contract === null ? 'Sin asignar' : paymentHistorie.contract.id }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">ID De la transaccion</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              {{ paymentHistorie.transaction === null ? 'Sin asignar' : paymentHistorie.transaction.id }}
            </dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">Cantidad</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ paymentHistorie.amount  }}</dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">Contenido</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ paymentHistorie.content}}</dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">Metodo de pago </dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ paymentHistorie.payment_method}}</dd>
          </div>

          <div>
            <dt class="text-sm font-medium text-indigo-600">URL recibida</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ paymentHistorie.receipt_url}}</dd>
          </div>

        </dl>
        <!-- Enlace de acción (opcional) -->
        <div class="mt-8 text-center">
          <Link href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            Ver más detalles &rarr;
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["paymentHistorie"],
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
