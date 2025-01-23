<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-60" @click="closeModal"></div>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full"
      >
        <div class="p-5">
          <div class="mb-2">
            <slot name="content"></slot>
          </div>
          <form @submit.prevent="submitForm">
            <div class="flex justify-center mb-1">
              <input
                type="file"
                name="excel"
                id="excel"
                accept=" .xls, .xlsx"
                required
                ref="fileInput"
              />
            </div>
            <div class="flex justify-end md:ml-2 md:px-2">
              <input
                type="submit"
                value="Importar"
                class="w-full md:w-auto md:px-6 bg-slate-600 hover:bg-slate-700 hover:cursor-pointer text-gray-100 p-1 rounded-md"
              />

              <InputError class="mt-2" :message="form.errors.rows" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>
  
<script setup>
import { defineEmits, defineProps, ref, reactive } from "vue";
import { router } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const emit = defineEmits(["close"]);
const props = defineProps({
  show: Boolean,
  title: String,
  toImportRoute: String,
});
const closeModal = () => {
  emit("close");
};

/////////////////////////////////////////////////////////////////

// Refs para el archivo
const fileInput = ref(null);

const form = reactive({
  errors: {},
});

const submitForm = () => {
  const formData = new FormData();
  const file = fileInput.value.files[0];

  if (file) {
    // Añadir el archivo a FormData
    formData.append("excel", file);

    // Enviar la solicitud con Inertia
    router.post(props.toImportRoute, formData, {
      forceFormData: true, // Para asegurar que se envíe como FormData
      onError: (errors) => {
        console.error(errors); // Manejo de errores si ocurre
      },
    });
    closeModal();
  } else {
    console.error("Debe seleccionar un archivo.");
  }
};
</script>
  