<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  pingDevice: Object,
  pagination: Object,
  success: String,
  totalPingDeviceCount: Number,
  totalDeviceFail: Number,
  users: Object,
});

const { pings, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = ["id", "dispositivo", "router", "dirección", "encargado", "estado", "creado", "acciones"];
const filters = ["id", "dispositivo","router", "dirección", "encargado", "estado", "creado"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Pings a dispositivos</h4>
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
            :users="users"
            :totalDeviceFail= "totalDeviceFail"
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
      const link = route("pingDevice");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "dispositivo") {
        this.attribute = "device_id";
      }
      if (this.attribute === "router") {
        this.attribute = "router_id";
      }

      if (this.attribute === "dirección") {
        this.attribute = "address";
      }

      if (this.attribute === "estado") {
        this.attribute = "status";
      }

      if (this.attribute === "creado") {
        this.attribute = "create_at";
      }
      
      if (this.attribute === "encargado") {
        this.attribute = "user_id";
      }

      this.$inertia.get(
        link,
        { q: this.q, attribute: this.attribute, type: this.type, order:this.order },
        { preserveState: true, replace: true }
      );
    },
  },

  watch: {
    pingDevice() {
      const toast = useToast();
      this.rows = this.pingDevices.data;
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
