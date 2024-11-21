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

// Método para confirmar el pago
function confirmPay(token, cart, amount) {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "¿Estas seguro confirmar el orden?",
        accept: true,
        cancel: true,
        textConfirm: "Confirmar",
      },
      listeners: {
        accept: () => {
          const url = route("local.pay.confirm", { token: token });
          router.post(url, {
            token: token,
            cart: cart,
            amount: amount,
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
}

const formattedDate = (date) => {
  const dateObj = new Date(date);
  const options = { year: "numeric", month: "long", day: "numeric" };
  return dateObj.toLocaleDateString("es-ES", options);
};
</script>

<template>
  <div class="flex justify-center items-center w-full h-full" v-if="cart">
    <div class="p-8 rounded-lg md:w-1/2 w-full">
      <!-- Token y Botón de Comprobar Estado -->
      <div class="text-gray-600 mb-4 text-center">
        Selecciona la opción de cobro para limpiar los pagos del cliente
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
            @click="confirmPay(token, cart, amount)"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
          >
            Confirmar Pago
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
    </div>
  </div>
  <div v-else>No hay datos para mostrar</div>
</template>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined");
.material-symbols-outlined {
  font-size: 24px;
  cursor: pointer;
}
</style>
