<script setup>
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  pingDevice: Object,
  router: Object,
  pagination: Object,
  totalPingDeviceCount: Number,
  totalDeviceFail: Number,
  users: Object,
});

const headers = [
  "id",
  "dispositivo",
  "router",
  "ip",
  "encargado",
  "estado",
  "creado",
  "acciones",
];
const filters = ["id", "ip", "encargado", "creado"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div class="flex justify-center items-center gap-2">
          <h4>Hisotial de Pings</h4>
        </div>

        <div class="flex justify-center items-center gap-2">
          <span class="material-symbols-outlined" style="font-size: 35px">
            router
          </span>
          <span v-if="router" class="bg-slate-200 rounded-md px-2 py-1">
            {{ router?.ip_address }}1
          </span>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalPingDeviceCount > 0">
          <!-- Esta es el inicio de la tabla -->
          <base-table-ping-device
            :headers="headers"
            :rows="rows"
            :routerObject="router ?? null"
            :users="users"
            :totalDeviceFail="totalDeviceFail"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-ping-device>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="pingDevice.data.length > 0"
            :links="pingDevice.links"
            :pagination="pagination"
            :current="pingDevice.current_page"
            :total="pingDevice.last_page"
            :data="{
              q: q,
              attribute: attribute,
              type: type,
              order: order,
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
          <h2>No hay Pings para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";

import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTablePingDevice from "./BaseTablePingDevice.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTablePingDevice,
    BasePagination,
  },

  props: {
    pingDevice: Object,
    router: {
      type: Object,
      defauld: null,
    },
    pagination: Object,
    success: String,
    totalPingDeviceCount: Number,
    users: Object,
  },

  data() {
    return {
      rows: this.pingDevice.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.order = props.order;

      let $url = this.router
        ? "router.device.ping.historie"
        : "device.ping.historie";

      const link = route($url, this?.router?.id ?? null);

      // if (this.attribute === "dispositivo") {
      //   this.attribute = "device_id";
      // }

      // if (this.attribute === "estado") {
      //   this.attribute = "status";
      // }

      if (this.attribute === "ip") {
        this.attribute = "device_id";
      }

      if (this.attribute === "creado") {
        this.attribute = "created_at";
      }

      if (this.attribute === "encargado") {
        this.attribute = "user_id";
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
    pingDevice() {
      this.rows = this.pingDevice.data;
    },
  },
};
</script>
