<script setup>
import { router } from "@inertiajs/vue3";

import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "./BaseQuestion.vue";

import FilterOrderBase from "./FilterOrderBase.vue";

// ACCION DE ELIMINAR

const getOriginal = (data) => {
  if (data === "id") {
    return "id";
  }

  if (data === "contrato") {
    return "contract_id";
  }

  if (data === "descripción") {
    return "description";
  }

  if (data === "monto") {
    return "amount";
  }
  if (data === "¿pagado?") {
    return "paid";
  }

  if (data === "fecha del pago") {
    return "date_paid";
  }

  if (data === "fecha de creación") {
    return "created_at";
  }

  return;
};
const destroy = (id, data) => {
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
          const url = route("charges.destroy", id);

          const attributeURL = getOriginal(data.attribute);
          router.delete(
            url,
            {
              data: {
                q: data.searchQuery,
                attribute: attributeURL,
                order: data.order,
              },
            },
            () => {
              onError: (error) => {
                toast.error("Ha Ocurrido un Error, Intentalo más Tarde");
              };
            }
          );
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

const formatDescription = (tipo) => {
  // Convertimos la fecha ISO a un objeto Date
  switch (tipo) {
    case "fuera-corte":
      return "No pagó antes del día de corte";
      break;
    case "recargo-mes":
      return "Recargo del mes";
      break;
    case "renta-dispositivo":
      return "Renta del dispositivo";
      break;
    case "instalacion-inicial":
      return "Instalación inicial";
      break;
    case "cambio-domicilio":
      return "Cambio de domicilio";
      break;
    default:
      return tipo;
  }
};
const formatDateWithDayAndTime = (dateString) => {
  // Reemplazar espacio por "T" para convertir al formato ISO 8601
  if(dateString !== ''){
   // alert(dateString);
    const validDateString = dateString.replace(" ", "T");
    const date = new Date(validDateString);
  
    return new Intl.DateTimeFormat("es-ES", {
      weekday: "long",
      day: "numeric",
      month: "long",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    }).format(date);

  }
};

// console.log(formatDateWithDayAndTime("2026-07-01 19:09:43"));
// Resultado: "miércoles, 1 de julio de 2026, 19:09"


// console.log(formatDateTime("2025-01-02 19:09:43"));
// Resultado: "jueves, 2 de enero de 2025, 19:09"


//console.log(formatDateWithDay("2026-07-01")); // miércoles, 1 de julio de 2026

const getTag = (cellIndex) => {
  console.log(cellIndex);
  switch (cellIndex) {
    case "description":
      return "Descripción";
      break;
    case "contract_id":
      return "Contrato de";
      break;
    case "amount":
      return "Monto";
      break;

    case "paid":
      return "¿Pagado?";
      break;

    case "date_paid":
      return "Fecha de Pago";
      break;

    case "created_at":
      return "Fecha de Creación";
      break;

    default:
      return cellIndex;
      break;
  }
};
</script>
<template>
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div
      class="flex flex-col sm:flex-row flex-wrap space-y-4 sm:space-y-0 items-center justify-between pb-4"
    >
      <!-- incio de filtros -->
      <div class="flex gap-2">
        <filter-order-base
          :list="[
            { id: 0, order: 'ASC' },
            { id: 1, order: 'DESC' },
          ]"
          name="order"
          @elementSelected="orderSelect"
        >
        </filter-order-base>
        <div v-click-outside="closeDropdown">
          <button
            id="dropdownRadioButton"
            @click="toggleDropdown"
            class="uppercase gap-2 inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-xs px-3 py-1.5"
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
            v-if="dropdownOpen"
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
                    class="w-full ms-2 text-xs font-medium text-gray-900 rounded uppercase cursor-pointer"
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
              order: currentOrder,
              attribute: currentFilter,
            })
          "
          class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Buscar"
        />
      </div>
    </div>

    <table class="w-full text-sm text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th></th>

          <th v-for="(header, index) in headers" :key="index" scope="col">
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
          <td></td>
          <td
            v-for="(cell, cellIndex) in row"
            :key="cellIndex"
            class="font-medium text-gray-900 whitespace-nowrap"
          >
        
            <div v-if="cellIndex === 'date_paid'" class="flex gap-1">
       
                <span class="lg:hidden md:hidden block font-bold lowercase">
                  {{ getTag(cellIndex) }} :
                </span>
                  {{ formatDateWithDayAndTime(cell) }}
        
            </div>
            <div v-else-if="cellIndex === 'paid'" class="flex gap-1">
                <span class="lg:hidden md:hidden block font-bold lowercase">
                  {{ getTag(cellIndex) }} :
                    
          
                </span>
                  {{ cell == 0 ? " No" : " Sí" }}
            </div>
            <div v-else-if="cellIndex === 'created_at'" class="flex gap-1">
                <span class="lg:hidden md:hidden block font-bold lowercase">
                  {{ getTag(cellIndex) }} :
                    
          
                </span>
                  {{ formatDateWithDayAndTime(cell) }}
            </div>
            <div v-else-if="cellIndex === 'description'">
              <span class="flex">
                <span class="lg:hidden md:hidden block font-bold lowercase">
                  {{ getTag(cellIndex) }} :
                    
    
                </span>
                <span>
                  {{ formatDescription(cell) }}

                </span>
              </span>
            </div>
            <div v-else>
              <div class="flex gap-1">
                <span class="lg:hidden md:hidden block font-bold lowercase"
                  >{{ getTag(cellIndex) }} :</span
                >
                {{ cell }}
              </div>
            </div>
          </td>
          <td class="flex items-stretch">
            <div class="sm:flex gap-4 flex actions">
              <Link
                :href="route('charges.show', row.id)"
                v-if="show"
                class="flex items-center gap-2 bg-slate-500 hover:bg-slate-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12"
                  />
                </svg>

                Mostrar
              </Link>
              <Link
                v-if="edit"
                :href="route('charges.edit', row.id)"
                class="flex items-center gap-2 bg-cyan-500 hover:bg-cyan-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"
                  />
                </svg>

                Editar
              </Link>

              <div v-if="del">
                <button
                  @click="
                    destroy(row.id, {
                      searchQuery: searchQuery,
                      attribute: currentFilter,
                      order: currentOrder,
                    })
                  "
                  class="flex items-center gap-2 bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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
      dropdownOpen: false,
      dropdownOpen2: false,
      currentFilter: "id",
      currentCharge: "todos",
      currentOrder: "ASC",
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
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
    },

    closeDropdown() {
      this.dropdownOpen = false;
    },

    toggleDropdown2() {
      this.dropdownOpen2 = !this.dropdownOpen2;
    },

    selectFilter(filter) {
      this.currentFilter = filter;
      this.toggleDropdown();
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        type: this.currentCharge,
        order: this.currentOrder,
      });
    },

    selectCharge(charge) {
      this.currentCharge = charge;
      this.toggleDropdown2();
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        type: this.currentCharge,
        order: this.currentOrder,
      });
    },

    orderSelect(newOrder) {
      this.currentOrder = newOrder;
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        type: this.currentCharge,
        order: this.currentOrder,
      });
    },

    filterData() {
      console.log(this.searchQuery);
    },
  },
};
</script>