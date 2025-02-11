<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  histories: Object,
  pagination: Object,
  totalHistoriesCount: Number,
});

const { histories, totalHistoriesCount } = toRefs(props);
const toRouteExport = "historieDevices.excel";

const headers = [
  "estado",
  "id",
  "comentario",
  "mac",
  "usuario",
  "creador",
  "fecha",
  "acciones",
];
const filters = [
  "id",
  "comentario",
  "mac",
  "usuario",
  "creador",
  "estado",
  "fecha",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Historial del Inventario</h2>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="totalHistoriesCount > 0">
        <base-export-excel :to-route-export="toRouteExport"></base-export-excel>
        <!-- Esta es el inicio de la tabla -->
        <inventorie-table
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="true"
          :edit="true"
          :del="true"
          @search="search"
        >
        </inventorie-table>
        <base-pagination
          v-if="histories.data.length > 0"
          :links="histories.links"
          :pagination="pagination"
          :current="histories.current_page"
          :total="histories.last_page"
          :data="{
            q: q,
            type: type,
            order: order,
            attribute: attribute,
          }"
        ></base-pagination>
        <h2 v-else class="flex justify-center mt-4 bg-gray-400 text-white py-2">
          No se encontró ningún resultado de "{{ q }}"
        </h2>
      </div>
      <div v-else>
        <h2>No hay historia de ningún dispositivo</h2>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import InventorieTable from "./InventorieTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";
import { usePage } from "@inertiajs/vue3";

export default {
  components: {
    Link,
    DashboardBase,
    InventorieTable,
    //RouterTable,
    BasePagination,
  },

  props: {
    histories: Object,
    pagination: Object,
    success: String,
  },

  data() {
    return {
      rows: this.histories.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "DESC",
    };
  },

  methods: {
    search(props) {
      const link = route("historieDevices.index");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      //   if (props.attribute === "mac address") {
      //     this.attribute = "mac_address";
      //   }

      //   if (props.attribute === "descripción") {
      //     this.attribute = "description";
      //   }
      //       const filters = [
      //   "id",
      //   "comment",
      //   "device_id",
      //   "user_id",
      //   "creator_id",
      //   "state",
      //   "created_at",
      // ];

      if (props.attribute === "comentario") {
        this.attribute = "comment";
      }

      if (props.attribute === "mac") {
        this.attribute = "device_id";
      }

      if (props.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (props.attribute === "creador") {
        this.attribute = "creator_id";
      }

      if (props.attribute === "estado") {
        this.attribute = "state";
      }

      if (props.attribute === "fecha") {
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
    histories() {
      this.rows = this.histories.data;
    },
  },
};
</script>
