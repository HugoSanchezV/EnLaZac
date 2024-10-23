
<template>
    <dashboard-base :applyStyles="false">
      <template v-slot:namePage>
        <div class="flex justify-between items-center">
          <h2>Información del Usuario</h2>
          <span class="bg-blue-500 text-md text-white py-2 px-3 rounded-md">Usuario: {{ user.name }}</span>
        </div>
      </template>
  
      <template v-slot:content>
        <div class="flex justify-center items-center w-full h-full">
          <div class="p-8 rounded-lg w-2/3">
            <CardInformation :user="user" :ticket="ticket" :plan="plan" :contract="contract" :device="device"/>
          </div>
        </div>
      </template>
    </dashboard-base>
  </template>
  
  <script>
  import DashboardBase from "@/Pages/DashboardBase.vue";  // Importa el layout base del dashboard
  import CardInformation from "./CardInformation.vue";  // Importa el componente que muestra la información del usuario
  
  export default {
    components: {
      DashboardBase,  // Componente base del dashboard
      CardInformation,  // Componente que muestra los detalles del usuario
    },
    // Props que recibe la información del usuario y los tickets desde el componente padre
    props: {
      user: {
        type: Object,
        required: true,  // El objeto usuario es obligatorio
      },
      ticket: {
        type: Array,
        required: true,  // La lista de tickets es obligatoria
      },
      device:{
        type: [Object, null],
        required: true,
      },
      contract:
      {
        type: [Object, null],
        required: true,
      },
      plan: {
        type: [Object, null],
        required: true,
      },
      
    },
  };
  </script>