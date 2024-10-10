<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  histories: Object,
  pagination: Object,
  success: String,
});

const { histories, success } = toRefs(props);
const toast = useToast();
const toRouteExport = "historieDevices.excel";

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }

  success.value = "";
});

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
          <h2>Hisotirial del Inventario</h2>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="props.histories.data.length > 0">
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
