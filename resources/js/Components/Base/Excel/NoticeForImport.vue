<script setup>
import { defineProps } from "vue";

const props = defineProps({
  data: {
    type: String,
    required: true,
  },
});
</script>
<template>
  <div>
    <h2 class="font-bold">
      Importante<span class="text-red-600 font-extrabold">*</span>
    </h2>
    <ul>
      <li class="mb-3">
        <p>
          Solo se aceptan archivos
          <span class="font-extrabold">.csv, .xls, .xlsx</span>
        </p>
      </li>

      <li class="mb-3">
        <p>
          Se debe respetar la estructura (Headings)
          <span class="font-extrabold">
            {{ data }}
          </span>
        </p>
      </li>

      <li class="mb-3">
        <p class="text-red-400">
          En caso de haber un error en los datos, se cancelará toda la operación
        </p>
      </li>
    </ul>
  </div>
</template>