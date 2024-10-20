<script setup>
import resources_js_components_PaypalButton from "@/Components/Base/Pays/resources_js_components_PaypalButton.vue";
import { onMounted } from "vue";

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
onMounted(() => {
  props.cart.forEach((element) => console.log(element.id));
});

</script>

<template>
  <resources_js_components_PaypalButton
    :totalAmount="totalAmount"
    :selectedMonths = "selectedMonths"
    :contract = "contract"
    :cart = "cart"
  >
  </resources_js_components_PaypalButton>
</template>
