<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
    <div
      class="fixed inset-0 bg-black bg-opacity-70"
      @click="closeModal"
    ></div>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full"
      >
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start">
            <div class="text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900 text-wrap">
                {{ props.title }}
              </h3>
              <div class="mt-2">
                <select
                  v-model="selectedUser"
                  class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                >
                  <option value="null" selected>Selecciona una opción</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.id + " - " + user[itemText] }}
                  </option>
                </select>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="confirmSelection"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Confirmar
          </button>
          <button
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits, defineProps, watch } from "vue";

const emit = defineEmits(["close", "selectData"]);
const props = defineProps({
  show: Boolean,
  data: Object,
  id: Number,
  itemText: String,
  title: String,
});

const selectedUser = ref(null);
const users = ref(props.data);

// Resetea el usuario seleccionado cuando el modal se abre o cierra
watch(
  () => props.show,
  (newVal) => {
    if (!newVal) {
      selectedUser.value = null; // Resetea la selección cuando se cierra el modal
    }
  }
);
watch(
  () => props.data,
  (newValue) => {
    users.value = newValue
  }
);

const closeModal = () => {
  emit("close");
};

const confirmSelection = () => {
  emit("selectData", { selectId: selectedUser.value, itemId: props.id });

  closeModal();
};
</script>
