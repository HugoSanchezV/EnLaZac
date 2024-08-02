<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  users: Object,
  pagination: Object,
  success: String,
});

const { users, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = ["id", "Nombre", "Alias", "Email", "Rol", "Acciones"];
const filters = ["id", "nombre", "alias", "email"];
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
            @search="search"
          ></base-table-users>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            :links="users.links"
            :pagination="pagination"
            :current="users.current_page"
            :total="users.last_page"
            :data="{
              'q': q,
              'order': order,
              'type': type,
            }"
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
import { useToast, POSITION } from "vue-toastification";

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
  },

  data() {
    return {
      rows: this.users.data,
      q: '',
      order: 'id',
      type: 'todos',
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

      console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.order = props.order;
      this.type = props.type;

      if (this.order === "nombre") {
        this.order = "name";
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
        { q: this.q, order: this.order, type: this.type },
        { preserveState: true, replace: true }
      );
    },
  },

  watch: {
    users() {
      const toast = useToast();
      this.rows = this.users.data;
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
