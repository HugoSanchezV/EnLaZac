<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
import BaseImportExcelDevices from "@/Components/Base/Excel/BaseImportExcelDevices.vue";
import { usePage } from "@inertiajs/vue3";

const admin = usePage().props.auth.user.admin;
const props = defineProps({
  devices: Object,
  pagination: Object,
  totalDevicesCount: Number,
  users: Object,
  inv_devices: Object,
});

const { devices, users } = toRefs(props);
const toRouteExport = "devices.all.excel";
const toImportRoute = "devices.import.excel";
//const toImportRouteSecond = "devices.to.local.import.excel";
const headingsImport =
  "id interno, id disposotivo, id usuario, comentario, direccion, id router, desactivado (0 | 1) \n (Si no hay relacion dejar vacio, el router es obligatorio)";

const headers = [
  "id",
  //"router_id",
  "dispositivo",
  "usuario",
  "comentario",
  // "list",
  "ip",
  "router",
  "estado",
  "acciones",
];
const filters = [
  "id",
  "id interno",
  "dispositivo",
  "usuario",
  "comentario",
  //"list",
  "ip",
];

const columns = ["id", "name"];
</script>

  <template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between flex-col md:flex-row">
        <div>
          <h2>Todas las Conexiones</h2>
        </div>
        <div class="flex flex-col columns-1 md:columns-2 md:gap-1 md:flex-row">
          <div class="mb-1 mt-1 md:mb-0 md:mt-0">
            <Link
              :href="route('device.ping.historie')"
              class="flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
            >
              <span class="material-symbols-outlined" style="font-size: 18px">
                network_ping
              </span>
              Historial de pings
            </Link>
          </div>
          <div>
            <Link
              :href="route('routers')"
              method="get"
              class="flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="size-4"
              >
                <path
                  fill-rule="evenodd"
                  d="M2 2.75A.75.75 0 0 1 2.75 2C8.963 2 14 7.037 14 13.25a.75.75 0 0 1-1.5 0c0-5.385-4.365-9.75-9.75-9.75A.75.75 0 0 1 2 2.75Zm0 4.5a.75.75 0 0 1 .75-.75 6.75 6.75 0 0 1 6.75 6.75.75.75 0 0 1-1.5 0C8 10.35 5.65 8 2.75 8A.75.75 0 0 1 2 7.25ZM3.5 11a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3Z"
                  clip-rule="evenodd"
                />
              </svg>
              Nueva Conexión
            </Link>
          </div>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="props.totalDevicesCount > 0">
        <div class="flex justify-center md:justify-start" v-if="admin === 1">
          <base-export-excel :toRouteExport="toRouteExport"></base-export-excel>
          <base-import-excel-devices
            :toImportRoute="toImportRoute"
            :headings="headingsImport"
          >
          </base-import-excel-devices>
        </div>

        <!-- Esta es el inicio de la tabla -->
        <device-table
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="true"
          :edit="true"
          :del="true"
          :users="users"
          :inv_devices="inv_devices_ref"
          @search="search"
        >
        </device-table>
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
        <h2>No hay dispositivos para mostrar</h2>
      </div>
    </template>
  </dashboard-base>
</template>

  <script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import DeviceTable from "./DeviceTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    DeviceTable,
    //RouterTable,
    BasePagination,
  },

  props: {
    devices: Object,
    pagination: Object,
    totalDevicesCount: Number,
    inv_devices: Object,
  },

  data() {
    return {
      rows: this.devices.data,
      inv_devices_ref: this.inv_devices,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("devices");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.order = props.order;

      if (this.attribute === "id interno") {
        this.attribute = "device_internal_id";
      }

      if (this.attribute === "dispositivo") {
        this.attribute = "device_id";
      }

      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }

      if (this.attribute === "comentario") {
        this.attribute = "comment";
      }

      if (this.attribute === "ip") {
        this.attribute = "address";
      }

      this.$inertia.get(
        link,
        {
          q: this.q,
          attribute: this.attribute,
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

    inv_devices() {
      this.inv_devices_ref = this.inv_devices;
    },
  },
};
</script>
