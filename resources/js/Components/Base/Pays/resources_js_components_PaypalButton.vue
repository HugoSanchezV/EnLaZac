<template>
  <div>
    <!-- Contenedor principal para centrar el botón de PayPal -->
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
      required: true,
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
    paypal_data: {
      type: Array,
    },
  },
  mounted() {
    // Verificar si el script de PayPal ya está cargado
    if (this.paypal_data.active) {
      if (!document.getElementById("paypal-script")) {
        const script = document.createElement("script");
        script.id = "paypal-script";
        script.src = `https://www.paypal.com/sdk/js?client-id=${this.paypal_data.live_client_id}&currency=MXN`;
        script.onload = () => {
          // Iniciar los botones de PayPal solo después de que el script se haya cargado
          this.initializePayPalButtons();
        };
        document.body.appendChild(script);
      } else {
        // Si el script ya está cargado, inicializa los botones directamente
        this.initializePayPalButtons();
      }
    }
  },
  methods: {
    initializePayPalButtons() {
      try {
        paypal
          .Buttons({
            createOrder: async (data, actions) => {
              // Crear la orden de pago en el backend
              const response = await axios.post("/api/paypal/create-order", {
                amount: this.totalAmount,
              });

              // Log para depuración
             // console.log("Carrito:", this.allCart);

              // Devolver el ID de la orden para PayPal
              return response.data.id;
            },
            onApprove: async (data, actions) => {
              // Capturar el pago en el backend
              try {
                const toast = useToast();
                const response = await axios.post("/api/paypal/capture-order", {
                  orderID: data.orderID,
                  amount: this.totalAmount,
                  charges: this.cartCharge,
                  cart: this.allCart,
                });

                if (response.data.status === "success") {
                  toast.success(
                    "Pago realizado con éxito, gracias por estar con nosotros",
                    {
                      position: POSITION.TOP_CENTER,
                      draggable: true,
                    }
                  );

                  // Redirigir usando Inertia o el router
                  // window.location.href = response.data.redirect;
                  this.$inertia.visit(response.data.redirect);
                } else {
                  toast.error("No se realizó el pago", {
                    position: POSITION.TOP_CENTER,
                    draggable: true,
                  });
                }
              } catch (error) {
                console.error("Error al capturar la orden:", error.message);
                toast.error(
                  "Hubo un problema al procesar el pago. Inténtelo de nuevo.",
                  {
                    position: POSITION.TOP_CENTER,
                    draggable: true,
                  }
                );
              }
            },
            onError: (err) => {
              console.error("Error en PayPal:", err.message);
              this.$toast.error(
                "Hubo un problema con el pago. Inténtelo de nuevo.",
                {
                  position: POSITION.TOP_CENTER,
                  draggable: true,
                }
              );
            },
          })
          .render("#paypal-button-container");
      } catch (error) {
        console.error(
          "Error al inicializar los botones de PayPal:",
          error.message
        );
      }
    },
  },
};
</script>
