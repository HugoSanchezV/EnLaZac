<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  devicesStatus: Object,
  pagination: Object,
  totalDevicesCount: Number,
  countFails: Number,
});

const { routers, success, error } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

watch(error, (newValue) => {
  if (newValue) {
    toast.error(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = [ "Id", "dirección", "Estado", "Enviado"];
const filters = [ "id", "dirección", "estado","enviado"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Estados de los dispositivos</h2>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="totalDevicesCount > 0">
        <!-- Esta es el inicio de la tabla -->
        <ping-status
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="true"
          :edit="true"
          :del="true"
          @search="search"
        >
        </ping-status>
        <base-pagination
          v-if="devicesStatus.data.length > 0"
          :links="devicesStatus.links"
          :pagination="pagination"
          :current="devicesStatus.current_page"
          :total="devicesStatus.last_page"
          :data="{
            q: q,
            order: order,
            type: type,
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
import PingStatus from "./PingStatus.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    RouterTable,
    BasePagination,
    // BasePagination,
  },

  props: {
    devicesStatus: Object,
    pagination: Object,
    totalDevicesCount: Number,
    countFails: Number,
  },

  data() {
    return {
      rows: this.devicesStatus.data,
      q: "",
      order: "id",
      type: "todos",
    };
  },

  methods: {
    search(props) {
      const link = route("routers");

      this.q = props.searchQuery;
      this.order = props.order;
      this.type = props.type;

      if (this.order === "dirección") {
        this.order = "address";
      }

      if (this.order === "ip") {
        this.order = "device_ip";
      }

      if (this.order === "estado") {
        this.order = "message";
      }

      if (this.order === "") {
        this.order = "status";
      }

      if (this.order === "estado") {
        this.order = "status";
      }

      this.$inertia.get(
        link,
        { q: this.q, order: this.order, type: this.type },
        { preserveState: true, replace: true }
      );
    },
  },
};
</script>
