<script setup>
import { defineProps } from "vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
import { ref } from "vue";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import { router } from "@inertiajs/vue3";

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

// Estado para mostrar u ocultar el token
const isTokenVisible = ref(false);

// Método para alternar la visibilidad del token
function toggleTokenVisibility() {
  isTokenVisible.value = !isTokenVisible.value;
}

function comprobarEstado(token) {
  const url = route("local.pay.check");

  const reponser =  router.get(url, token);
}

const formattedDate = (date) => {
  const dateObj = new Date(date);
  const options = { year: "numeric", month: "long", day: "numeric" };
  return dateObj.toLocaleDateString("es-ES", options);
};

const destroy = (orderId) => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "¿Estas seguro de Eliminar la orden?",
        accept: true,
        cancel: true,
        textConfirm: "Eliminar",
      },

      listeners: {
        accept: () => {
          const url = route("local.pay.delete", orderId);

          router.delete(url);
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
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <h2>Detalles de orden</h2>
      </div>
    </template>
    <template v-slot:content>
      <div class="flex justify-center items-center w-full h-full" v-if="cart">
        <div class="p-8 rounded-lg md:w-1/2 w-full">
          <!-- Token y Botón de Comprobar Estado -->
          <div class="text-gray-600 mb-4">
            Para poder realizar tu pago de manera local es necesario que des la
            clave de order al encargado de hacer cobrarte, para mostrar la clave
            simplemente hay presionar el boton con icono de ojo
          </div>
          <div class="mb-4">
            <span class="font-semibold">Total :</span> ${{ amount }}
          </div>
          <div class="flex justify-between items-center mb-4">
            <div class="flex items-center mb-4 gap-2">
              <button
                @click="toggleTokenVisibility"
                class="text-gray-500 hover:text-gray-800"
              >
                <span class="material-symbols-outlined">
                  {{ isTokenVisible ? "visibility" : "visibility_off" }}
                </span>
              </button>
              <span class="p-2 border rounded mr-2">
                {{ isTokenVisible ? token : "********" }}
              </span>
            </div>
            <div>
              <button
                @click="comprobarEstado"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
              >
                Comprobar Estado
              </button>
            </div>
          </div>

          <!-- Lista de ítems del carrito -->
          <div class="space-y-6">
            <div
              v-for="item in cart"
              :key="item.id"
              class="p-6 bg-white rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition duration-300"
            >
              <!-- Header de la tarjeta con la fecha y el tipo -->
              <div class="flex justify-between items-center mb-4">
                <div class="text-sm text-gray-500">
                  {{ formattedDate(date) }}
                </div>
                <div
                  class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-1 rounded"
                >
                  {{ item.type === "charge" ? "Cargo" : "Contrato" }}
                </div>
              </div>

              <!-- Título de la tarjeta -->
              <h3 class="text-lg font-bold text-gray-900 mb-2">
                {{ item.description }}
              </h3>

              <!-- Descripción del pago -->
              <p class="text-gray-700 text-sm mb-4">
                Monto a pagar: <strong>${{ item.amount }}</strong>
              </p>

              <!-- Información del contrato y acciones -->
              <div class="flex justify-between items-center">
                <div class="text-gray-500 text-sm">
                  Contrato ID: <strong>{{ item.contract }}</strong>
                </div>
                <div class="flex items-center mt-4">
                  <p class="text-sm font-medium text-gray-800">
                    {{ user["name"] }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end mt-4">
            <button
              @click="destroy(orderId)"
              class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 transition flex justify-center gap-2"
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

              Eliminar orden
            </button>
          </div>
        </div>
      </div>
      <div v-else>No hay datos para mostrar</div>
    </template>
  </dashboard-base>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined");
.material-symbols-outlined {
  font-size: 24px;
  cursor: pointer;
}
</style>
