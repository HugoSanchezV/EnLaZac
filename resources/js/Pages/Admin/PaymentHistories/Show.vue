
<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Información sobre el historial de pagos</h2>
        <span class="bg-cyan-500 text-md text-white py-2 px-3 rounded-md"
          >paymentHistorie No. {{ paymentHistorie.id }}</span
        >
      </div>
    </template>
    <template v-slot:content>
      <CardInformation
        :cart="paymentHistorie.content"
        :amount="paymentHistorie.amount"
        :token="paymentHistorie.transaction_id"
        :user="paymentHistorie.user"
        :date="paymentHistorie.created_at"
        :orderId="paymentHistorie.id"
      />
    </template>
  </dashboard-base>
</template>
  
<script>
import CardInformation from "./CardInformation.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    CardInformation,
    DashboardBase,
  },
  props: {
    paymentHistorie: {
      type: Object,
      required: true,
    },
  },
};
</script>
