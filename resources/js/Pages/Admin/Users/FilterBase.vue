<template>
  <div>
    <button
      id="dropdownRadioButton"
      @click="toggleDropdown"
      class="uppercase gap-2 inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-3 py-1.5"
      type="button"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="size-6"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5"
        />
      </svg>
    </button>

    <!-- Mostrar la opción seleccionada -->

    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      id="dropdownRadio"
      class="z-10 w-48 bg-white divide-y divide-gray-100 rounded-lg shadow absolute"
    >
      <ul class="p-3 space-y-1 text-sm text-gray-700">
        <li v-for="element in list" :key="element.id">
          <div class="flex items-center p-2 rounded hover:bg-gray-100">
            <input
              :id="element.id"
              type="radio"
              :value="element[name]"
              v-model="currentElement"
              @change="selectElement(element[name])"
              name="type-radio"
              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 cursor-pointer"
            />
            <label
              :for="element.id"
              class="w-full ms-2 text-sm font-medium text-gray-900 rounded uppercase cursor-pointer"
            >
              {{ element.order }}
            </label>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>
  
  <script setup>
import { ref, defineProps } from "vue";

const emit = defineEmits(["elementSelected"]);

const currentElement = ref("ASC"); // La opción seleccionada por defecto
const dropdownOpen = ref(false); // Estado del dropdown

const props = defineProps({
  list: Array, // Lista de opciones
  name: String,
});

// Función para alternar el estado del dropdown
const toggleDropdown = () => {
  dropdownOpen.value = !dropdownOpen.value;
};

// Función para seleccionar un elemento y cerrar el dropdown
const selectElement = (element) => {
  currentElement.value = element; // Actualizar el valor de la opción seleccionada
  toggleDropdown(); // Cerrar el dropdown
  emit("elementSelected", element);
};
</script>
  