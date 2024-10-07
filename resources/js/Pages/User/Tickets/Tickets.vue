<script setup>
import { toRefs, watch } from "vue";
import { useToast, POSITION } from "vue-toastification";

const props = defineProps({
  tickets: Object,
  pagination: Object,
  success: String,
  totalTicketsCount: Number,
});

const { tickets, success } = toRefs(props);
const toast = useToast();

watch(success, (newValue) => {
  if (newValue) {
    toast.success(newValue, {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  }
});

const headers = [
  "Id",
  "Asunto",
  "Descripción",
  "Estado",
  "Creación",
  "Acciones",
];
const filters = [
  "id",
  "subject",
  "description",
  "status",
  "user_id",
  "created_at",
];
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Tickets para usuario</h2>
        </div>
        <div>
          <Link
            :href="route('tickets.create')"
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

            Crear Tickets
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

/*
  // Variable global para el mapa y los marcadores
  var map;
  var markers = [];

  // Definir la función para inicializar el mapa
  function initMap() {
      // Crear el mapa con un centro y zoom inicial
      map = new google.maps.Map(document.getElementById('map-container'), {
          zoom: 8,
          center: {lat: 4.134282, lng: -73.637742} // Establecer un centro predeterminado como punto de partida
      });

      // Cargar las coordenadas iniciales
      var sedeCoordinates = {!! json_encode($sedeCoordinates) !!};
      addMarkers(sedeCoordinates);
  }

  // Función para agregar marcadores al mapa
  function addMarkers(sedeCoordinates) {
      clearMarkers(); // Limpiar los marcadores existentes
      sedeCoordinates.forEach(function(coordinate) {
          var parts = coordinate.split(',');
          var lat = parseFloat(parts[0]);
          var lng = parseFloat(parts[1]);

          var marker = new google.maps.Marker({
              position: {lat: lat, lng: lng},
              map: map
          });
          markers.push(marker);
      });
  }

  // Función para limpiar los marcadores del mapa
function clearMarkers() {
    markers.forEach(function(marker) {
        marker.setMap(null);
    });
    markers = [];
}

// Agregar un event listener para el formulario de búsqueda
  document.getElementById('searchForm').addEventListener('submit', function(event) {
      event.preventDefault(); // Prevenir el envío del formulario

      // Hacer la solicitud de búsqueda (ajustar según sea necesario para tu configuración)
      var formData = new FormData(this);
      var searchParams = new URLSearchParams(formData).toString();

      fetch('{{ route("agendas.index") }}?' + searchParams)
          .then(response => response.json())
          .then(data => {
              // Actualizar los datos de la tabla y los marcadores del mapa
              updateTable(data.agendas);
              updateMap(data.sedeCoordinates);
          });
  });

  function updateTable(agendas) {
      // Actualizar la tabla de resultados (implementar según tu estructura de tabla)
      // Aquí debes agregar la lógica para actualizar la tabla con los nuevos datos
  }
  function updateMap(sedeCoordinates){
    addMarkers(sedeCoordinates);
  }
*/

<script>
import { Link } from "@inertiajs/vue3";
import { useToast, POSITION } from "vue-toastification";
import DashboardBase from "@/Pages/DashboardBase.vue";
import BaseTableTickets from "@/Components/Base/BaseTableTicketsForUser.vue";
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
      this.order = props.order;

      if (this.attribute === "id") {
        this.attribute = "id";
      }

      if (this.attribute === "status") {
        this.attribute = "status";
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
      const toast = useToast();
      this.rows = this.tickets.data;
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
