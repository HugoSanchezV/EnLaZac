<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  contracts: Object,
  pagination: Object,
  totalContractsCount: Number,
  paymentSanction: Array,
});

const { contracts } = toRefs(props);
const toRouteExport = "contracts.excel";

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "dispositivo",
  // "usuario",
  "plan internet",
  "comunidad",
  // "fecha de inicio",
  "fecha de terminación",
  "¿activo?",
  // "dirección",
];

const headers = [
  "Id",
  "Dispositivo",
  "Usuarios",
  "Plan Internet",
  "Comunidad",
  // "Fecha de Inicio",
  "Fecha de Terminación",
  "¿Activo?",
  // "Dirección",
  "Acciones",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Contratos</h2>
        </div>
        <div>
          <Link
            :href="route('contracts.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px">
              contract
            </span>

            Crear contrato 
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalContractsCount > 0">
          <base-export-excel :toRouteExport="toRouteExport"></base-export-excel>
          <!-- Esta es el inicio de la tabla -->
          <base-table-contracts
            :paymentSanction = "paymentSanction"
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-contracts>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="contracts.data.length > 0"
            :links="contracts.links"
            :pagination="pagination"
            :current="contracts.current_page"
            :total="contracts.last_page"
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
          <h2>No hay Contratos para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableContracts from "@/Pages/Coordi/Contracts/BaseTableContracts.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableContracts,
    BasePagination,
  },

  props: {
    contracts: Object,
    pagination: Object,
    totalContractsCount: Number,
  },

  data() {
    return {
      rows: this.contracts.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("contracts");

      console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "id") {
        this.attribute = "id";
      }

      if (this.attribute === "dispositivo") {
        this.attribute = "inv_device_id";
      }
      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (this.attribute === "plan internet") {
        this.attribute = "plan_id";
      }

      if (this.attribute === "fecha de inicio") {
        this.attribute = "start_date";
      }

      if (this.attribute === "fecha de terminación") {
        this.attribute = "end_date";
      }

      if (this.attribute === "¿activo?") {
        this.attribute = "active";
      }

      if (this.attribute === "dirección") {
        this.attribute = "address";
      }
      if (this.attribute === "comunidad") {
        this.attribute = "rural_community_id";
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
    contracts() {
      this.rows = this.contracts.data;
    },
  },
};
</script>
