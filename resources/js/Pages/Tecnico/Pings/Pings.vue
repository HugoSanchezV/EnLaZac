<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  pings: Object,
  pagination: Object,
  totalPingsCount: Number,
});


// watch(success, (newValue) => {
//   if (newValue) {
//     toast.success(newValue, {
//       position: POSITION.TOP_CENTER,
//       draggable: true,
//     });
//   }
// });

const headers = ["id", "Router", "Contenido", "Fecha", "Acciones"];
const filters = ["id", "router", "contenido", "fecha"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Ping automatizados</h4>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalPingsCount > 0">
          <!-- Esta es el inicio de la tabla -->
          <base-table-pings
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-pings>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="pings.data.length > 0"
            :links="pings.links"
            :pagination="pagination"
            :current="pings.current_page"
            :total="pings.last_page"
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
import BaseTablePings from "./BaseTablePings.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTablePings,
    BasePagination,
  },

  props: {
    pings: Object,
    pagination: Object,
    success: String,
    totalPingsCount: Number,
  },

  data() {
    return {
      rows: this.pings.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("technical.pings");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "router") {
        this.attribute = "router_id";
      }

      if (this.attribute === "contenido") {
        this.attribute = "content";
      }

      if (this.attribute === "fecha") {
        this.attribute = "created_at";
      }

      this.$inertia.get(
        link,
        { q: this.q, attribute: this.attribute, type: this.type, order:this.order },
        { preserveState: true, replace: true }
      );
    },
  },

  watch: {
    pings() {
      // const toast = useToast();
      this.rows = this.pings.data;
      // if (this.success) {
      //   toast.success(this.success, {
      //     position: POSITION.TOP_CENTER,
      //     draggable: true,
      //   });
      // }
    },
  },

  // beforeMount() {
  //   const toast = useToast();
  //   if (this.success) {
  //     toast.success(this.success, {
  //       position: POSITION.TOP_CENTER,
  //       draggable: true,
  //     });
  //   }
  // },
};
</script>
