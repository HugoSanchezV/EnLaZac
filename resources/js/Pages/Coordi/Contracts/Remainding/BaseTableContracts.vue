<script setup>
import FilterOrderBase from "@/Components/Base/FilterOrderBase.vue";

const getTag = (cellIndex) => {
  switch (cellIndex) {
    case "user_id":
      return "Usuario";
      break;
    case "plan_id":
      return "Plan";
      break;
    case "rural_community_id":
      return "Comunidad";
      break;
    case "start_date":
      return "Inicio";
      break;
    case "end_date":
      return "Fin";
      break;

    case "address":
      return "Dirección";
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

      <div class="flex gap-2 items-center mb-4">
        <label for="daysRemaining" class="text-sm text-gray-500"
          >Días restantes:</label
        >
        <input
          type="number"
          id="daysRemaining"
          v-model="daysFilter"
          @input="filterContracts"
          min="1"
          placeholder="Ej. 5"
          class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-20 bg-gray-50 focus:ring-blue-500 focus:border-blue-500"
        />
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
              attribute: currentFilter,
              order: currentOrder,
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
            <div v-if="cellIndex === 'active'">
              <div v-if="cell === 0">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="red"
                  class="size-8"
                >
                  <path
                    fill-rule="evenodd"
                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm6-2.438c0-.724.588-1.312 1.313-1.312h4.874c.725 0 1.313.588 1.313 1.313v4.874c0 .725-.588 1.313-1.313 1.313H9.564a1.312 1.312 0 0 1-1.313-1.313V9.564Z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div v-else-if="cell == 1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="#00CF00"
                  class="size-8"
                >
                  <path
                    fill-rule="evenodd"
                    d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm14.024-.983a1.125 1.125 0 0 1 0 1.966l-5.603 3.113A1.125 1.125 0 0 1 9 15.113V8.887c0-.857.921-1.4 1.671-.983l5.603 3.113Z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
            <div v-else-if="cellIndex === 'end_date'">
              <span
                :class="{
                  'text-green-500': daysRemaining(row.end_date) > 5,
                  'text-yellow-500':
                    daysRemaining(row.end_date) <= 5 &&
                    daysRemaining(row.end_date) > 1,
                  'text-red-500': daysRemaining(row.end_date) === 1,
                }"
              >
                {{ daysRemaining(row.end_date) }} días restantes
              </span>
              con fecha {{ cell }}
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
              <div>
                <button
                  @click="
                    openModal(row.id, {
                      searchQuery: this.searchQuery,
                      attribute: this.currentFilter,
                      order: this.currentOrder,
                    })
                  "
                  class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
                >
                  <span class="material-symbols-outlined">
                    settings_backup_restore
                  </span>
                  Extender
                </button>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div>
      <div v-if="showExtendModal">
        <div
          class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center"
        >
          <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">
              <span class="bg-gray-400 text-white px-2 py-1 rounded-md"
                >ID: {{ selectedContractId }}</span
              >
              Extender contrato
            </h2>

            <label for="extendDays" class="block text-sm mb-2"
              >Días a extender:</label
            >
            <input
              type="number"
              id="extendDays"
              v-model="extendDays"
              min="1"
              max="30"
              class="block w-full p-2 border border-gray-300 rounded"
            />
            <div class="mt-4 flex justify-end gap-2">
              <button
                @click="closeModal"
                class="px-4 py-2 bg-white border rounded-md hover:bg-gray-100"
              >
                Cancelar
              </button>
              <button
                @click="submitExtension"
                class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
              >
                Confirmar
              </button>
            </div>
          </div>
        </div>
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
      dropdownOpen: false,
      dropdownOpen2: false,
      currentFilter: "id",
      currentContract: "todos",
      currentOrder: "ASC",
      daysFilter: 5,
      extendDays: 5,
      showExtendModal: false,
      selectedContractId: null,
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
        day: this.daysFilter,
        order: this.currentOrder,
      });
    },

    selectContract(contract) {
      this.currentContract = contract;
      this.toggleDropdown2();
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        day: this.daysFilter,
        order: this.currentOrder,
      });
    },

    orderSelect(newOrder) {
      this.currentOrder = newOrder;
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        day: this.daysFilter,
        order: this.currentOrder,
      });
    },

    filterData() {
      console.log(this.searchQuery);
    },
    filterContracts() {
      // const url = route("reaming.contracts", {
      //   days: this.daysFilter,
      //   q: this.q,
      //   attribute: this.attribute,
      //   order: this.order,
      // });

      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        day: this.daysFilter,
        order: this.currentOrder,
      });
      // console.log(url);
      // router.visit(url, { method: "get", preserveState: true });
    },
    openModal(contractId, data) {
      this.selectedContractId = contractId;
      this.showExtendModal = true;
    },
    closeModal() {
      this.showExtendModal = false;
      this.extendDays = 0;
      this.selectedContractId = null;
    },

    submitExtension() {
      const url = route("reaming.contracts.extend", {
        id: this.selectedContractId,
      });

      const attributeUrl = this.getOriginal(this.currentFilter);
      router.post(
        url,
        {
          days: this.extendDays,
          daysFilter: this.daysFilter,
          q: this.searchQuery,
          attribute: attributeUrl,
          order: this.currentOrder,
        },
        {
          onSuccess: () => {
            this.$toast.success("Contrato extendido correctamente");
            this.closeModal();
          },
          onError: () => {
            this.$toast.error("Hubo un error al extender el contrato");
          },
        }
      );
      this.closeModal();
    },

    getOriginal(data) {
      if (data.attribute === "id") {
        return "id";
      }

      if (data.attribute === "usuario") {
        return "user_id";
      }

      if (data.attribute === "plan internet") {
        return "plan_id";
      }

      if (data.attribute === "fecha de inicio") {
        return "start_date";
      }

      if (data.attribute === "fecha de terminación") {
        return "end_date";
      }

      if (data.attribute === "¿activo?") {
        return "active";
      }

      if (data.attribute === "dirección") {
        return "address";
      }
      if (data.attribute === "comunidad") {
        return "community";
      }
    },
    daysRemaining(endDate) {
      return Math.ceil(
        (new Date(endDate) - new Date()) / (1000 * 60 * 60 * 24)
      );
    },
  },
};
</script>