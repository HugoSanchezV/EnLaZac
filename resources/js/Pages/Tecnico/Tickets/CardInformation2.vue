<script setup>
import { onMounted } from "vue";

const props = defineProps({
  ticket: Object,
});

onMounted(() => {
  var bg = document.getElementById('status-background');
  var txt = document.getElementById('text-info');
  var title = document.getElementById('title-info');
  
  switch(props.ticket.status) {
    
    case '0':
      bg.classList.add('bg-red-600');
      txt.classList.add('text-white');
      title.classList.add('text-white');

      break;
    case '1':
      bg.classList.add('bg-yellow-500');
      break;
    case '2':
      bg.classList.add('bg-blue-500');
      txt.classList.add('text-white');
      title.classList.add('text-black');
      break;
    case '3':
      bg.classList.add('bg-green-600');
      txt.classList.add('text-white');
      title.classList.add('text-white');
      break;
    default:
      alert("220000413513201");
      break;
  }
});
</script>

<template>
  <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
      <!--bg-gray-50 -->
      <div class="px-6 py-4" id="status-background">
        <h3 class="text-lg leading-6 font-semibold" id="title-info">
          Información del Ticket
        </h3>
        <p class="mt-1 text-sm" id="text-info">
          Detalles sobre el ticket y el usuario asociado
        </p>
      </div>
    
    <div class="border-t border-gray-100 px-6 py-4">
      <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">ID</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0" >
              {{ ticket.id }}
            </div>
          </dd>
        </div>
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Asunto</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0">
              {{ ticket.subject }}
            </div>
          </dd>
        </div>
        <div class="sm:col-span-2">
          <dt class="text-sm font-medium text-gray-600">Descripción</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0">
              {{ ticket.description }}
            </div>
          </dd>
        </div>
        <div class="sm:col-span-2 flex justify-center">
          <div class="flex align-center flex-col">
            <dt class="text-sm font-medium text-gray-600 flex justify-center">Estado</dt>
            <dd class="mt-1 text-sm text-gray-900">
              <span
                v-if="ticket.status === '0'"
                class="items-center   inline-block px-3 py-1 text-sm font-semibold text-white bg-red-600 rounded-full"
              >Pendiente</span>
              <span
                v-else-if="ticket.status === '1'"
                class="items-center inline-block px-3 py-1 text-sm font-semibold text-white bg-yellow-500 rounded-full"
              >En espera</span>
              <span
                v-else-if="ticket.status === '2'"
                class="items-center inline-block px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded-full"
              >Trabajando</span>
              <span
                v-else-if="ticket.status === '3'"
                class="items-center inline-block px-3 py-1 text-sm font-semibold text-white bg-green-600 rounded-full"
              >Solucionado</span>
              <span v-else>{{ ticket.status }}</span>
            </dd>
          </div>
        </div>
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">ID del Usuario</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0">
              {{ ticket.user_id }}
            </div>
          </dd>
        </div>
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Nombre del Usuario</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0">
              {{ ticket.user.name }}
            </div>
          </dd>
        </div>
        <div class="sm:col-span-1">
          <dt class="text-sm font-medium text-gray-600">Email</dt>
          <dd class="mt-1 text-sm text-gray-900">
            <div class="data-container w-full bg-gray-50 text-sm font-medium text-gray-800 border-0">
            {{ ticket.user.email }}
            </div>
          </dd>
        </div>
      </dl>
    </div>
  </div>
</template>

<style scoped>
.bg-pendiente{
  background-color: #dc2626;
}
.bg-espera{
  background-color: #f59e0b;
}
.bg-trabajando{
  background-color: #3b82f6;
}
.bg-solucionado{
  background-color: #16a34a;
}
.data-container{
  border-radius: 0px;
    padding-top: 0.5rem;
    padding-right: 0.75rem;
    padding-bottom: 0.5rem;
    padding-left: 0.75rem;
}
.bg-gray-50 {
  background-color: #f9fafb;
}

.text-gray-900 {
  color: #1f2937;
}

.text-gray-600 {
  color: #4b5563;
}

.border-gray-100 {
  border-color: #f3f4f6;
}

.border-gray-200 {
  border-color: #e5e7eb;
}

.shadow-md {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
}
</style>