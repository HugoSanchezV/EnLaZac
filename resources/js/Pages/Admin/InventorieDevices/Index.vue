<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
import BaseImportExcel from "@/Components/Base/Excel/BaseImportExcel.vue";

const props = defineProps({
  devices: Object,
  pagination: Object,
  totalDevicesCount: Number,
});

const { devices } = toRefs(props);

const toRouteExport = "inventorie.devices.excel";
const toImportRoute = "historieDevices.import.excel";

const headers = [
  "estado",
  "id",
  "mac address",
  "descripción",
  "marca",
  "acciones",
];
const filters = ["estado", "id", "mac address", "descripción", "marca"];
const headingsImport = "mac, descripción, marca";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Dispositivos</h2>
        </div>
        <div>
          <Link
            :href="route('inventorie.devices.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
            ><svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
              fill="currentColor"
              class="size-4"
            >
              <path
                d="M2.879 7.121A3 3 0 0 0 7.5 6.66a2.997 2.997 0 0 0 2.5 1.34 2.997 2.997 0 0 0 2.5-1.34 3 3 0 1 0 4.622-3.78l-.293-.293A2 2 0 0 0 15.415 2H4.585a2 2 0 0 0-1.414.586l-.292.292a3 3 0 0 0 0 4.243ZM3 9.032a4.507 4.507 0 0 0 4.5-.29A4.48 4.48 0 0 0 10 9.5a4.48 4.48 0 0 0 2.5-.758 4.507 4.507 0 0 0 4.5.29V16.5h.25a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75v-3.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v3.5a.75.75 0 0 1-.75.75h-4.5a.75.75 0 0 1 0-1.5H3V9.032Z"
              />
            </svg>

            Agregar al Inventario
          </Link>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="props.totalDevicesCount > 0">
        <div class="flex justify-center md:justify-start">
          <base-export-excel
            :to-route-export="toRouteExport"
          ></base-export-excel>

          <base-import-excel
            :toImportRoute="toImportRoute"
            :headings="headingsImport"
          >
          </base-import-excel>
        </div>

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
          v-if="devices.data.length > 0"
          :links="devices.links"
          :pagination="pagination"
          :current="devices.current_page"
          :total="devices.last_page"
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
        <h2>No tienes dispositivos en el inventario</h2>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import InventorieTable from "./InventorieTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    InventorieTable,
    //RouterTable,
    BasePagination,
  },

  props: {
    devices: Object,
    pagination: Object,
    success: String,
    totalDevicesCount: Number,
  },

  data() {
    return {
      rows: this.devices.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("inventorie.devices.index", route().params.router);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (props.attribute === "estado") {
        this.attribute = "state";
      }

      if (props.attribute === "mac address") {
        this.attribute = "mac_address";
      }

      if (props.attribute === "descripción") {
        this.attribute = "description";
      }

      if (props.attribute === "marca") {
        this.attribute = "brand";
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
    devices() {
      this.rows = this.devices.data;
    },
  },
};
</script>
