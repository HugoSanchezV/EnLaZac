<script setup>
import DashboardBase from "@/Pages/DashboardBase.vue";
import resources_js_components_PaypalButton from "@/Components/Base/Pays/resources_js_components_PaypalButton.vue";
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:content>
      <div>
        <div class="payment-process">
    <h2>Proceso de Pago - Servicio de Internet</h2>
    
    <!-- Selección de meses -->
    <div class="select-months">
      <label for="months">Seleccionar meses a pagar:</label>
      <select v-model="selectedMonths" @change="addMonthToCart">
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
          <tr v-for="charge in clientCharges" :key="charge.id">
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
    <button @click="processPayment">Realizar Pago</button>

    <!-- Mensaje de error si no hay artículos en el carrito -->
    <p v-if="paymentError" class="error-message">{{ paymentError }}</p>
  </div>
      </div>
      <resources_js_components_PaypalButton></resources_js_components_PaypalButton>
    </template>
  </dashboard-base>
</template>
<script>
export default {
  data() {
    return {
      selectedMonths: 1,
      cart: [],
      clientCharges: [], // Cargos obtenidos de la base de datos
      totalAmount: 0,
      serviceAdded: false, // Nueva bandera para controlar si ya se ha agregado el servicio
      paymentError: ''
    };
  },
  methods: {
    // Función para agregar los meses seleccionados al carrito
    addMonthToCart() {
      if (this.serviceAdded) {
        alert('Ya has agregado el servicio de internet.');
        return;
      }
      const serviceItem = {
        id: 'service', // Asignar un id único para el servicio
        description: `Servicio por ${this.selectedMonths} mes(es)`,
        amount: 250 * this.selectedMonths
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
      }
      this.cart.splice(index, 1);
      this.calculateTotal();
    },
    
    // Cálculo del total
    calculateTotal() {
      this.totalAmount = this.cart.reduce((acc, item) => acc + item.amount, 0);
    },
    
    // Formateo de la moneda
    formatCurrency(value) {
      return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value);
    },
    
    // Función para procesar el pago
    processPayment() {
      if (this.cart.length === 0) {
        this.paymentError = 'Agregar por lo menos un artículo a pagar';
      } else {
        this.paymentError = '';
        // Aquí llamarías a un componente o función externa que maneje el proceso de pago
        console.log('Procesando pago con los siguientes artículos:', this.cart);
        // Lógica para continuar con el pago, integración con tu componente de pago
      }
    }
  },
  mounted() {
    // Aquí realizarías la consulta a la base de datos para obtener los cargos del cliente
    this.clientCharges = [
      { id: 1, description: 'Cargo por mora', amount: 100 },
      { id: 2, description: 'Cargo por reconexión', amount: 150 }
    ];
  }
};
</script>

<style scoped>
.payment-process {
  max-width: 800px;
  margin: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th, td {
  border: 1px solid #ccc;
  padding: 10px;
  text-align: center;
}

.total-label {
  margin-bottom: 20px;
}

.error-message {
  color: red;
}
</style>