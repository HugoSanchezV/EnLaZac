<script setup>
import { router } from "@inertiajs/vue3";

import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "./BaseQuestion.vue";

// ACCION DE ELIMINAR
const destroy = (id) => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "¿Estas seguro de Eliminar el registro?",
        accept: true,
        cancel: true,
        textConfirm: "Eliminar",
      },

      listeners: {
        accept: () => {
          const url = route("tickets.destroy", id);

          router.delete(url, () => {
            onError: (error) => {
              toast.error("Ha Ocurrido un Error, Intentalo más Tarde");
            };
          });
        },
      },
    },

    {
      type: TYPE.WARNING,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
};
const editStatus = (id, estado) => {
    const toast = useToast();
    var str = estado.toString()
    toast({
        component: BaseQuestion,
        props: {
            message: "¿Estás seguro de cambiar el estado del ticket?",
            accept: true,
            cancel: true,
            textConfirm: "Cambiar",
        },
        listeners: {
            accept: () => {
                axios.post(route('tickets.statusUpdate', id), {
                    status: str // Enviar el nuevo estado como 'status'
                })
                .then(response => {
                    console.log('Estado actualizado exitosamente');
                    // Opcionalmente, redireccionar o actualizar la interfaz
                    window.location.reload(); // Recarga la página para reflejar los cambios
                })
                .catch(error => {
                    console.log('Error al actualizar el estado:', error.response.data);
                });
            },
        },
    }, {
        type: TYPE.WARNING,
        position: POSITION.TOP_CENTER,
        timeout: 10000,
    });
};

</script>
<style>
  .boton-modal label{
    cursor: pointer;
    transition: all 300ms ease;
  }
  .overlay{
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0, 0,0, 0.3);
    opacity: 0;
    pointer-events: none;
  }
  .container-modal{
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 380px;
    width: 100%;
    padding: 30px 20px;
    border-radius: 24px;
    background-color: #ffff;
    opacity: 0;
    pointer-events: none;

  }
  .content-modal Link{
    font-size: 14px;
    padding: 6px 12px;
    margin: 0 10px;
  }
 
  

