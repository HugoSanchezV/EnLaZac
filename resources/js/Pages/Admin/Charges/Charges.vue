<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  charges: Object,
  pagination: Object,
  totalChargesCount: Number,
});

const { charges } = toRefs(props);
const toRouteExport = "charges.excel";

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "contrato",
  "descripción",
"monto",
  "¿pagado?",
  "fecha del pago",
  "fecha de creación",
];

const headers = [
  "Id",
  "Contrato",
  "Descripción",
  "Monto",
  "¿Pagado?",
  "Fecha de Pago",
  "Fecha de Creación",
  "Acciones",
];
//const filters = ["id", "usuario", "plan internet", "dirección"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Cargos</h2>
        </div>
        <div>
          <Link
            :href="route('charges.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px">
              contract
            </span>

            Crear cargo
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalChargesCount > 0">
          <base-export-excel :toRouteExport="toRouteExport"></base-export-excel>
          <!-- Esta es el inicio de la tabla -->
          <base-table-charges
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-charges>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="charges.data.length > 0"
            :links="charges.links"
            :pagination="pagination"
            :current="charges.current_page"
            :total="charges.last_page"
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
          <h2>No hay Cargos para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableCharges from "@/Components/Base/BaseTableCharges.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableCharges,
    BasePagination,
  },

  props: {
    charges: Object,
    pagination: Object,
    totalChargesCount: Number,
  },

  data() {
    return {
      rows: this.charges.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("charges");

      //    console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "id") {
        this.attribute = "id";
      }

      if (this.attribute === "contrato") {
        this.attribute = "contract_id";
      }

      if (this.attribute === "descripción") {
        this.attribute = "description";
      }

      if (this.attribute === "monto") {
        this.attribute = "amount";
      }
      if (this.attribute === "¿pagado?") {
        this.attribute = "paid";
      }

      if (this.attribute === "fecha del pago") {
        this.attribute = "date_paid";
      }

      if (this.attribute === "fecha de creación") {
        this.attribute = "created_at";
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
    charges() {
      this.rows = this.charges.data;
    },
  },
};
</script>
