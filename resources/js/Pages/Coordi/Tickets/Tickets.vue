<script setup>
import { toRefs } from "vue";

const props = defineProps({
  tickets: Object,
  pagination: Object,
  totalTicketsCount: Number,
});

const { tickets } = toRefs(props);

const headers = [
  "Id",
  "Asunto",
  "Descripción",
  "Estado",
  "Cliente",
  "Encagado",
  "Creación",
  "Acciones",
];
const filters = [
  "id",
  "asunto",
  "descripción",
  "estado",
  "usuario",
  "Encargado",
  "creación",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Tickets</h2>
        </div>
        <div>
          <Link
            :href="route('tickets.create')"
            method="get"
            class="flex justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 18px">
              summarize
            </span>

            Crear Ticket
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalTicketsCount > 0">
          <!-- Esta es el inicio de la tabla -->
          <base-table-tickets
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></base-table-tickets>
          <!-- Este es el fin de la tabla -->
          <base-pagination
            v-if="tickets.data.length > 0"
            :links="tickets.links"
            :pagination="pagination"
            :current="tickets.current_page"
            :total="tickets.last_page"
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
          <h2>No hay Tickets para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableTickets from "@/Components/Base/BaseTableTickets.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableTickets,
    BasePagination,
  },

  props: {
    tickets: Object,
    pagination: Object,
    success: String,
    totalTicketsCount: Number,
  },

  data() {
    return {
      rows: this.tickets.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },
  methods: {
    search(props) {
      const link = route("tickets");

      console.log(props.searchQuery);

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;

      if (this.attribute === "id") {
        this.attribute = "id";
      }
      if (this.attribute === "asunto") {
        this.attribute = "subject";
      }

      if (this.attribute === "descripción") {
        this.attribute = "description";
      }

      if (this.attribute === "estado") {
        this.attribute = "status";
      }
      if (this.attribute === "usuario") {
        this.attribute = "user_id";
      }
      if (this.attribute === "creación") {
        this.attribute = "created_at";
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
    tickets() {
      this.rows = this.tickets.data;
    },
  },
};
</script>
