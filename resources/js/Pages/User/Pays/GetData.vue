<script setup>
import resources_js_components_PaypalButton from "@/Components/Base/Pays/resources_js_components_PaypalButton.vue";
import resources_vue_components_MercadoPagoButton from "@/Components/Base/Pays/resources_vue_components_MercadoPagoButton.vue";
import resources_js_component_paymentlocal from "@/Components/Base/Pays/resources_js_component_paymentlocal.vue";
import { onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
  totalAmount: {
    type: String,
  },
  cart: {
    type: Object,
  },
  paypal: {
    type: Array,
  },
  mercadopago: {
    type: Number,
  },
});
const carrito = ref([]);
onMounted(() => {
  props.cart.forEach(function (element, index, array) {
    if (element.id != "service") {
      carrito.value.push(element);
    }
  });
});
</script>

<template>
  <div class="mt-5 w-full">
    <div class="mb-5 w-full">
      <!-- <resources_vue_components_MercadoPagoButton v-if="mercadopago['active']" -->
      <resources_vue_components_MercadoPagoButton
        v-if="mercadopago !== null && mercadopago == 1"
        :totalAmount="totalAmount"
        :allCart="cart"
      >
      </resources_vue_components_MercadoPagoButton>
    </div>

    <div class="mt-5 w-full">
      <resources_js_components_PaypalButton
        :totalAmount="totalAmount"
        :cartCharge="carrito"
        :allCart="cart"
        :paypal_data="paypal"
      >
      </resources_js_components_PaypalButton>
    </div>

    <div class="">
      <resources_js_component_paymentlocal
        :totalAmount="totalAmount"
        :allCart="cart"
      >
      </resources_js_component_paymentlocal>
    </div>
  </div>
</template>
