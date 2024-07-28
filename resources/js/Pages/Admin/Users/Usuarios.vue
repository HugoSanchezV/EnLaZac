<script setup>
import { onMounted, toRefs } from "vue";
import { watch } from "vue";

const props = defineProps({
  users: Object,
  pagination: Object,
  success: String,
});

const { users, success } = toRefs(props);

watch(success, (newValue) => {
  if (newValue) {
    // alert(newValue);
    //success.value = null
  }
});

const headers = ["id", "Nombre", "Alias", "Email", "Rol", "Acciones"];
const filters = ["todo", "id", "nombre", "alias", "email", "rol"];
</script>

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
        <div v-if="users.data.length > 0">
          <!-- Esta es el inicio de la tabla -->
          <base-table-users
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
          ></base-table-users>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            :links="users.links"
            :pagination="pagination"
            :current="users.current_page"
            :total="users.last_page"
          ></base-pagination>
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
import { POSITION, useToast } from "vue-toastification";

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
  },

  data() {
    return {
      rows: this.users.data,
    };
  },

  watch: {
    users() {
      const toast = useToast();
      this.rows = this.users.data;
      toast.success(this.success, {
        position: POSITION.TOP_CENTER,
        draggable: true,
      });
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
