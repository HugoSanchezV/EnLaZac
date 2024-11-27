<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import GetData from "./GetData.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  contracts: {
    type: Array,
  },
  paypal: {
    type: Array,
    default: null,
  },
  mercadopago: {
    type: Number,
    default: 0,
  },
});

const cart = ref([]);
const totalAmount = ref(0);
const showPayment = ref(false);
const selectedMonthsPerContract = ref({});

const formatCurrency = (value) => {
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
  }).format(value);
};

const formatDescription = (tipo) => {
  switch (tipo) {
    case "fuera-corte":
      return "No pagó antes del día de corte";
    case "recargo-mes":
      return "Recargo del mes";
    case "renta-dispositivo":
      return "Renta del dispositivo";
    case "instalacion-inicial":
      return "Instalación inicial";
    case "cambio-domicilio":
      return "Cambio de domicilio";
    default:
      return tipo;
  }
};

const calculateTotal = () => {
  totalAmount.value = cart.value.reduce((acc, item) => acc + item.amount, 0);
  showPayment.value = totalAmount.value > 0;
};

const isInCart = (itemId, itemType) => {
  return cart.value.some(
    (item) => item.id === itemId && item.type === itemType
  );
};

const addContractToCart = (contract) => {
  const months = selectedMonthsPerContract.value[contract.id] || 0;

  if (months <= 0) {
    alert("Por favor selecciona la cantidad de meses a pagar.");
    return;
  }

  if (isInCart(contract.id, "contract")) {
    alert("Este contrato ya ha sido agregado.");
    return;
  }

  const amount = contract.plan.price * months;
  cart.value.push({
    id: contract.id,
    type: "contract",
    description: `Contrato #${contract.id} (${months} mes(es))`,
    amount,
  });

  // Agregar cargos automáticamente
  contract.charges.forEach((charge) => {
    if (!isInCart(charge.id, "charge")) {
      cart.value.push({
        id: charge.id,
        type: "charge",
        description: formatDescription(charge.description),
        amount: charge.amount,
      });
    }
  });

  calculateTotal();
};

