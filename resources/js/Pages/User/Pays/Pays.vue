
<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import GetData from "./GetData.vue"

const props = defineProps({
  charges:{
    type: Array
  },
  cost_service: {
    type: Number
  },
  contract:{
    type: Object
  }
});



</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:content>
      

    <div class="flex justify-start w-full">
      <div class="w-full">
        <div class="payment-process"></div>
  
      <div>
        <h2>Proceso de Pago - Servicio de Internet</h2>
      </div>
      <div class="mt-3 mb-3">
        <label for="">El valor unitario de tu plan es de $</label>
        <span>{{ cost_service }}</span>
      </div>
      <!-- Selección de meses -->
      <div class="select-months">
        <label for="months">Seleccionar meses a pagar: </label>
        <select v-model="selectedMonths" @change="addMonthToCart">
          <option value="0" selected>--Seleccionar mes--</option>
          <option v-for="n in 12" :key="n" :value="n">{{ n }} mes(es)</option>
        </select>
        
      </div>
      
      <!-- Tabla de cargos generados -->
      <div class="client-charges">
        <h3>Cargos del Cliente</h3>
        <table>
          <thead>
            <tr>
              <th>Descripción</th>
              <th>Monto</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="charge in charges" :key="charge.id">
              <td>{{ charge.description }}</td>
              <td>{{ formatCurrency(charge.amount) }}</td>
              <td>
                <button :disabled="isInCart(charge.id)" @click="addChargeToCart(charge)">
                  {{ isInCart(charge.id) ? 'Ya agregado' : 'Agregar al carrito' }}
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tabla del carrito -->
      <div class="cart">
        <h3>Carrito</h3>
        <table>
          <thead>
            <tr>
              <th>Concepto</th>
              <th>Monto</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in cart" :key="index">
              <td>{{ item.description }}</td>
              <td>{{ formatCurrency(item.amount) }}</td>
              <td><button @click="removeFromCart(index)">Eliminar</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Label del total -->
      <div class="total-label">
        <h4>Total a pagar: {{ formatCurrency(totalAmount) }}</h4>
      </div>

      <!-- Botón para realizar el pago -->
      <button class="pay" @click="processPayment">Realizar Pago</button>
    </div>
    <!-- Mensaje de error si no hay artículos en el carrito -->
  </div>
  

      <div v-if="showPayment">
        <GetData
          :totalAmount="totalAmount"
          :selectedMonths = "selectedMonths"
          :contract = "contract"
          :cart = "cart"
        >
        </GetData>
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

th, td {
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
<script>
export default {
  data() {
    return {
      selectedMonths: 0,
      cart: [],
      clientCharges: [], // Cargos obtenidos de la base de datos
      totalAmount: 0,
      serviceAdded: false, // Nueva bandera para controlar si ya se ha agregado el servicio
      paymentError: '',
      showPayment: false,
    };
  },
  methods: {
    // Función para agregar los meses seleccionados al carrito
    addMonthToCart() {
      if (this.serviceAdded) {
        this.warning();
        return;
      }

      const serviceItem = {
        id: 'service', // Asignar un id único para el servicio
        description: `Servicio por ${this.selectedMonths} mes(es)`,
        amount: this.cost_service * this.selectedMonths
      };
      this.cart.push(serviceItem);
      this.serviceAdded = true; // Marcar que ya se agregó el servicio
      this.calculateTotal();
    },
    
    // Función para agregar un cargo de la tabla de cargos al carrito
    addChargeToCart(charge) {
      if (this.isInCart(charge.id)) {
        alert('Este cargo ya ha sido agregado.');
        return;
      }
      this.cart.push({
        id: charge.id, // Incluir el id para evitar duplicados
        description: charge.description,
        amount: charge.amount
      });
      this.calculateTotal();
    },
    
    // Verificar si un cargo ya está en el carrito
    isInCart(itemId) {
      return this.cart.some(item => item.id === itemId);
    },
    
    // Función para remover un artículo del carrito
    removeFromCart(index) {
      const removedItem = this.cart[index];
      if (removedItem.id === 'service') {
        this.serviceAdded = false; // Permitir agregar el servicio de nuevo si es eliminado
        this.selectedMonths = 0;
      }
      this.cart.splice(index, 1);
      this.calculateTotal();
    },
    
    // Cálculo del total
    calculateTotal() {
      this.totalAmount = this.cart.reduce((acc, item) => acc + item.amount, 0);
      
      if(this.totalAmount < 1)
      {
        this.showPayment = false;
      }
  
    },
    
    // Formateo de la moneda
    formatCurrency(value) {
      return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
    },
    warning(){
      const toast = useToast();
        toast(
          {
            component: BaseQuestion,
            props: {
              message: "Ya seleccionaste un servicio",
            },
          },
          {
            type: TYPE.ERROR,
            position: POSITION.TOP_CENTER,
            timeout: 10000,
          }
        );
    },
    error(){
      const toast = useToast();
        toast(
          {
            component: BaseQuestion,
            props: {
              message: "¿Selecciona un servicio o cargo?",
            },
          },
          {
            type: TYPE.ERROR,
            position: POSITION.TOP_CENTER,
            timeout: 10000,
          }
        );
    },
    // Función para procesar el pago
    processPayment() {
      if (this.cart.length === 0) {
        this.error();
        //this.paymentError = 'Agregar por lo menos un artículo a pagar';
        this.showPayment = false;
      } else {
        this.paymentError = '';
        this.showPayment = true;
        // Aquí llamarías a un componente o función externa que maneje el proceso de pago
        console.log('Procesando pago con los siguientes artículos:', this.cart);
        // Lógica para continuar con el pago, integración con tu componente de pago
      }
    }
  },
};
</script>