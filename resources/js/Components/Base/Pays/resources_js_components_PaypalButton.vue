<template>
  <div>
    <div id="paypal-button-container"></div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  props: {
    totalAmount: {
      type: Number,
    },
    selectedMonths: {
      type: Number,
    },
    contract: {
      type: Object,
    },
    cartCharge: {
      type: Object,
    },
    allCart: {
      type: Object,
    },
  },
  mounted() {
    paypal
      .Buttons({
        createOrder: async (data, actions) => {
          // alert("bien");
          const response = await axios.post("/api/paypal/create-order", {
            // amount: this.totalAmount,
            amount: 10.0,
            // mounths: this.selectedMonths,
            // contract: this.contract,
            // charges: this.cartCharge,
            // cart: this.allCart,
          });
          ///alert("bien");
          return response.data.id;
        },

        onApprove: async (data, actions) => {
          const response = await axios.post("/api/paypal/capture-order", {
            orderID: data.orderID,
            // amount: this.totalAmount,
            // mounths: this.selectedMonths,
            // contract: this.contract,
            // charges: this.cartCharge,
            // cart: this.allCart
          });
          if (response.data.status === "success") {
            alert("Pago completado con éxito!");
          } else {
            alert("Hubo un problema con el pago.");
          }
        },

        onError: (err) => {
          console.error("Error aqui: " + err.message);
          //alert("Error en el proceso de pago.");
        },
      })
      .render("#paypal-button-container");
  },
};
</script>
