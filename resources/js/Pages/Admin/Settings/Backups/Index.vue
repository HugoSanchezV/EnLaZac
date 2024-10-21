<script setup>
import { toRefs } from "vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  backups: Object,
  pagination: Object,
  totalBackupCount: Number,
});

const { backups, totalBackupCount } = toRefs(props);

const headers = ["id", "path", "user_id", "created_at", "acciones"];
const filters = ["id", "path", "user_id", "created_id"];

const createBackup = () => {
  const routeBackUp = route("backups.create");

  router.post(routeBackUp);
};
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>BackUps {{ totalBackupCount }}</h2>
        </div>

        <div>
          <button
            :href="route('usuarios.create')"
            @click="createBackup"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined"> cloud_download </span>

            BackUp Ahora
          </button>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div v-if="totalBackupCount > 0">
        <!-- Esta es el inicio de la tabla -->
        <backups-table
          :headers="headers"
          :rows="rows"
          :filters="filters"
          :show="false"
          :edit="false"
          :del="true"
          @search="search"
        >
        </backups-table>
        <base-pagination
          v-if="backups.data.length > 0"
          :links="backups.links"
          :pagination="pagination"
          :current="backups.current_page"
          :total="backups.last_page"
          :data="{
            q: q,
            type: type,
            order: order,
            attribute: attribute,
          }"
        ></base-pagination>
        <h2 v-else class="flex justify-center mt-4 bg-gray-400 text-white py-2">
          No se encontró ningún resultado de "{{ q }}"
        </h2>
      </div>
      <div v-else>
        <h2>No hay historia de ningún dispositivo</h2>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BackupsTable from "./BackupsTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";
// import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BackupsTable,
    // //RouterTable,
    BasePagination,
  },

  props: {
    backups: Object,
    pagination: Object,
    success: String,
  },

  data() {
    return {
      rows: this.backups.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "DESC",
    };
  },

  methods: {
    search(props) {
      const link = route("backups");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      //   if (props.attribute === "mac address") {
      //     this.attribute = "mac_address";
      //   }

      //   if (props.attribute === "descripción") {
      //     this.attribute = "description";
      //   }

      //   if (props.attribute === "marca") {
      //     this.attribute = "brand";
      //   }
      this.$inertia.get(
        link,
        {
          q: this.q,
          attribute: this.attribute,
          type: this.type,
          order: this.order,
        },
        { preserveState: true, replace: true, preserveScroll: true }
      );
    },
  },

  watch: {
    backups() {
      this.rows = this.backups.data;
    },
  },
};
</script>
