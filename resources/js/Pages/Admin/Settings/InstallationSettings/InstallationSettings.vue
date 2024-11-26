<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";

const props = defineProps({
  installationSettings: Object,
  pagination: Object,
  totalInstallationSettingsCount: Number,
});

const { installationSt } = toRefs(props);
// const toRouteExport = "contracts.excel";

//const headers = ["Id", "Usuarios", "Plan Internet","Fecha de Inicio","Fecha de Terminación","¿Activo?", "Dirección", "Geolocación", "Acciones"];
const filters = ["id", "usuario", "mes extendido"];

const headers = ["Id", "Usuario", "Mes Extendido", "Acciones"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Configuración de Instalación</h2>
        </div>
        <div>
          <Link
            :href="route('settings.installation.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px">
              settings_input_antenna
            </span>

            Crear Instalación Personalizada
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalInstallationSettingsCount > 0">
          <!-- <base-export-excel :toRouteExport="toRouteExport"></base-export-excel> -->
          <!-- Esta es el inicio de la tabla -->
          <base-table-installation-settings
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-installation-settings>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="installationSettings.data.length > 0"
            :links="installationSettings.links"
            :pagination="pagination"
            :current="installationSettings.current_page"
            :total="installationSettings.last_page"
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
import BaseTableInstallationSettings from "@/Components/Base/BaseTableInstallationSettings.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableInstallationSettings,
    BasePagination,
  },

  props: {
    installationSettings: Object,
    pagination: Object,
    totalInstallationSettingsCount: Number,
  },

  data() {
    return {
      rows: this.installationSettings.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("settings.installation");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "usuario") {
        this.attribute = "installation_id";
      }
      if (this.attribute === "mes extendido") {
        this.attribute = "exemption_months";
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
    installationSettings() {
      this.rows = this.installationSettings.data;
    },
  },
};
</script>
