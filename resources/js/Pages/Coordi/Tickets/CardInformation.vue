<script setup>
import { onMounted } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  ticket: Object,
});

onMounted(() => {
  var bg = document.getElementById("status-background");
  var txt = document.getElementById("text-info");
  var title = document.getElementById("title-info");

  switch (props.ticket.status) {
    case "0":
      bg.classList.add("bg-red-600");
      txt.classList.add("text-white");
      title.classList.add("text-white");
      break;
    case "1":
      bg.classList.add("bg-yellow-500");
      break;
    case "2":
      bg.classList.add("bg-blue-500");
      txt.classList.add("text-white");
      title.classList.add("text-black");
      break;
    case "3":
      bg.classList.add("bg-green-600");
      txt.classList.add("text-white");
      title.classList.add("text-white");
      break;
    default:
      alert("Estado desconocido");
      break;
  }
});
</script>

<template>
  <!-- Contenedor principal centrado con fondo degradado profesional -->
  <div class="min-h-screen flex items-center justify-center p-6">
    <!-- Tarjeta de información del ticket -->
    <div class="bg-white shadow-2xl rounded-3xl border border-gray-200 w-full max-w-4xl">
      <!-- Encabezado de la tarjeta con degradado dinámico -->
      <div class="px-8 py-6 rounded-t-3xl" id="status-background">
        <h3 class="text-2xl font-bold" id="title-info">Información del Ticket</h3>
        <p class="mt-2 text-sm" id="text-info">Detalles sobre el ticket y el usuario asociado</p>
      </div>
      <!-- Contenido de la tarjeta -->
      <div class="px-8 py-6">
        <!-- Lista de detalles en un diseño de rejilla responsivo -->
        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- ID del ticket -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.id }}</dd>
          </div>

          <!-- Asunto -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Asunto</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.subject }}</dd>
          </div>

          <!-- Descripción -->
          <div class="sm:col-span-2">
            <dt class="text-sm font-medium text-indigo-600">Descripción</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.description }}</dd>
          </div>

          <!-- Estado -->
          <div class="sm:col-span-2 flex justify-center">
            <div class="text-center">
              <dt class="text-sm font-medium text-indigo-600">Estado</dt>
              <dd class="mt-1 text-lg font-semibold text-gray-900">
                <span
                  v-if="ticket.status === '0'"
                  class="inline-block px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-full"
                >
                  Pendiente
                </span>
                <span
                  v-else-if="ticket.status === '1'"
                  class="inline-block px-4 py-2 text-sm font-semibold text-white bg-yellow-500 rounded-full"
                >
                  En espera
                </span>
                <span
                  v-else-if="ticket.status === '2'"
                  class="inline-block px-4 py-2 text-sm font-semibold text-white bg-blue-500 rounded-full"
                >
                  Trabajando
                </span>
                <span
                  v-else-if="ticket.status === '3'"
                  class="inline-block px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-full"
                >
                  Solucionado
                </span>
              </dd>
            </div>
          </div>

          <!-- ID del usuario -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">ID del Usuario</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">
              <Link
                :href="route('usuarios.show', ticket.user_id)"
                class="text-indigo-500 underline hover:text-indigo-700"
              >
                {{ ticket.user_id }}
              </Link>
            </dd>
          </div>

          <!-- Nombre del usuario -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Nombre del Usuario</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.user.name }}</dd>
          </div>

          <!-- Email del usuario -->
          <div>
            <dt class="text-sm font-medium text-indigo-600">Email</dt>
            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ ticket.user.email }}</dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Fondo degradado para los estados */
.bg-pendiente {
  background-color: #dc2626;
}
.bg-espera {
  background-color: #f59e0b;
}
.bg-trabajando {
  background-color: #3b82f6;
}
.bg-solucionado {
  background-color: #16a34a;
}

/* Diseño de la tarjeta */
.shadow-2xl {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}
.rounded-3xl {
  border-radius: 1.5rem;
}
.text-indigo-600 {
  color: #4f46e5;
}
.text-indigo-500 {
  color: #6366f1;
}
.text-indigo-700 {
  color: #4338ca;
}
</style>
