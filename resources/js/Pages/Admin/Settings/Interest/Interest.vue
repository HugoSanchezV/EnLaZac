<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  interest: Object,
  pagination: Object,
  success: String,
  totalInterestCount: Number,
});

const { interest, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = ["id", "Nombre", "Monto", "Acciones"];
const filters = ["id", "nombre", "monto"];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Intereses a cobrar</h4>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div>
        <div v-if="props.totalInterestCount > 0">
          <!-- Esta es el inicio de la tabla -->
          <BaseTableInterest
            :headers="headers"
            :rows="rows"
            :filters="filters"
            :edit="true"
            :del="true"
            @search="search"
          ></BaseTableInterest>
        </div>
        <div v-else class="flex justify-center uppercase font-bold">
          <h2>No hay Intereses para mostrar</h2>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";

import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableInterest from "@/Components/Base/BaseTableInterest.vue";

export default {
  components: {
    Link,
    DashboardBase,
    BaseTableInterest,  
  },

  props: {
    interest: Object,
    pagination: Object,
    success: String,
    totalInterestCount: Number,
  },

  data() {
    return {
      rows: this.interest.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  methods: {
    search(props) {
      const link = route("interest");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "nombre") {
        this.attribute = "name";
      }

      if (this.attribute === "monto") {
        this.attribute = "amount";
      }

      this.$inertia.get(
        link,
        { q: this.q, attribute: this.attribute, type: this.type, order:this.order },
        { preserveState: true, replace: true }
      );
    },
  },

  watch: {
    interest() {
      const toast = useToast();
      this.rows = this.pings.data;
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
