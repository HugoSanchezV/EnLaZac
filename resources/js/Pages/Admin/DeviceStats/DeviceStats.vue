<script setup>
import { toRefs } from "vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
import BaseImportExcel from "@/Components/Base/Excel/BaseImportExcel.vue";

const props = defineProps({
  performance: Object,
});

const { performance } = toRefs(props);

</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h4>Usuarios</h4>
        </div>
        <div>
          <Link

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

            Crear Usuario
          </Link>
        </div>
      </div>
    </template>
    <template v-slot:content>
     
          <!-- Esta es el inicio de la tabla -->

          <!-- Este es el fin de la tabla -->

      
    
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";

export default {
  components: {
    Link,
    DashboardBase,
  },

  props: {
    performance: Object,
    success: String,
    
  },

  data() {
    return {
      rows: this.performance.data,
      q: "",
      attribute: "id",
      type: "todos",
      order: "ASC",
    };
  },

  watch: {
    performance() {
      this.rows = this.performance.data;
    },
  },
};
</script>
