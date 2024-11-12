<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  payments: Object,
  pagination: Object,
  totalPaymentsCount: Number,
});

const { payments } = toRefs(props);
//const toRouteExport = "contracts.excel";

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "usuario",
  "contrato",
  "monto",
  "contenido",
  "metodo de pago",
  "id de transacción",
  "link de recepción",
  "fecha de pago"
];

const headers = [
  "Id",
  "Usuario",
  "Contrato",
  "Monto",
  "Contenido",
  "Método de pago",
  "Id de Transacción",
  "Link de Recepción",
  "Fecha de Pago",
  "Acciones",
];
//const filters = ["id", "usuario", "plan internet", "dirección"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Historial de Pagos</h2>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalPaymentsCount > 0">
          <!-- <base-export-excel :toRouteExport="toRouteExport"></base-export-excel> -->
          <!-- Esta es el inicio de la tabla -->
          <base-table-payments
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-payments>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="payments.data.length > 0"
            :links="payments.links"
            :pagination="pagination"
            :current="payments.current_page"
            :total="payments.last_page"
            :data="{
              q: q,
              attribute: attribute,
              order: order,
              type: type,
            }"
          ></base-pagination>
          <h2
            v-else
            class="flex justify-center mt-4 bg-gray-400 text-white py-2"
          >
            No se encontró ningún resultado de "{{ q }}"
          </h2>
        </div>
        <div v-else class="flex justify-center uppercase font-bold">
          <h2>No hay pagos para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTablePayments from "@/Components/Base/BaseTablePaymentHistories.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTablePayments,
    BasePagination,
  },

  props: {
    payments: Object,
    pagination: Object,
    totalPaymentsCount: Number,
  },

  data() {
    return {
      rows: this.payments.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("payments");

  //    console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "id") {
        this.attribute = "id";
      }

      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (this.attribute === "contrato") {
        this.attribute = "contract_id";
      }
      
      if (this.order === "monto") {
        this.order = "amount";
      }

      if (this.attribute === "contenido") {
        this.attribute = "content";
      }

      if (this.order === "metodo de pago") {
        this.order = "payment_method";
      }

      if (this.order === "id de transacción") {
        this.order = "transaction_id";
      }

      if (this.order === "link de recepción") {
        this.order = "receipt_url";
      }

      if (this.order === "fecha de pago") {
        this.order = "created_at";
      }

      this.$inertia.get(
        link,
        {
          q: this.q,
          attribute: this.attribute,
          type: this.type,
          order: this.order,
        },
        { preserveState: true, replace: true }
      );
    },
  },
  watch: {
    payments() {
      this.rows = this.payments.data;
    },
  },
};
</script>
