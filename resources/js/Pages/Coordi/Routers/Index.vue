<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { onMounted, toRefs, watch } from "vue";
import { useToast, POSITION, TYPE } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";

const props = defineProps({
  routers: Object,
  pagination: Object,
  totalRoutersCount: Number,
  schedule: Number,
});

onMounted(() => {
  if (props.schedule == true) {
    const checkbox = document.getElementById("activated");
    if (checkbox) {
      checkbox.checked = true;
    } else {
      console.error("El elemento con ID 'activated' no existe.");
    }
  }
});
let enviado = false;
const updateStatus = () => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "¿Estas seguro de Cambiar el estado de la automatización?",
        accept: true,
        cancel: true,
        textConfirm: "Aceptar",
      },

      listeners: {
        accept: () => {
          // event.stopPropagation();     // Evitar que el evento burbujee a otros elementos
          const url = route("routers.scheduled.ping", "ping-routers");

          router.put(url, () => {
            onError: (error) => {
              toast.error("Ha Ocurrido un Error, Intentalo más Tarde");
            };
          });
          enviado = true;
        },

        cancel: () => {
          if(!enviado)
          {
            const activatedCheckbox = document.getElementById("activated");
            activatedCheckbox.checked = props.schedule;
          }else{
            enviado = false;
          }
          //salert("GOlar");
        },
      },
    },
    {
      type: TYPE.INFO,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
};
const { routers } = toRefs(props);
const headers = [
  "SYNC",
  "id",
  "usuario",
  "ip",
  "Dipositivos",
  "Disp. Activos",
  "Acciones",
];

const filters = ["id", "usuario", "ip"];
const headingsImport = "usuario, direccion, password"
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between flex-col md:flex-row">
        <div>
          <h2>Routers</h2>
        </div>
        <div class="flex gap-2 flex-col md:flex-row">
          
          <!-- <div>
            <Link
              :href="route('pings')"
              method="get"
              class="flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
            >
              <span class="material-symbols-outlined"> network_ping </span>
              Historial
            </Link>
          </div> -->
          <!-- <div>
            <Link
              :href="route('routers.create')"
              method="get"
              class="flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2.5 px-3 text-sm rounded-md"
              ><svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 16 16"
                fill="currentColor"
                class="size-4"
              >
                <path d="M6 6v4h4V6H6Z" />
                <path
                  fill-rule="evenodd"
                  d="M5.75 1a.75.75 0 0 0-.75.75V3a2 2 0 0 0-2 2H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3v.75H1.75a.75.75 0 0 0 0 1.5H3a2 2 0 0 0 2 2v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13h.75v1.25a.75.75 0 0 0 1.5 0V13a2 2 0 0 0 2-2h1.25a.75.75 0 0 0 0-1.5H13v-.75h1.25a.75.75 0 0 0 0-1.5H13V6.5h1.25a.75.75 0 0 0 0-1.5H13a2 2 0 0 0-2-2V1.75a.75.75 0 0 0-1.5 0V3h-.75V1.75a.75.75 0 0 0-1.5 0V3H6.5V1.75A.75.75 0 0 0 5.75 1ZM11 4.5a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5h6Z"
                  clip-rule="evenodd"
                />
              </svg>

              Crear Router
            </Link>
          </div> -->
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div v-if="totalRoutersCount > 0">
        <div class="flex justify-center md:justify-start">

        </div>

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
        <base-pagination
          v-if="routers.data.length > 0"
          :links="routers.links"
          :pagination="pagination"
          :current="routers.current_page"
          :total="routers.last_page"
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
        <h2>No hay datos para mostrar</h2>
      </div>
    </template>
  </dashboard-base>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import DashboardBase from "@/Pages/DashboardBase.vue";
import RouterTable from "./RouterTable.vue";
import BasePagination from "@/Components/Base/BasePagination.vue";

export default {
  components: {
    Link,
    DashboardBase,
    RouterTable,
    BasePagination,
    // BasePagination,
  },

  props: {
    routers: Object,
    pagination: Object,
    success: String,
    totalRoutersCount: Number,
  },

  data() {
    return {
      rows: this.routers.data,
      q: "",
      attribute: "id",
      type: "todos",
      isActived: false,
    };
  },

  methods: {
    search(props) {
      const link = route("routers");

      this.q = props.searchQuery;
      this.attribute = props.attribute;
      this.type = props.type;
      this.order = props.order;

      if (this.attribute === "usuario") {
        this.attribute = "user";
      }

      if (this.attribute === "ip") {
        this.attribute = "ip_address";
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
    routers() {
      const toast = useToast();
      this.rows = this.routers.data;
    },
  },
};
</script>
