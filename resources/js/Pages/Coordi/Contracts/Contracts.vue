<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  contracts: Object,
  pagination: Object,
  success: String,
  totalContractsCount: Number,
});

const { contracts, success } = toRefs(props);
const toast = useToast();
const toRouteExport = 'contracts.excel'

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "usuario",
  "plan internet",
  "fecha de inicio",
  "fecha de terminación",
  "¿activo?",
  "dirección",
];

const headers = [
  "Id",
  "Usuarios",
  "Plan Internet",
  "Fecha de Inicio",
  "Fecha de Terminación",
  "¿Activo?",
  "Dirección",
  "Acciones",
];
//const filters = ["id", "usuario", "plan internet", "dirección"];
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
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"
              />
            </svg>

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
import BaseTableContracts from "@/Components/Base/BaseTableContracts.vue";
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
    success: String,
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

      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (this.attribute === "plan internet") {
        this.attribute = "plan_id";
      }

      if (this.order === "fecha de inicio") {
        this.order = "start_date";
      }

      if (this.order === "fecha de terminación") {
        this.order = "end_date";
      }

      if (this.order === "¿activo?") {
        this.order = "active";
      }

      if (this.attribute === "dirección") {
        this.attribute = "address";
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
      const toast = useToast();
      this.rows = this.contracts.data;
      if (this.success) {
        toast.success(this.success, {
          position: POSITION.TOP_CENTER,
          draggable: true,
        });
      }
    },
  },

  beforeMount() {
    const toast = useToast();
    if (this.success) {
      toast.success(this.success, {
        position: POSITION.TOP_CENTER,
        draggable: true,
      });
    }
  },
};
</script>
