<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  histories: Object,
  pagination: Object,
  totalHistoriesCount: Number,
});

const toRouteExport = "historieDevices.excel.historie";
const idExport = props.histories.data[0].id;

const headers = [
  "state",
  "id",
  "comment",
  "mac address",
  "user",
  "creator",
  "date",
  "acciones",
];
const filters = [
  "id",
  "comment",
  "device_id",
  "user_id",
  "creator_id",
  "state",
  "created_at",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Historial</h2>
        </div>

        <div>
          <h2 class="items-center flex">
            <span class="material-symbols-outlined"> nest_wifi_point </span>
            <span class="bg-gray-300 px-2 rounded-md">{{
              histories.data[0].device.mac_address
            }}</span>
          </h2>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="totalHistoriesCount > 0">
        <base-export-excel :to-route-export="toRouteExport" :id="idExport"></base-export-excel>
        <!-- Esta es el inicio de la tabla -->
        <inventorie-show-table
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="true"
          :edit="true"
          :del="true"
          @search="search"
        >
        </inventorie-show-table>
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
import InventorieShowTable from "./InventorieShowTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    InventorieShowTable,
    //RouterTable,
    BasePagination,
    InventorieShowTable,
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
      const link = route("historieDevices.show");

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

      //   if (props.attribute === "marca") {
      //     this.attribute = "brand";
      //   }

      console.log(props.type);

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
