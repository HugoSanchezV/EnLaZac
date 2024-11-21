<script setup>
import { defineProps } from "vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
import ShowLocaPayAdmin from "./ShowLocaPayAdmin.vue";
import { router } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  cart: {
    type: Array,
    default: () => [],
  },
  amount: {
    type: Number,
    default: 0.0,
  },
  token: {
    type: String,
    default: "",
  },

  user: {
    type: Array,
    default: [],
  },

  date: {
    type: String,
    default: "",
  },

  orderId: {
    type: Number,
    default: null,
  },
});
const searchId = ref("");

function search() {
  const url = route("local.pay.search");
  router.get(url, { token: searchId.value });
}
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Cobro local</h2>
        </div>
      </div>
    </template>

    <template v-slot:content>
      <div class="flex justify-center">
        <!-- Contenedor para el input y el botón -->
        <div
          class="px-3 py-5 rounded-md flex items-center justify-center gap-4 transition-all ease-out"
        >
          <!-- Input para buscar el ID -->
          <input
            v-model="searchId"
            type="text"
            placeholder="Ingresa el Token a buscar"
            class="p-2 border border-gray-300 rounded h-10 w-64"
          />
          <!-- Botón para ejecutar la búsqueda -->
          <button
            @click="search"
            class="bg-blue-500 h-10 px-4 text-white rounded hover:bg-blue-600 transition text-sm"
          >
            Buscar 
          </button>
        </div>
      </div>
      <div v-if="cart.length > 0">
        <ShowLocaPayAdmin
          :cart="cart"
          :amount="amount"
          :date="date"
          :order-id="order_id"
          :token="token"
          :user="user"
        >
        </ShowLocaPayAdmin>
      </div>
      <div class="flex justify-center mt-5">
        <span>Busca un token válido</span>
      </div>
    </template>
  </dashboard-base>
</template>


