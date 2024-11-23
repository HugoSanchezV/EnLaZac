<script setup>
import { toRefs } from "vue";

const props = defineProps({
  installation: Object,
  pagination: Object,
  totalInstallationCount: Number,
});

const { installation } = toRefs(props);
// const toRouteExport = "contracts.excel";

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = [
  "id",
  "contrato",
  'descripción',
  "fecha asignada",
];

const headers = [
  "Id",
  "Contracto",
  "Descripción",
  "Fecha asignada",
  "Acciones",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Instalaciones</h2>
        </div>
        <div class="flex gap-2">
          <Link
            :href="route('settings.installation')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px;"> edit_calendar </span>

             Primer Mes - Plazo
          </Link>
          <Link
            :href="route('installation.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px;"> settings_input_antenna </span>

            Crear Instalación
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalInstallationCount > 0">
          <!-- <base-export-excel :toRouteExport="toRouteExport"></base-export-excel> -->
          <!-- Esta es el inicio de la tabla -->
          <base-table-installations
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-installations>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="installation.data.length > 0"
            :links="installation.links"
            :pagination="pagination"
            :current="installation.current_page"
            :total="installation.last_page"
            :data="{
              q: q,
              attribute: attribute,
              order: order,
              type: type,
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
          <h2>No hay Instalaciones para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>
<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableInstallations from "@/Components/Base/BaseTableInstallations.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableInstallations,
    BasePagination,
  },

  props: {
    installation: Object,
    pagination: Object,
    totalInstallationCount: Number,
  },

  data() {
    return {
      rows: this.installation.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("installation");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;


      if (this.attribute === "contrato") {
        this.attribute = "contract_id";
      }
      if (this.attribute === "descripción") {
        this.attribute = "description";
      }

      if (this.attribute === "fecha asignada") {
        this.attribute = "assigned_date";
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
    installation() {
      this.rows = this.installation.data;
    },
  },
};
</script>
