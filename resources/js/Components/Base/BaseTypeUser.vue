<template>
  <h2
    v-if="cell === 0"
    class="bg-green-500 text-white px-2 py-1 rounded flex justify-start items-center gap-2"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="size-5"
    >
      <path
        d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"
      />
    </svg>

    Cliente
  </h2>
  <h2
    v-else-if="cell === 2"
    class="bg-blue-500 text-white px-2 py-1 rounded flex justify-start gap-2"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      class="h-4 w-4 mr-1"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M5.121 17.804A1 1 0 016.638 17h10.724a1 1 0 01.8 1.6l-5.363 6.463a1 1 0 01-1.598 0l-5.36-6.463z"
      />
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 14c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4zM12 10a4 4 0 100-8 4 4 0 000 8z"
      />
    </svg>

    Coordinador
  </h2>
  <h2
    v-else-if="cell === 3"
    class="bg-yellow-500 text-white px-2 py-1 rounded flex justify-start gap-2"
  >
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 20 20"
      fill="currentColor"
      class="size-5"
    >
      <path
        fill-rule="evenodd"
        d="M19 5.5a4.5 4.5 0 0 1-4.791 4.49c-.873-.055-1.808.128-2.368.8l-6.024 7.23a2.724 2.724 0 1 1-3.837-3.837L9.21 8.16c.672-.56.855-1.495.8-2.368a4.5 4.5 0 0 1 5.873-4.575c.324.105.39.51.15.752L13.34 4.66a.455.455 0 0 0-.11.494 3.01 3.01 0 0 0 1.617 1.617c.17.07.363.02.493-.111l2.692-2.692c.241-.241.647-.174.752.15.14.435.216.9.216 1.382ZM4 17a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"
        clip-rule="evenodd"
      />
    </svg>

    Técnico
  </h2>
  <h2 v-else>{{ cell }}</h2>
</template>

<script setup>
import { defineProps, watch } from "vue";
const props = defineProps(["cell"]);

const cell = props.cell;

watch("cell", (newValue) => {
    cell.value = cellnewValue
});
</script>