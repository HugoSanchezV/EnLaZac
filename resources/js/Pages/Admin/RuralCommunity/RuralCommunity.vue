<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
import BaseImportExcel from "@/Components/Base/Excel/BaseImportExcel.vue";

const props = defineProps({
  community: Object,
  pagination: Object,
  totalCommunityCount: Number,
});

const { community } = toRefs(props);
//const toRouteExport = "usuarios.excel";
//const toImportRoute = "usuarios.import.excel";

const headers = ["id", "Nombre", "Costo de instalación","Acciones"];
const filters = ["id", "nombre", "costo de instalación"];
//const headingsImport = "nombre, alias, email, password, role";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Comunidades Rurales</h4>
        </div>
        <div>
          <Link
            :href="route('rural-community.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-5"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"
              />
            </svg>

            Crear Comunidad
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalCommunityCount > 0">
          <div class="flex justify-center md:justify-start">
            <!-- <base-export-excel
              :toRouteExport="toRouteExport"
            ></base-export-excel>
            <base-import-excel
              @click="openModal"
              :toImportRoute="toImportRoute"
              :headings="headingsImport"
            >
            </base-import-excel> -->
          </div>

          <!-- Esta es el inicio de la tabla -->
          <base-table-communities
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-communities>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="community.data.length > 0"
            :links="community.links"
            :pagination="pagination"
            :current="community.current_page"
            :total="community.last_page"
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
          <h2>No hay Comunidades para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableCommunities from "@/Components/Base/BaseTableCommunities.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableCommunities,
    BasePagination,
  },

  props: {
    community: Object,
    pagination: Object,
    success: String,
    totalCommunityCount: Number,
  },

  data() {
    return {
      rows: this.community.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("rural-community");


      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "nombre") {
        this.attribute = "name";
      }
      if (this.attribute === "costo de instalación") {
        this.attribute = "installation_cost";
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
    community() {
      this.rows = this.community.data;
    },
  },
};
</script>
