<script setup>
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import ModalUsers from "../Components/ModalUsers.vue";

import FilterOrderBase from "@/Components/Base/FilterOrderBase.vue";
import BaseExportExcel from "@/Components/Base/Excel/BaseExportExcel.vue";
// ACCION DE ELIMINAR

const toRouteExport = "devices.all.excel";
//const urlComplete = "/devices/all/to/excel";
const getOriginal = (data) => {
  if (data === "id interno") {
    return "device_internal_id";
  }

  if (data === "dispositivo") {
    return "device_id";
  }

  if (data === "usuario") {
    return "user_id";
  }

  if (data === "comentario") {
    return "comment";
  }

  if (data === "ip") {
    return "address";
  }
};

const destroy = (id, data) => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "¿Estas seguro de Eliminar el Dispositivo?",
        accept: true,
        cancel: true,
        textConfirm: "Eliminar",
      },

      listeners: {
        accept: () => {
          const url = route("devices.all.destroy", id);

          const attributeUrl = getOriginal(data.attribute);
          router.delete(
            url,
            {
              preserveState: true,
              data: {
                q: data.searchQuery,
                attribute: attributeUrl,
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

const setDeviceStatus = (row) => {
  const url = route("devices.all.set.status", {
    device: row.id,
  });

  router.patch(url, () => {});
};

const isModalOpen = ref({});
const isModalDeviceOpen = ref({});

const openModal = (id) => {
  isModalOpen.value[id] = true;
};

const closeModal = (id) => {
  isModalOpen.value[id] = false;
};

const openDeviceModal = (id) => {
  isModalDeviceOpen.value[id] = true;
};

const closeDeviceModal = (id) => {
  isModalDeviceOpen.value[id] = false;
};

const confirmSelectionDevice = (row, select, data) => {
  if (select.selectId === null) {
    const toast = useToast();
    toast.error("Selecciona un dispositivo", {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  } else {
    const url = route("devices.all.update", row.id);
    const attributeUrl = getOriginal(data.attribute);
    let user_id = null;

    if (row.user_id) {
      user_id = row.user_id.id;
    }
    router.put(url, {
      address: row.address,
      router_id: row.router.id,
      comment: row.comment,
      user_id: user_id,
      device_id: select.selectId,
      ///////////////////////
      q: data.searchQuery,
      attribute: attributeUrl,
      order: data.order,
    });
    closeModal();
  }
};

const confirmSelectionUser = (row, select, data) => {
  if (select.selectId === null) {
    const toast = useToast();
    toast.error("Selecciona un usuario", {
      position: POSITION.TOP_CENTER,
      draggable: true,
    });
  } else {
    const url = route("devices.all.update", row.id);
    const attributeUrl = getOriginal(data.attribute);

    let device_id = null;

    if (row.device_id) {
      device_id = row.device_id.id;
    }

    router.put(url, {
      address: row.address,
      router_id: row.router.id,
      comment: row.comment,
      user_id: select.selectId,
      device_id: device_id,
      ///////////////////////
      q: data.searchQuery,
      attribute: attributeUrl,
      order: data.order,
    });
    closeModal();
  }
};

const getTag = (cellIndex) => {
  switch (cellIndex) {
    case "user":
      return "usuario";
    case "device_internal_id":
      return "id interno";

    case "comment":
      return "comentario";

    case "address":
      return "ip";
    case "device_id":
      return "Mac";

    case "user_id":
      return "usuario";
    default:
      return cellIndex;
  }
};
</script>
<template>
  <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <!-- <base-export-excel
      :toRouteExport="toRouteExport"
      :url-complete="urlComplete"
    ></base-export-excel> -->
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
            <div v-if="cellIndex === 'disabled'">
              <label class="inline-flex items-center cursor-pointer">
                <input
                  type="checkbox"
                  :checked="cell === 0"
                  class="sr-only peer"
                  @click="setDeviceStatus(row)"
                />
                <div
                  class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 peer-focus:ring-blue-300 rounded-full peer bg-gray-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-300 peer-checked:bg-green-300"
                ></div>
              </label>
            </div>

            <div v-else-if="cellIndex === 'device_id'">
              <div v-if="cell === null && inv_devices.length > 0">
                <button
                  @click="openDeviceModal(row.id)"
                  class="flex justify-center items-center gap-1 border border-gray-500 bg-white-500 hover:bg-slate-600 py-1 px-2 rounded-md text-slate-600 hover:text-white sm:mb-0 mb-1"
                >
                  Asignar Dispositivo
                </button>

                <modal-users
                  :show="isModalDeviceOpen[row.id]"
                  @close="closeDeviceModal(row.id)"
                  @selectData="
                    confirmSelectionDevice(row, $event, {
                      searchQuery: this.searchQuery,
                      attribute: this.currentFilter,
                      order: this.currentOrder,
                    })
                  "
                  :data="inv_devices"
                  :id="row.id"
                  :title="
                    'Selecciona un dispositivo del inventario para la conexión ' +
                    row.address
                  "
                  item-text="mac_address"
                >
                </modal-users>
              </div>
              <div v-else>
                <div v-if="cell !== null">
                  <div class="flex gap-1">
                    <span class="lg:hidden md:hidden block font-bold lowercase"
                      >{{ getTag(cellIndex) }} :</span
                    ><Link
                      :href="route('inventorie.devices.show', cell.id)"
                      class="cursor-pointer"
                    >
                      {{ cell.mac_address }}
                    </Link>
                  </div>
                </div>
                <div v-else>
                  <span class="bg-slate-500 py-1 px-2 rounded-md text-white"
                    >Sin dispositvos</span
                  >
                </div>
              </div>
            </div>

            <div v-else-if="cellIndex === 'user_id'">
              <div v-if="cell === null && users.length > 0">
                <button
                  @click="openModal(row.id)"
                  class="flex justify-center items-center gap-1 border border-teal-500 text-teal-500 hover:bg-teal-500 hover:bg-teal-600 py-1 px-2 rounded-md hover:text-white sm:mb-0 mb-1"
                >
                  Asignar usuario
                </button>

                <modal-users
                  :show="isModalOpen[row.id]"
                  @close="closeModal(row.id)"
                  @selectData="
                    confirmSelectionUser(row, $event, {
                      searchQuery: this.searchQuery,
                      attribute: this.currentFilter,
                      order: this.currentOrder,
                    })
                  "
                  :data="users"
                  :id="row.id"
                  :title="
                    'Selecciona un usuario para la conexión ' + row.address
                  "
                  item-text="name"
                >
                </modal-users>
              </div>

              <div v-else>
                <div v-if="cell !== null">
                  <div class="flex gap-1">
                    <span class="lg:hidden md:hidden block font-bold lowercase"
                      >{{ getTag(cellIndex) }} :</span
                    >
                    <Link
                      :href="route('usuarios.show', cell.id)"
                      class="cursor-pointer"
                    >
                      {{ cell.name }}
                    </Link>
                  </div>
                </div>
                <div v-else>
                  <span class="bg-slate-500 py-1 px-2 rounded-md text-white"
                    >Sin Usuarios</span
                  >
                </div>
              </div>
            </div>
            <div v-else-if="cellIndex === 'router'">
              {{ cell.ip_address }}
            </div>
            <!-- <div v-if="cellIndex === 'sync'">
              <Link
              v-if="edit"
              :href="route('routers.sync', row.id)"
              class="flex gap-1 p-1 rounded-full text-white sm:mb-0 mb-1 w-8 items-center justify-center"
              :class="
              row.sync
                    ? 'bg-green-500 hover:bg-green-600'
                    : ' bg-orange-500 hover:bg-orange-600'
                "
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
                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"
                />
                </svg>
              </Link>
            </div> -->
            <div v-else>
              <!-- v-if="cellIndex !== 'router_id'" -->
              <div>
                <div class="flex gap-1">
                  <span class="lg:hidden md:hidden block font-bold lowercase"
                    >{{ getTag(cellIndex) }} :</span
                  >
                  {{ cell }}
                </div>
              </div>
            </div>
          </td>

          <td class="flex items-stretch">
            <div class="sm:flex gap-4 flex flex-wrap actions">
              <Link
                :href="route('performance.device', row.id)"
                v-if="show"
                class="flex items-center gap-2 bg-green-500 hover:bg-green-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
              >
              <span class="material-symbols-outlined">
              signal_cellular_alt
              </span>

                Consumo
              </Link>
              <Link
                :href="route('routers.devices', row.id)"
                class="flex items-center gap-1 bg-slate-500 hover:bg-slate-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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
                Detalles
              </Link>

              <Link
                :href="route('devices.one.ping', row.id)"
                class="flex items-center gap-1 bg-emerald-500 hover:bg-emerald-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
              >
                <span class="material-symbols-outlined"> network_ping </span>
                Ping
              </Link>
              <Link
                v-if="edit"
                :href="
                  route('devices.all.edit', {
                    router: row.router.id,
                    device: row.id,
                  })
                "
                class="flex items-center gap-1 bg-cyan-500 hover:bg-cyan-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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
                      searchQuery: this.searchQuery,
                      attribute: this.currentFilter,
                      order: this.currentOrder,
                    })
                  "
                  class="flex items-center gap-1 bg-red-500 hover:bg-red-600 py-1 px-2 rounded-md text-white sm:mb-0 mb-1"
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

    users: {
      type: Object,
      required: true,
    },

    inv_devices: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      searchQuery: "",
      dropdownOpen: false,
      dropdownOpen2: false,
      currentFilter: "id",
      currentUser: "todos",
      currentOrder: "ASC",
      typeUsers: ["todos", "cliente", "coordinador", "tecnico"],
      inv_devices_ref: this.inv_devices,
    };
  },

  watch: {
    inv_devices() {
      this.inv_devices_ref = this.inv_devices;
    },
  },
  computed: {
    filteredRows() {
      return this.rows;
    },
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
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
        order: this.currentOrder,
      });
    },

    selectUser(user) {
      this.currentUser = user;
      this.toggleDropdown2();
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        order: this.currentOrder,
      });
    },

    orderSelect(newOrder) {
      this.currentOrder = newOrder;
      this.$emit("search", {
        searchQuery: this.searchQuery,
        attribute: this.currentFilter,
        order: this.currentOrder,
      });
    },

    // filterData() {
    //   console.log(this.searchQuery);
    // },
  },
};
</script>``