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
          <CardInformationUser :user="user"/>
          <!-- Iterar sobre el arreglo de contratos y enviar cada dispositivo, contrato y plan al componente -->
          <pre>
            
          </pre>
          <div v-for="(item, index) in contracts" :key="index">
            <CardInformation 
              :device="item.device" 
              :contract="item.contract" 
              :plan="item.contract.plan" 
            />
          </div>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import CardInformation from "./CardInformation.vue";
import CardInformationUser from "./CardInformationUser.vue";

export default {
  components: {
    DashboardBase,
    CardInformation,
    CardInformationUser
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    ticket: {
      type: Number,
      required: true,
    },
    contracts: {
      type: Array,
      required: true,
    }
  },
};
</script>