</style>
<template>
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div
      class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4"
    >
      <!-- incio de filtros -->
      <div class="flex gap-2">
        <div>
          <button
            id="dropdownRadioButton"
            @click="toggleDropdown1"
            class="uppercase gap-2 inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5"
            type="button"
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
                d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"
              />
            </svg>

            <span class="bg-neutral-500 py-1 px-2 text-white rounded-md">
              Ordenar
            </span>

            por {{ currentFilter }}
            <svg
              class="w-2.5 h-2.5 ms-2.5"
              aria-hidden="true"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 10 6"
            >
              <path
                stroke="currentColor"
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="m1 1 4 4 4-4"
              />
            </svg>
          </button>

          <!-- Dropdown menu -->
          <div
            v-if="dropdownOpen1"
            id="dropdownRadio"
            class="z-10 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow absolute"
          >
            <ul class="p-3 space-y-1 text-sm text-gray-700">
              <li v-for="(filter, index) in filters" :key="index">
                <div class="flex items-center p-2 rounded hover:bg-gray-100">
                  <input
                    :id="'filter-radio-' + index"
                    type="radio"
                    :value="filter"
                    v-model="currentFilter"
                    @click="selectFilter(filter)"
                    name="filter-radio"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 cursor-pointer"
                  />
                  <label
                    :for="'filter-radio-' + index"
                    class="w-full ms-2 text-sm font-medium text-gray-900 rounded uppercase cursor-pointer"
                    >{{ filter }}</label
                  >
                </div>
              </li>
            </ul>
          </div>
        </div>

        <!-- Final dfrop nbuton  -->
      </div>
      <!-- final de filtros -->
      <div class="relative">
        <div
          class="absolute inset-y-0 left-0 flex items-center ps-3 pointer-events-none"
        >
          <svg
            class="w-5 h-5 text-gray-500"
            aria-hidden="true"
            fill="currentColor"
            viewBox="0 0 20 20"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <input
          type="text"
          id="table-search"
          v-model="searchQuery"
          @input="
            $emit('search', {
              searchQuery: searchQuery,
              order: currentFilter,
              type: currentUser,
            })
          "
          class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Buscar"
        />
      </div>
    </div>

    <table class="w-full text-sm text-left text-gray-500 p-10">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th></th>
          <th
            v-for="(header, index) in headers"
            :key="index"
            scope="col"
            class="px-6 py-3"
          >
            {{ header }}
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(row, rowIndex) in filteredRows"
          :key="rowIndex"
          class="bg-white border-b hover:bg-gray-100"
        >
        <!-- INICIO DEWL  -->
        <td></td>
        <!-- SELECTOR DE ESTADOS DE TICKET  -->
        <td v-for="(cell, cellIndex) in row"
          :key="cellIndex"
          class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
          <div class="flex gap-2">
          <div v-if="cellIndex === 'status'">
            <button
              id="dropdownRadioButton"
              @click="toggleDropdown(rowIndex)"
              class="uppercase gap-2 inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5"
              type="button"
            >
          
            <div>
            <h2
              v-if="cell === '0'"
              class="bg-green-500 text-white px-2 py-1 rounded flex justify-start items-center gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="size-5"
              >
                <path
                  d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"
                />
              </svg>

                Pendiente
            </h2>
            <h2
              v-else-if="cell === '1'"
              class="bg-blue-500 text-white px-2 py-1 rounded flex justify-start gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4 mr-1"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5.121 17.804A1 1 0 016.638 17h10.724a1 1 0 01.8 1.6l-5.363 6.463a1 1 0 01-1.598 0l-5.36-6.463z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zM12 10a4 4 0 100-8 4 4 0 000 8z"
                />
              </svg>

              En espera
            </h2>
            <h2
              v-else-if="cell === '2'"
              class="bg-yellow-500 text-white px-2 py-1 rounded flex justify-start gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="size-5"
              >
                <path
                  fill-rule="evenodd"
                  d="M19 5.5a4.5 4.5 0 0 1-4.791 4.49c-.873-.055-1.808.128-2.368.8l-6.024 7.23a2.724 2.724 0 1 1-3.837-3.837L9.21 8.16c.672-.56.855-1.495.8-2.368a4.5 4.5 0 0 1 5.873-4.575c.324.105.39.51.15.752L13.34 4.66a.455.455 0 0 0-.11.494 3.01 3.01 0 0 0 1.617 1.617c.17.07.363.02.493-.111l2.692-2.692c.241-.241.647-.174.752.15.14.435.216.9.216 1.382ZM4 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                  clip-rule="evenodd"
                />
              </svg>

              Trabajando
            </h2>
            <h2
              v-else-if="cell === '3'"
              class="bg-yellow-500 text-white px-2 py-1 rounded flex justify-start gap-2"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                class="size-5"
              >
                <path
                  fill-rule="evenodd"
                  d="M19 5.5a4.5 4.5 0 0 1-4.791 4.49c-.873-.055-1.808.128-2.368.8l-6.024 7.23a2.724 2.724 0 1 1-3.837-3.837L9.21 8.16c.672-.56.855-1.495.8-2.368a4.5 4.5 0 0 1 5.873-4.575c.324.105.39.51.15.752L13.34 4.66a.455.455 0 0 0-.11.494 3.01 3.01 0 0 0 1.617 1.617c.17.07.363.02.493-.111l2.692-2.692c.241-.241.647-.174.752.15.14.435.216.9.216 1.382ZM4 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
                  clip-rule="evenodd"
                />
              </svg>

              Finalizado
            </h2>
            <h2 v-else>{{ cell }}</h2>
            </div>
              <svg
                class="w-2.5 h-2.5 ms-2.5"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 10 6"
              >
                <path
                  stroke="currentColor"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="m1 1 4 4 4-4"
                />
              </svg>
            </button>

            <!-- Dropdown menu -->
            <div
              v-if="dropdownOpen[rowIndex]"
              id="dropdownRadio"
              class="z-10 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow absolute dropdown-contents"
            >
              <ul class="p-3 space-y-1 text-sm text-gray-700">
                <li v-for="(type, index) in typeStatus" :key="index">
                  <div class="flex items-center p-2 rounded hover:bg-gray-100">
                    <input
                    :id="'type-radio-' + index"
                    type="radio"
                    :value="type"
                    v-model="currentUser"
                    @click="editStatus(row.id, index)"
                    name="type-radio"
                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 cursor-pointer"
                  />
                  <label
                    :for="'type-radio-' + index"
                    class="w-full ms-2 text-sm font-medium text-gray-900 rounded uppercase cursor-pointer"
                    >{{ type }}</label
                  >
                    
                  </div>
                </li>
              </ul>
            </div>
          </div>
          <div v-else>
            {{ cell }}
          </div>

          <!-- Final dfrop nbuton  -->
        </div>

        </td>
        <!-- SELECTOR DE ESTADOS DE TICKET  -->
        
         

          <td class="flex items-stretch">
            <div class="sm:flex gap-4">
              <Link
                href="#"
                v-if="show"
                class="btn-modal flex items-center gap-2 bg-slate-500 hover:bg-slate-600 py-2 px-3 rounded-md text-white sm:mb-0 mb-1"
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
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"
                  />
                </svg>
                
                Mostrar cliente
              </Link>
              <div v-if="del">
                <button
                  @click="destroy(row.id)"
                  class="flex items-center gap-2 bg-red-500 hover:bg-red-600 py-2 px-3 rounded-md text-white sm:mb-0 mb-1"
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
                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"
                    />
                  </svg>
                  Eliminar
                </button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <span class="overlay"></span>
  <div class="container-modal">
      <div class="content-modal">
        <h2>Hola checando esta vaina</h2>
        <p>Parangaracutirimicuaro</p>
          <div class="buttons">
            <Link class="btn-cerrarflex items-center gap-2 bg-cyan-500 hover:bg-cyan-600 py-2 px-3 rounded-md text-white sm:mb-0 mb-1">Cerrar</Link>
   
          </div>
        
      </div>

    </div>
