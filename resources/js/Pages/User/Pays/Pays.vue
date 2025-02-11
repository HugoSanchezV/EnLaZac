<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import GetData from "./GetData.vue";
import { Head } from "@inertiajs/vue3";
import { onMounted, ref } from "vue";

const props = defineProps({
  contracts: {
    type: Array,
  },
  paypal: {
    type: Object,
    default: null,
  },
  mercadopago: {
    type: Number,
    default: 0,
  },
  rent: {
    type: Number,
  },
});

const cart = ref([]);
const totalAmount = ref(0);
const showPayment = ref(false);
const selectedMonthsPerContract = ref({});
const selectedMonthsPerRent = ref({});
// Lista de cargos individuales
const availableCharges = ref([]);
const unabledContracts = ref(true);

// Al inicializar el componente, extraemos los cargos individuales
onMounted(() => {
  props.contracts.forEach((contract) => {
    contract.charges.forEach((charge) => {
      if (
        ["instalacion-inicial", "cambio-domicilio"].includes(charge.description)
      ) {
        availableCharges.value.push({
          id: charge.id,
          type: "individual-charge",
          contractId: contract.id,
          description: charge.description,
          amount: charge.amount,
        });
        if("instalacion-inicial".includes(charge.description))
        {
          unabledContracts.value = false;
        }
      }
    });
  });
});

const formatCurrency = (value) => {
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
  }).format(value);
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

const calculateCartTotal = (cartItems) =>
  (cartItems || [])
    .filter((item) => typeof item.amount === "number") // Filtra valores no válidos
    .reduce((acc, item) => acc + item.amount, 0);

const calculateTotal = () => {
  totalAmount.value = calculateCartTotal(cart.value);
  showPayment.value = totalAmount.value > 0;

  console.log("total");
  console.log("Total a pagar: ", totalAmount.value);
  console.log("================================");

  console.log("Carrito");
  console.log(cart.value);
  console.log("================================");
};

const isInCart = (itemId, itemType) => {
  let valor = cart.value.some(
    (item) => {
      if(item.id === itemId)
      {
        if((itemType === "contract" )||(itemType === "rent" )){
          if((item.type === "contract" )||(item.type === "rent" )){
            return true;
          }else{
            return false;
          }

        }else if((itemType  === 'individual-charge') && (item.type === 'individual-charge')) {
          return true;
        }
      }else{
        return false;
      }
     // item.id === itemId && (((itemType === "contract" )||(itemType === "rent" )))
    }
  );
  //alert(valor);
  return valor;
};
const isInCartIndivialCharge = (itemId) => {
  let valor = cart.value.some(
    (item) => 
    item.id === itemId && item.type == "individual-charge"
  );
  //alert(valor);
  return valor;
};

