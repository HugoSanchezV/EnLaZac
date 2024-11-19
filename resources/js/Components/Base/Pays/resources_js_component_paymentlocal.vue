<script setup>
import { defineProps } from "vue";
import { router } from "@inertiajs/vue3";
const props = defineProps({
  totalAmount: {
    type: Number,
  },
  allCart: {
    type: Object,
  },
});

const pay = () => {
  const url = route("local.pay.store", {
    amount: props.totalAmount,
    cart: props.allCart,
  });

  router.post(url, {});
};
</script>

<template>
  <div>
    <button
      @click="pay"
      class="bg-orange-400 hover:bg-orange-500 text-white px-5 py-3 rounded w-full flex justify-center gap-2 cursor-pointer"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 16 16"
        fill="currentColor"
        class="size-6"
      >
        <path
          fill-rule="evenodd"
          d="M7.605 2.112a.75.75 0 0 1 .79 0l5.25 3.25A.75.75 0 0 1 13 6.707V12.5h.25a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1 0-1.5H3V6.707a.75.75 0 0 1-.645-1.345l5.25-3.25ZM4.5 8.75a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-1.5 0v-3ZM8 8a.75.75 0 0 0-.75.75v3a.75.75 0 0 0 1.5 0v-3A.75.75 0 0 0 8 8Zm2 .75a.75.75 0 0 1 1.5 0v3a.75.75 0 0 1-1.5 0v-3ZM8 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
          clip-rule="evenodd"
        />
      </svg>

      <span class="font-semibold">Pago</span> Local
    </button>
  </div>
</template>

