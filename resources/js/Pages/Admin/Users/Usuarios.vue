<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Usuarios</h2>
        </div>
        <div>
          <Link
            :href="route('usuarios.create')"
            method="get"
            class="text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            Crear Usuario
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div>
          <!-- Esta es el inicio de la tabla -->
          <base-table
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="edit"
            :del="del"
          ></base-table>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            :links="users.links"
            :pagination="pagination"
            :current="users.to"
            :total="users.total"
          ></base-pagination>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";

import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTable from "@/Components/Base/BaseTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTable,
    BasePagination,
  },

  props: {
    users: Object,
    pagination: Object,
  },

  data() {
    return {
      headers: ['id', "Nombre", "Alias", "Email", "Rol", "Acciones"],
      rows: this.users.data,
      filters: ['id', "Nombre", "Alias", "Email", 'Rol'],
      show: true,
      edit: true,
      del: true,
    };
  },
};
</script>
