  <script setup>
import { toRefs, watch, ref } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  devices: Object,
  pagination: Object,
  success: String,
  totalDevicesCount: Number,
  users: Object,
  inv_devices: Object,
});

const { devices, success, users } = toRefs(props);
const toast = useToast();
// let inv_devices_ref = ref(inv_devices.value);

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }

  newValue = "";
});

// watch(inv_devices, (newValue) => {
//   // Aquí puedes realizar acciones cuando inv_devices cambie.
//   console.log(newValue)
//   inv_devices_ref = ref(newValue)
//   console.log(inv_devices_ref)
// });

const headers = [
  "id",
  "device_internal_id",
  //"router_id",
  "device_id",
  "user_id",
  "comment",
  // "list",
  "address",
  "Enable",
  "acciones",
];
const filters = [
  "id",
  "device_internal_id",
  "device_id",
  "user_id",
  "comment",
  //"list",
  "address",
];
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
            :href="route('devices.create', route().params.router)"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
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
    </template>

    <template v-slot:content>
      <div v-if="props.totalDevicesCount > 0">
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
        <h2>No hay datos para mostrar</h2>
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
    success: String,
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
      const link = route("routers.devices", route().params.router);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

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
      // const toast = useToast();
      this.rows = this.devices.data;
      // if (this.success) {
      //   toast.success(this.success, {
      //     position: POSITION.TOP_CENTER,
      //     draggable: true,
      //   });
      // }
    },

    inv_devices() {
      this.inv_devices_ref = this.inv_devices;
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
