<template>
  <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto">
    <div class="fixed inset-0 bg-black bg-opacity-60" @click="closeModal"></div>
    <div class="flex items-center justify-center min-h-screen px-4">
      <div
        class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full"
      >
        <div class="p-5">
          <div class="mb-2">
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
                      <!-- Aquí se muestra el estado dinámico -->
                    </span>
                  </p>
                </li>

                <li class="mb-3">
                  <p class="text-red-400">
                    En caso de haber un error en los datos, se cancelará toda la
                    operación
                  </p>
                </li>
              </ul>
            </div>
          </div>
          <div class="m-2">
            {{ warning }}
          </div>
          <!-- Aquí añadimos el switch tipo toggle con Tailwind -->
          <div
            class="mb-4 flex items-center justify-center p-2 rounded-md"
            :class="useUserRoute ? 'bg-red-500' : 'bg-green-500'"
          >
            <label for="switchRoute" class="mr-2 text-gray-50"
              >Subir informacion al Router</label
            >
            <!-- Toggle switch -->
            <label class="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                id="switchRoute"
                v-model="useUserRoute"
                class="sr-only peer"
              />
              <div
                class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-gray-700 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-400"
              ></div>
            </label>
          </div>

          <form @submit.prevent="submitForm">
            <div class="flex justify-center mb-1">
              <input
                type="file"
                name="excel"
                id="excel"
                accept=".csv, .xls, .xlsx"
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
import { defineEmits, defineProps, ref, reactive, computed } from "vue";
import { router } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

const emit = defineEmits(["close"]);
const props = defineProps({
  show: Boolean,
  title: String,
  toImportRoute: String, // Ruta inicial
});

// Estado del switch para cambiar la ruta
const useUserRoute = ref(false);

// Computed para que el texto de "data" cambie dinámicamente según el estado del switch
const data = computed(() => {
  return useUserRoute.value
    ? "id disposotivo, id usuario, comentario, direccion, id router, desactivado (0 | 1) \n (Si no hay relacion dejar vacio, el router es obligatorio)"
    : "id interno, id disposotivo, id usuario, comentario, direccion, id router, desactivado (0 | 1) \n (Si no hay relacion dejar vacio, el router es obligatorio)";
});

const warning = computed(() => {
  return useUserRoute.value
    ? "Los datos serán dados de alta en la base de datos y enviados al router, debes tener cuidado con el trato de los datos en el router"
    : "Los datos serán dados de alta en la base de de datos";
});

const closeModal = () => {
  emit("close");
};

// Refs para el archivo
const fileInput = ref(null);

const form = reactive({
  errors: {},
});

// Función para determinar la ruta según el estado del switch
const getRoute = () => {
  // verde //rojo
  return useUserRoute.value ? props.toImportRoute : props.toImportRoute;
};

const submitForm = () => {
  const formData = new FormData();
  const file = fileInput.value.files[0];

  if (file) {
    // Añadir el archivo a FormData
    formData.append("excel", file);

    // Obtener la ruta dinámicamente
    const route = getRoute();

    // alert(route)
    // Enviar la solicitud con Inertia
    router.post(route, formData, {
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
  