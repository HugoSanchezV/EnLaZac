<script setup>
import { toRefs } from "vue";

const props = defineProps({
  phones: Object,
  pagination: Object,
  totalPhonesCount: Number,
});

const headers = ["id", "número", "Acciones"];
const filters = ["id", "número"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="block md:flex md:justify-between">
        <div class="mb-2 md:mb-0">
          <h4>Pre-registro</h4>
        </div>
        <div class="md:flex gap-1">
          <Link
            :href="route('usuarios.pre.register.create')"
            method="get"
            class="flex justify-center md:justify-betweenitems-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 18px">
              add_call
            </span>

            Agregar Número
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalPhonesCount > 0">
          <pre-register-table
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :show="true"
            :edit="true"
            :del="true"
            @search="search"
          ></pre-register-table>

          <!-- <base-pagination
            v-if="phones.data.length > 0"
            :links="phones.links"
            :pagination="pagination"
            :current="phones.current_page"
            :total="phones.last_page"
            :data="{
              q: q,
              attribute: attribute,
              type: type,
              order: order,
            }"
          ></base-pagination> -->
          <!-- <h2
            v-else
            class="flex justify-center mt-4 bg-gray-400 text-white py-2"
          >
            No se encontró ningún resultado de "{{ q }}"
          </h2> -->
        </div>
        <div v-else>
          <h2>No hay números para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";
import PreRegisterTable from "./PreRegisterTable.vue";

export default {
  components: {
    Link,
    DashboardBase,
    PreRegisterTable,
    BasePagination,
  },

  props: {
    phones: Object,
    pagination: Object,
    success: String,
    totalUsersCount: Number,
  },

  data() {
    return {
      rows: this.phones.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("usuarios.pre.register");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "número") {
        this.attribute = "phone";
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
    phones() {
      this.rows = this.phones.data;
    },
  },
};
</script>
