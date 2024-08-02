<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  routers: Object,
  pagination: Object,
  success: String,
});

const { routers, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = ["id", "usuario", "ip", "Acciones"];
const filters = ["id", "usuario", "ip"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Routers</h2>
        </div>
        <div>
          <Link
            :href="route('routers.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="size-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
              />
            </svg>

          Crear Router
          </Link>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="routers.data.length > 0">
        <!-- Esta es el inicio de la tabla -->
        <router-table
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="true"
          :edit="true"
          :del="true"
          @search="search"
        >
        </router-table>
        <!-- Este es el fin de la tabla -->
        <!-- <base-pagination
            :links="users.links"
            :pagination="pagination"
            :current="users.current_page"
            :total="users.last_page"
            :data="{
              'q': q,
              'order': order,
              'type': type,
            }"
          ></base-pagination> -->
      </div>

      <div v-else>
        <h2>No hay datos para mostrar</h2>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import RouterTable from "./RouterTable.vue";

export default {
  components: {
    Link,
    DashboardBase,
    RouterTable,
    // BasePagination,
  },

  props: {
    routers: Object,
    pagination: Object,
    success: String,
  },

  data() {
    return {
      rows: this.routers.data,
      q: "",
      order: "id",
      type: "todos",
    };
  },

  methods: {
    search(props) {
      const link = route("routers");

      this.q = props.searchQuery;
      this.order = props.order;
      this.type = props.type;

      if (this.order === 'usuario') {
        this.order = 'user'
      }

      if (this.order === 'ip') {
        this.order = 'ip_address'
      }

      this.$inertia.get(
        link,
        { q: this.q, order: this.order, type: this.type },
        { preserveState: true, replace: true }
      );
    },
  },

  watch: {
    routers() {
      const toast = useToast();
      this.rows = this.routers.data;
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