const alerta = (message) => {
  const toast = useToast();
  toast(
    {
      component: BaseQuestion,
      props: {
        message: message,
        textConfirm: "",
      },
    },
    {
      type: TYPE.ERROR,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
};

const addChargeToCart = (charge) => {
  console.log("Agregaste un cargo al carrito");
  console.log(charge);
  charge.amount = parseFloat(charge.amount);
  console.log(typeof charge.amount);
  console.log("================================");
  if (isInCart(charge.id, "individual-charge")) {
    alerta("Este cargo ya ha sido agregado al carrito.");
    return;
  }

  cart.value.push({
    id: charge.id,
    type: "individual-charge",
    description: charge.description,
    amount: charge.amount,
  });

  calculateTotal();
};


const addContractToRent = (contract) => {
  const months = selectedMonthsPerRent.value[contract.id] || 0;

  if (months <= 0) {
    alerta("Por favor selecciona la cantidad de meses a pagar.");
    return;
  }

  if (isInCart(contract.id, "rent")) {
    ///----------------------------
    alerta("Este contrato ya ha sido agregado.");
    return;
  }

  const amount = props.rent;
  cart.value.push({
    id: contract.id,
    type: "rent",
    description: `Renta del contrato #${contract.id} por ${months} mes(es))`,
    months: months,
    amount,
  });

  // Agregar cargos no individuales relacionados con el contrato
  // contract.charges.forEach((charge) => {
  //   if (!isInCart(charge.id, "charge") && !["instalacion-inicial", "cambio-domicilio"].includes(charge.description)) {
  //     cart.value.push({
  //       id: charge.id,
  //       type: "charge",
  //       contractId: contract.id,
  //       description: (charge.description),
  //       amount: charge.amount,
  //     });
  //   }
  // });

  calculateTotal();
};

const addContractToCart = (contract) => {
  const months = selectedMonthsPerContract.value[contract.id] || 0;

  if (months <= 0) {
    alerta("Por favor selecciona la cantidad de meses a pagar.");
    return;
  }

  if (isInCart(contract.id, "contract")) {
    alerta("Este contrato ya ha sido agregado.");
    return;
  }

  const amount = contract.plan.price * months;
  cart.value.push({
    id: contract.id,
    type: "contract",
    months: months,
    description: `Contrato #${contract.id} por ${months} mes(es)`,
    amount,
  });

  // Agregar cargos no individuales relacionados con el contrato
  contract.charges.forEach((charge) => {
    if (
      !isInCart(charge.id, "charge") &&
      !["instalacion-inicial", "cambio-domicilio"].includes(charge.description)
    ) {
      cart.value.push({
        id: charge.id,
        type: "charge",
        contractId: contract.id,
        description: charge.description,
        amount: charge.amount,
      });
    }
  });

  calculateTotal();
};

const setButton = (type) => {
  return type == "charge" ? false : true;
};

const removeFromCart = (contractId, type) => {
  // Eliminar contrato y sus cargos asociados
  if (type == "contract") {
    cart.value = cart.value.filter(
      (item) => !(item.id === contractId && item.type === "contract") && item.contractId !== contractId && (item.type === 'individual-charge')
    );
  }else if(type == 'rent'){
    cart.value = cart.value.filter(
      (item) => !(item.id === contractId && item.type === "rent") && item.contractId !== contractId && (item.type === 'individual-charge')
    );
  }else if(type == 'individual-charge'){
    cart.value = cart.value.filter(
      (item) => !(item.id === contractId && item.type === 'individual-charge')
    );
  }else{
    cart.value = cart.value.filter((item) => item.id !== contractId );
  
  }
  calculateTotal();
};

const formatDateWithDay = (dateString) => {
  // Agregar la hora para evitar problemas de huso horario
  const date = new Date(`${dateString}T00:00:00`);
  return new Intl.DateTimeFormat("es-ES", {
    weekday: "long",
    day: "numeric",
    month: "long",
    year: "numeric",
  }).format(date);
};

</script>


<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:content>
      <div class="m-10 gap-5  ">
        <!-- Tabla de contratos -->
        <div v-if="unabledContracts" class="bg-white shadow rounded-lg p-5 col-span-2">
          <h3 class="text-lg font-bold mb-3">Contratos del Usuario</h3>
          <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-700 uppercase bg-gray-950">
              <tr class="bg-white border-b hover:bg-gray-100">
                <th class="border border-gray-300 p-2">ID</th>
                <th class="border border-gray-300 p-2">Fecha de Fin</th>
                <th class="border border-gray-300 p-2">Precio</th>
                <th class="border border-gray-300 p-2">Servicio</th>
                <th class="border border-gray-300 p-2">Renta del dispositivo</th>
            
              </tr>
            </thead>
            <tbody>
              <tr v-for="contract in contracts" :key="contract.id">
                <td class="border border-gray-300 items-center p-2 ">
                  {{ contract.id }}
                </td>
                <td class="border border-gray-300 items-center p-2">
                  <div class="flex gap-1">
                    <span class="lg:hidden md:hidden block">Fin del contrato: </span>
                    {{ formatDateWithDay(contract.end_date) }}
                    
                  </div>
              
                </td>
                <td class="border border-gray-300 items-center p-2">
                  <div class="flex  gap-1">
                    <span class="lg:hidden md:hidden block">Precio: </span>
                    {{ formatCurrency(contract.plan.price) }}
                    
                  </div>
                </td>
                
                <td class="border border-gray-300 p-2 items-center gap-2">
                  <span class="lg:hidden md:hidden block">Servicio</span>

                  <div class="flex flex-col gap-2 items-center">
                    <select
                      v-model="selectedMonthsPerContract[contract.id]"
                      class="border border-gray-400 p-1 rounded"
                    >
                      <option :value=null disabled selected>Selecciona una opción</option>
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
                          ? "Agregado"
                          : "Agregar"
                      }}
                    </button>
                  </div>
                  
                </td>
                <td class="border border-gray-300 p-2 items-center gap-2">
                  <span class="lg:hidden md:hidden block">Renta del dispositivo</span>
                  <div class="flex flex-col gap-2 items-center">
                    <select
                      v-model="selectedMonthsPerRent[contract.id]"
                      class="border border-gray-400 p-1 rounded"
                    >
                      <option :value="null" disabled selected>
                        Selecciona una opción
                      </option>
                      <option v-for="n in 12" :key="n" :value="n">
                        {{ n }} mes(es)
                      </option>
                    </select>
                    <button
                      class="bg-blue-500 text-white px-3 py-1 rounded disabled:opacity-50"
                      :disabled="isInCart(contract.id, 'rent')"
                      @click="addContractToRent(contract)"
                    >
                      {{
                        isInCart(contract.id, 'rent')
                          ? "Agregado"
                          : "Agregar"
                      }}
                    </button>

                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Tabla de cargos individuales -->
        <div class="bg-white shadow rounded-lg p-5">
          <h3 class="text-lg font-bold mb-3">Cargos de Instalaciones</h3>
          <table class="table-auto w-full border-collapse border border-gray-300 text-sm">
            <thead  class="text-xs text-gray-700 uppercase bg-gray-950">
              <tr>
                <th class="border border-gray-300 p-2">Descripción</th>
                <th class="border border-gray-300 p-2">Del contrato</th>

                <th class="border border-gray-300 p-2">Monto</th>
                <th class="border border-gray-300 p-2">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="charge in availableCharges" :key="charge.id">
                <td class="border border-gray-300 p-2">{{ formatDescription(charge.description) }}</td>
                <td class="border border-gray-300 p-2">{{ formatDescription(charge.contractId) }}</td>
                <td class="border border-gray-300 p-2">{{ formatCurrency(charge.amount) }}</td>
                <td class="border border-gray-300 p-2">
                  <button
                    class="bg-green-500 text-white px-3 py-1 rounded"
                    :disabled="isInCart(charge.id,'individual-charge')"
                    @click="addChargeToCart(charge)"
                  >
                    {{
                      isInCart(charge.id, "individual-charge") ? "Agregado": "Agregar"
                    }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div></div>
        <!-- Carrito -->
        <div class="bg-white shadow rounded-lg p-5">
          <h3 class="text-lg font-bold mb-3">Carrito</h3>
          <table class="table-auto w-full border-collapse border border-gray-300 text-sm">
            <thead class="text-xs text-gray-700 uppercase bg-gray-950">
              <tr>
                <th class="border border-gray-300 p-2">Concepto</th>
                <th class="border border-gray-300 p-2">Monto</th>
                <th class="border border-gray-300 p-2">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in cart" :key="index">
                <td class="border border-gray-300 p-2">
                  {{ formatDescription(item.description) }}
                </td>
                <td class="border border-gray-300 p-2">
                  {{ formatCurrency(item.amount) }}
                </td>
                <td class="border border-gray-300 p-2">
                  <button
                    v-if="setButton(item.type)"
                    class="bg-red-500 text-white px-3 py-1 rounded"
                    @click="removeFromCart(item.id, item.type)"
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
       
        <div></div>
        <!-- Sección de Pago -->
        <div class="bg-gray-100 shadow rounded-lg p-5">
          <h3 class="text-lg font-bold mb-3">Métodos de Pago</h3>
          <div v-if="showPayment" class="mt-5">
            <GetData :totalAmount="totalAmount" :cart="cart" :paypal="paypal" :mercadopago="mercadopago.active" />
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
  background-color: #9321fe;
  /*
  background-image: linear-gradient(to right, var(--tw-gradient-stops));
  --tw-gradient-from: #6366f1 var(--tw-gradient-from-position);
  --tw-gradient-stops: var(--tw-gradient-from), #a855f7 var(--tw-gradient-via-position), var(--tw-gradient-to);
  --tw-gradient-to: #ec4899 var(--tw-gradient-to-position);*/
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