<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-10" @click="closeModal"></div>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-xs sm:w-full">
        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
          <div class="sm:flex sm:items-start justify-center">
            <div class="text-center sm:mt-0 sm:ml-4 sm:text-left">
              <h3 class="text-lg leading-6 font-medium text-gray-900 text-wrap">
                {{ title }}
              </h3>
              <div class="mt-2">
                <label class="switch">
                  <input
                    type="checkbox"
                    id="activated"
                    @change="getCurrentLocation"
                    v-model="selectStatus"
                  />
                  <span class="slider round"></span>
                </label>
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
import { ref, defineEmits, defineProps, computed, watch } from "vue";

const props = defineProps({
  paymentSanction: Array,
  show: Boolean,
  id: Number,
  itemText: String,
  title: String,
});

// Emitir eventos
const emit = defineEmits(["close", "selectData"]);

// Reactive data
const selectStatus = ref(false);

// Computed para obtener el contrato actual
const currentSanction = computed(() =>
  props.paymentSanction.find((contract) => contract.id === props.id) || null
);

// Watch para resetear datos cuando el modal cambia
watch(
  () => props.show,
  (newVal) => {
    if (newVal && currentSanction.value) {
      console.log(currentSanction.value);
      selectStatus.value = !!currentSanction.value.payment_sanction?.status;
    } else if (!newVal) {
      
      selectStatus.value = false;
    }
  },
  { immediate: true } // Ejecutar inmediatamente al cargar
);

// Métodos
const closeModal = () => {
  emit("close");
};

const confirmSelection = () => {
  //alert(ubicacionManual.value);
  emit("selectData", { selectId: currentSanction.value.payment_sanction?.id, status: selectStatus.value });
  closeModal();
};

</script>
<style scoped>
.switch {
  position: relative;
  display: inline-block;
  width: 44px;
  height: 20px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 13px;
  width: 13px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: 0.4s;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #2196f3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196f3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 17px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>