const removeFromCart = (index) => {
  cart.value.splice(index, 1);
  calculateTotal();
};
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:content>
      <div class="m-5 grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Tabla de contratos -->
        <div class="bg-white shadow rounded-lg p-5 col-span-2">
          <h3 class="text-lg font-bold mb-3">Contratos del Usuario</h3>
          <table class="table-auto w-full border-collapse border border-gray-300 text-sm">
            <thead>
              <tr>
                <th class="border border-gray-300 p-2">ID</th>
                <th class="border border-gray-300 p-2">Fecha de Fin</th>
                <th class="border border-gray-300 p-2">Precio</th>
                <th class="border border-gray-300 p-2">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="contract in contracts" :key="contract.id">
                <td class="border border-gray-300 p-2">{{ contract.id }}</td>
                <td class="border border-gray-300 p-2">{{ contract.end_date }}</td>
                <td class="border border-gray-300 p-2">{{ formatCurrency(contract.plan.price) }}</td>
                <td class="border border-gray-300 p-2 flex items-center gap-2">
                  <select
                    v-model="selectedMonthsPerContract[contract.id]"
                    class="border border-gray-400 p-1 rounded"
                  >
                    <option value="0">--Seleccionar meses--</option>
                    <option v-for="n in 12" :key="n" :value="n">
                      {{ n }} mes(es)
                    </option>
                  </select>
                  <button
                    class="bg-blue-500 text-white px-3 py-1 rounded disabled:opacity-50"
                    :disabled="isInCart(contract.id, 'contract')"
                    @click="addContractToCart(contract)"
                  >
                    {{
                      isInCart(contract.id, 'contract')
                        ? "Ya agregado"
                        : "Agregar al carrito"
                    }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Carrito -->
        <div class="bg-white shadow rounded-lg p-5">
          <h3 class="text-lg font-bold mb-3">Carrito</h3>
          <table class="table-auto w-full border-collapse border border-gray-300 text-sm">
            <thead>
              <tr>
                <th class="border border-gray-300 p-2">Concepto</th>
                <th class="border border-gray-300 p-2">Monto</th>
                <th class="border border-gray-300 p-2">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in cart" :key="index">
                <td class="border border-gray-300 p-2">{{ item.description }}</td>
                <td class="border border-gray-300 p-2">{{ formatCurrency(item.amount) }}</td>
                <td class="border border-gray-300 p-2">
                  <button
                    class="bg-red-500 text-white px-3 py-1 rounded"
                    @click="removeFromCart(index)"
                  >
                    Eliminar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="text-right mt-5">
            <h4 class="text-lg font-bold">
              Total a pagar: {{ formatCurrency(totalAmount) }}
            </h4>
          </div>
        </div>

        <!-- Sección de Pago -->
        <div class="bg-gray-100 shadow rounded-lg p-5">
          <h3 class="text-lg font-bold mb-3">Métodos de Pago</h3>
          <button
            class="bg-green-500 text-white px-3 py-2 rounded w-full"
            @click="() => (showPayment = true)"
          >
            Realizar Pago
          </button>
          <div v-if="showPayment" class="mt-5">
            <GetData :totalAmount="totalAmount" :cart="cart" :paypal="paypal" :mercadopago="mercadopago" />
          </div>
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

<style scoped>
/* Contenedor principal */
.card {
  background-color: #ffffff;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  padding: 32px;
  max-width: 700px;
  margin: 50px auto;
  transition: box-shadow 0.3s ease;
}

.card:hover {
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

/* Encabezados */
h2 {
  font-size: 1.8rem;
  color: #2c3e50;
  font-weight: 700;
  margin-bottom: 20px;
  text-align: center;
}

h3 {
  font-size: 1.4rem;
  color: #4a4a4a;
  font-weight: 600;
  margin-bottom: 15px;
}

h4 {
  font-size: 1.2rem;
  color: #333;
  font-weight: 500;
  margin-top: 20px;
  text-align: center;
}

/* Labels y Selectores */
.select-months label {
  font-weight: 600;
  color: #2c3e50;
  margin-right: 12px;
}

.select-months select {
  padding: 10px;
  border-radius: 8px;
  border: 1px solid #d1d1d1;
  background-color: #f9f9f9;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.select-months select:hover {
  border-color: #2c3e50;
}

/* Tablas */
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 15px;
  font-size: 0.95rem;
  border-radius: 8px;
  overflow: hidden;
}

th,
td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

th {
  background-color: #2c3e50;
  color: white;
  font-weight: 600;
}

tbody tr:hover {
  background-color: #f7f7f7;
}

/* Botones */
button {
  padding: 10px 18px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 1rem;
  transition: background-color 0.3s ease, box-shadow 0.3s ease;
  color: white;
}

button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

/* Botón de acción */
.client-charges button,
.cart button {
  background-color: #3498db;
}

.client-charges button:hover,
.cart button:hover {
  background-color: #2980b9;
  box-shadow: 0 4px 12px rgba(41, 128, 185, 0.3);
}

/* Botón de eliminación */
.cart button {
  background-color: #e74c3c;
}

.cart button:hover {
  background-color: #c0392b;
}

/* Total a pagar */
.total-label {
  font-size: 1.3rem;
  font-weight: bold;
  color: #27ae60;
  text-align: center;
  margin-top: 20px;
  padding: 10px;
  background-color: #ecf9f1;
  border-radius: 8px;
}

/* Mensaje de error */
.error-message {
  color: #e74c3c;
  font-weight: bold;
  margin-top: 10px;
  text-align: center;
  padding: 8px;
  background-color: #fdecea;
  border-radius: 8px;
}

/* Botón de pago */
button.process-payment {
  background-color: #27ae60;
  width: 100%;
  padding: 14px;
  font-size: 1.1rem;
  border-radius: 8px;
  margin-top: 25px;
  box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
}

button.process-payment:hover {
  background-color: #219150;
}
</style>