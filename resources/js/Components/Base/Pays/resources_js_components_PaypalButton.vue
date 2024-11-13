<template>
  <div>
    <div id="paypal-button-container"></div>
  </div>
</template>

<script>
import axios from "axios";
import { POSITION, useToast } from "vue-toastification";
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
            amount: this.totalAmount,
          });
          console.log(response);
          return response.data.id;
        },

        onApprove: async (data, actions) => {
          const response = await axios.post("/api/paypal/capture-order", {
            orderID: data.orderID,
            amount: this.totalAmount,
            mounths: this.selectedMonths,
            contract: this.contract,
            charges: this.cartCharge,
            cart: this.allCart
          });

          const toast = useToast();
          if (response.data.status === "success") {
            toast.success(
              "Pago Realizado con exito, gracias por estar con nosotros",
              {
                position: POSITION.TOP_CENTER,
                draggable: true,
              }
            );

            
          } else {
            toast.error("No se realizo el pago", {
              position: POSITION.TOP_CENTER,
              draggable: true,
            });
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
