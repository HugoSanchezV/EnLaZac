<template>
  <div>
    <div id="paypal-button-container"></div>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  mounted() {
    paypal
      .Buttons({
        createOrder: async (data, actions) => {
         // alert("bien");
          const response = await axios.post('/api/paypal/create-order');
          ///alert("bien");
          return response.data.id;
        },

        onApprove: async (data, actions) => {
          const response = await axios.post("/api/paypal/capture-order", {
            orderID: data.orderID,
          });
          if (response.data.status === "success") {
            alert("Pago completado con éxito!");
          } else {
            alert("Hubo un problema con el pago.");
          }
        },

        onError: (err) => {
          console.error("Error aqui: "+err.message);
          alert("Error en el proceso de pago.");
        },
      })
      .render("#paypal-button-container");
  },
};
</script>
