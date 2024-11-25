<script setup>
import { router } from "@inertiajs/vue3";

import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "./BaseQuestion.vue";

import FilterOrderBase from "./FilterOrderBase.vue";

const getOriginal = (data) => {
  if (data === "router") {
    return "router_id";
  }

  if (data === "contenido") {
    return "content";
  }

  if (data === "fecha") {
    return "created_at";
  }
};
// ACCION DE ELIMINAR
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
          const url = route("pings.destroy", id);

          console.log(data)
          const attributeUrl = getOriginal(data.attribute);
          router.delete(url, {
            preserveState: true,
            data: {
              q: data.searchQuery,
              attribute: attributeUrl,
              order: data.order,
            },
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

const getTag = (cellIndex) => {
  switch (cellIndex) {
    case "router_id":
      return "router";
    case "content":
      return "contenido";

    case "created_at":
      return "fecha";
    default:
      return cellIndex;
  }
};
</script>
<style>
.boton-modal label {
  cursor: pointer;
  transition: all 300ms ease;
}
.overlay {
  position: absolute;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  opacity: 0;
  pointer-events: none;
}
.container-modal {
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
.content-modal Link {
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
        <filter-order-base
          :list="[
            { id: 0, order: 'ASC' },
            { id: 1, order: 'DESC' },
          ]"
          name="order"
          @elementSelected="orderSelect"
        >
        </filter-order-base>

        <div>
          <button
            id="dropdownRadioButton"
            @click="toggleDropdown1"
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
              order: currentOder,
              type: currentFilter,
            })
          "
          class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
          placeholder="Buscar"
        />
      </div>
    </div>

    <table class="w-full text-sm text-left text-gray-500 p-2">
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
          <!-- INICIO DEWL  -->
          <td></td>
          <!-- SELECTOR DE ESTADOS DE TICKET  -->
          <td
            v-for="(cell, cellIndex) in row"
            :key="cellIndex"
            class="font-medium text-gray-900 whitespace-nowrap"
          >
            <div class="gap-2 w-full">
              <div>
                <div class="flex gap-1">
                  <span class="lg:hidden md:hidden block font-bold lowercase"
                    >{{ getTag(cellIndex) }} :</span
                  >
                  {{ cell }}
                </div>
              </div>

              <!-- Final dfrop nbuton  -->
            </div>
          </td>
          <!-- SELECTOR DE ESTADOS DE TICKET  -->

          <td class="flex items-stretch">
            <div class="sm:flex gap-4 flex actions">
              <div v-if="del">
                <button
                  @click="
                    destroy(row.id, {
                      searchQuery: searchQuery,
                      order: currentOrder,
                      attribute: currentFilter,
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
      dropdownOpen1: false,
      dropdownOpen: {},
      currentFilter: "id",
      currentPing: "todos",
      currentOrder: "ASC",
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
        attribute: this.currentFilter,
        type: this.currentPing,
        order: this.currentOrder,
      });
    },

    selectPing(ping, index) {
      this.currentPing = ping;
      this.toggleDropdown(index);
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        type: this.currentPing,
        order: this.currentOrder,
      });
    },

    orderSelect(newOrder) {
      this.currentOrder = newOrder;
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        type: this.currentPing,
        order: this.currentOrder,
      });
    },

    filterData() {
      console.log(this.searchQuery);
    },
  },
};
</script>