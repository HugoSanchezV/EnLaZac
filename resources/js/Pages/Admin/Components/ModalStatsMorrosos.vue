<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
    <div
      class="fixed inset-0 bg-gray-500 bg-opacity-10"
      @click="closeModal"
    ></div>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full"
      >
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start justify-center">
            <div class="text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900 text-wrap">
                {{ props.title }}
              </h3>
              <div>
                <h1>Lista de Registros</h1>

                <!-- Lista de registros -->
                <ul class="record-list">
                  <!-- Itera sobre los registros -->
                  <li v-for="(morroso, index) in morrosos" :key="morroso.id" class="record-item">
                    <div class="record-info">
                      <p>
                        <strong>{{ morroso.id }}:</strong> 
                        de {{ morroso.inventorie_device.device.user.name}} 
                        - Fecha de terminación: {{ morroso.end_date }}&nbsp;&nbsp;&nbsp;</p>
                    </div>
                    <!-- Botón Mostrar -->
                    <Link :href="route('contracts.show',morroso.id)" class="show-button">Mostrar</Link>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
          <button
            @click="closeModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, defineEmits, defineProps, watch } from "vue";
import { Link } from "@inertiajs/vue3";

const emit = defineEmits(["close", "selectData"]);
const props = defineProps({
  show: Boolean,
  data: Object,
  id: Number,
  itemText: String,
  title: String,
});

const morrosos = ref(props.data);


const closeModal = () => {
  emit("close");
};

</script>
<style scoped>
/* Estilos básicos para la lista */
.record-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.record-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  border-bottom: 1px solid #ddd;
  border-radius: 5px;
}

/* Estilo para el botón */
.show-button {
  background-color: #4caf50;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 14px;
}

.show-button:hover {
  background-color: #45a049;
}

.record-info p {
  margin: 0;
}
</style>