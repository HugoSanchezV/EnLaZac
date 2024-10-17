<script setup>
import { toRefs } from "vue";

const props = defineProps({
  plans: Object,
  pagination: Object,
  totalPlansCount: Number,
});

const { plans } = toRefs(props);

//  [   Burst Limit   ]  [Burst Threshold]  [   Burst Time   ] [   Limite At   ] [   Max Limit   ]
const headers = [
  "Id",
  "Nombre",
  'Precio',
  "Descripción",
  "Burst Limit (Mb/s)",
  "Burst Threshold (Mb/s)",
  "Burst Time (S)",
  "Limite At (Klb/s)",
  "Max Limit (Mb/s)",
  "Acciones",
];
const filters = [
  "id",
  "nombre",
  "descripción",
  'precio',
  "burst_limit",
  "burst_threshold",
  "burst_time",
  "limite_at",
  "max_limit",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Planes de Internet</h2>
        </div>
        <div>
          <Link
            :href="route('plans.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 18px">
              network_check
            </span>

            Crear Plan
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalPlansCount > 0">
          <!-- Esta es el inicio de la tabla -->
          <base-table-plans
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-plans>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="plans.data.length > 0"
            :links="plans.links"
            :pagination="pagination"
            :current="plans.current_page"
            :total="plans.last_page"
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
          <h2>No hay planes de internet para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTablePlans from "@/Components/Base/BaseTablePlan.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTablePlans,
    BasePagination,
  },

  props: {
    plans: Object,
    pagination: Object,
    success: String,
    totalPlansCount: Number,
  },

  data() {
    return {
      rows: this.plans.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("plans");

      console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "id") {
        this.attribute = "id";
      }
      if (this.order === "nombre") {
        this.order = "name";
      }
      if (this.order === "descripción") {
        this.order = "description";
      }
      if (this.order === "precio") {
        this.order = "price";
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
    plans() {
      this.rows = this.plans.data;
    },
  },
};
</script>
