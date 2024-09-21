<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  plans: Object,
  pagination: Object,
  success: String,
  totalPlansCount: Number,
});

const { plans, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});
//  [   Burst Limit   ]  [Burst Threshold]  [   Burst Time   ] [   Limite At   ] [   Max Limit   ]
const headers = [
  "Id",
  "Nombre",
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

            Crear PI
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
import { useToast, POSITION } from "vue-toastification";
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
    }
  },
  watch: {
    plans() {
      const toast = useToast();
      this.rows = this.plans.data;
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