</template>
  
  <script>
import { Link, router } from "@inertiajs/vue3";
export default {
  components: {
    Link,
  },

  props: {
    headers: {
      type: Array,
      required: true,
    },
    rows: {
      type: Array,
      required: true,
    },
    filters: {
      type: Array,
      required: true,
    },

    show: {
      type: Boolean,
      required: true,
    },

    edit: {
      type: Boolean,
      required: true,
    },

    del: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      searchQuery: "",
      dropdownOpen1: false,
      dropdownOpen: {},
      currentFilter: "id",
      currentUser: "todos",
      typeStatus: ["Pendiente", "En espera", "Trabajando", "Terminado"],
    };
  },
  computed: {
    filteredRows() {
      // if (!this.searchQuery) {
      return this.rows;
      //}
      // return this.rows.filter((row) =>
      //   row.some((cell) =>
      //     cell.toString().toLowerCase().includes(this.searchQuery.toLowerCase())
      //   )
      // );
    },
  },
  methods: {
    toggleDropdown1() {
      this.dropdownOpen1 = !this.dropdownOpen1;
    },

    toggleDropdown(index) {
    
      this.dropdownOpen[index] = !this.dropdownOpen[index];
    },
    
    selectFilter(filter) {
      this.currentFilter = filter;
      this.toggleDropdown1();
      this.$emit("search", {
        searchQuery: this.searchQuery,
        order: this.currentFilter,
        type: this.currentUser,
      });
    },

    selectUser(user, index) {
      this.currentUser = user;
      this.toggleDropdown(index);
      this.$emit("search", {
        searchQuery: this.searchQuery,
        order: this.currentFilter,
        type: this.currentUser,
      });
    },

    filterData() {
      console.log(this.searchQuery);
    },
  },
};
</script>
  
  <style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 8px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}
th {
  padding: 0.8rem;
  background-color: rgb(229 231 235);
}

thead th:first-child {
  border-top-left-radius: 0.25rem;
}

thead th:last-child {
  border-top-right-radius: 0.25rem;
}
</style>
  