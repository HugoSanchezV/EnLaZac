<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
import BaseImportExcel from "@/Components/Base/Excel/BaseImportExcel.vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  users: Object,
  pagination: Object,
  totalUsersCount: Number,
});

const { users } = toRefs(props);
const toRouteExport = "usuarios.excel";
const toImportRoute = "usuarios.import.excel";

const headers = ["id", "Nombre", "Alias", "Email", "número", "Rol", "Acciones"];
const filters = ["id", "nombre", "alias", "email", "número"];
const headingsImport = "nombre, alias, email, numero, password, role";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="block md:flex md:justify-between">
        
        <div class="mb-2 md:mb-0">
          <h4>Usuarios</h4>
        </div>
        <div class="md:flex gap-1">
          <Link
            :href="route('usuarios.pre.register')"
            method="get"
            class="mb-1 md:mb-0 flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px">
              smartphone
            </span>
            Pre-registro Cliente
          </Link>
          <Link
            :href="route('usuarios.create')"
            method="get"
            class="flex justify-center md:justify-betweenitems-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
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

            Crear Usuario
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalUsersCount > 0">
          <div class="flex justify-center md:justify-start">
            <base-export-excel
              :toRouteExport="toRouteExport"
            ></base-export-excel>
            <base-import-excel
              @click="openModal"
              :toImportRoute="toImportRoute"
              :headings="headingsImport"
            >
            </base-import-excel>
          </div>

          <!-- Esta es el inicio de la tabla -->
          <base-table-users
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-users>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="users.data.length > 0"
            :links="users.links"
            :pagination="pagination"
            :current="users.current_page"
            :total="users.last_page"
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
          <h2>No hay Usuarios para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableUsers from "@/Components/Base/BaseTableUsers.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableUsers,
    BasePagination,
  },

  props: {
    users: Object,
    pagination: Object,
    success: String,
    totalUsersCount: Number,
  },

  data() {
    return {
      rows: this.users.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("usuarios");

      if (props.filter === "rol") {
        props.filter = "admin";

        if (props.searchQuery === "cliente") {
          props.searchQuery = 0;
        } else if (props.searchQuery === "coordinador") {
          props.searchQuery = 2;
        } else if (props.searchQuery === "tecnico") {
          props.searchQuery = 3;
        }
      }

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "nombre") {
        this.attribute = "name";
      }

      if (this.attribute === "número") {
        this.attribute = "phone";
      }

      if (this.type === "cliente") {
        this.type = 0;
      } else if (this.type === "coordinador") {
        this.type = 2;
      } else if (this.type === "tecnico") {
        this.type = 3;
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
    users() {
      this.rows = this.users.data;
    },
  },
};
</script>
