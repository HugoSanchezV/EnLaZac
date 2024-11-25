<script setup>
import { toRefs } from "vue";
import BaseTablePaymentHistorie from "./BaseTablePaymentHistorie.vue";
const props = defineProps({
  payments: Object,
  pagination: Object,
  totalPaymentsCount: Number,
  totalAmount: Number,
  totalAmountMonth: Number,
});

const { payments } = toRefs(props);

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "usuario",
  // "contrato",
  "monto",
  // "contenido",
  "metodo de pago",
  "id de transacción",
  // "link de recepción",
  "fecha de pago",
];

const headers = [
  "Id",
  "Cliente",
  "Encagado",
  "Monto",
  // "Contenido",
  "Método de pago",
  "Id de Transacción",
  // "Link de Recepción",
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
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-6">
            <!-- Card para el total de pagos -->
            <div
              class="card-total-payments bg-gradient-to-r from-blue-500 to-blue-700 text-white p-6 rounded-lg shadow-md"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-xl font-bold">Total Recaudado</h3>
                  <p class="text-3xl mt-2 font-semibold">${{ totalAmount }}</p>
                </div>
                <div class="icon bg-white p-3 rounded-full shadow-md">
                  <i class="fas fa-dollar-sign text-blue-700 text-2xl"></i>
                </div>
              </div>
            </div>

            <!-- Card para el total del mes seleccionado -->
            <div
              class="card-monthly-payments bg-gradient-to-r from-green-500 to-green-700 text-white p-6 rounded-lg shadow-md"
            >
              <div class="flex items-center justify-between">
                <div>
                  <h3 class="text-xl font-bold">Total del Mes</h3>
                  <p class="text-3xl mt-2 font-semibold">
                    ${{ totalAmountMonth }}
                  </p>
                </div>
                <div class="icon bg-white p-3 rounded-full shadow-md">
                  <i class="fas fa-calendar-alt text-green-700 text-2xl"></i>
                </div>
              </div>
            </div>
          </div>
          <!-- Esta es el inicio de la tabla -->
          <base-table-payment-historie
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-payment-historie>
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
      date: null,
    };
  },
  methods: {
    search(props) {
      const link = route("payment");

      //    console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;
      this.date = props.date;

      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (this.attribute === "contrato") {
        this.attribute = "contract_id";
      }

      if (this.attribute === "monto") {
        this.attribute = "amount";
      }

      if (this.attribute === "contenido") {
        this.attribute = "content";
      }

      if (this.attribute === "metodo de pago") {
        this.attribute = "payment_method";
      }

      if (this.attribute === "id de transacción") {
        this.attribute = "transaction_id";
      }

      // if (this.attribute === "link de recepción") {
      //   this.attribute = "receipt_url";
      // }

      if (this.attribute === "fecha de pago") {
        this.attribute = "created_at";
      }

      this.$inertia.get(
        link,
        {
          q: this.q,
          attribute: this.attribute,
          type: this.type,
          order: this.order,
          date: this.date,
        },
        { preserveState: true, replace: true, preserveScroll: true }
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
