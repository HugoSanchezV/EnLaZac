<script setup>
import resources_js_components_PaypalButton from "@/Components/Base/Pays/resources_js_components_PaypalButton.vue";
import resources_vue_components_MercadoPagoButton from "@/Components/Base/Pays/resources_vue_components_MercadoPagoButton.vue";
import { onMounted } from "vue";
import { ref } from "vue";


const props = defineProps({
  totalAmount:{
    type: String
  },
  selectedMonths:{
    type:Number
  },
  contract:{
    type: Object
  },
  cart:{
    type: Object
  }
});
const carrito = ref([]);
onMounted(() => {
  props.cart.forEach(function(element, index, array){
      if(element.id != "service"){
        carrito.value.push(element);
      }
  });

});

</script>

<template>

  <!-- <resources_js_components_PaypalButton
    :totalAmount="totalAmount"
    :selectedMonths = "selectedMonths"
    :contract = "contract"
    :cartCharge = "carrito"
    :allCart = "cart">
  </resources_js_components_PaypalButton> -->
  <resources_vue_components_MercadoPagoButton>

  </resources_vue_components_MercadoPagoButton>

</template>
