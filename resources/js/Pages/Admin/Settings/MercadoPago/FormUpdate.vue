<!-- resources/js/Pages/Admin/Settings/MercadoPago/FormUpdate.vue -->
<template>
  <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-5">
    <!-- Tarjeta Principal -->
    <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-8">
      <!-- Encabezado -->
      <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">MercadoPago - Configuraciones</h2>
        <p class="text-gray-500 mt-2">
          Actualiza las configuraciones de MercadoPago de manera segura y rápida.
        </p>
      </div>
      
      <!-- Formulario -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Modo -->
        <div>
          <InputLabel for="mode" value="Modo" />
          <select
            id="mode"
            v-model="form.mode"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
          >
            <option value="sandbox">Sandbox</option>
            <option value="live">Live</option>
          </select>
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.mode" />
        </div>

        <!-- Sandbox Client ID -->
        <div>
          <InputLabel for="sandbox_client_id" value="Sandbox Client ID" />
          <TextInput
            id="sandbox_client_id"
            v-model="form.sandbox_client_id"
            type="text"
            class="mt-1 block w-full"
            required
          />
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.sandbox_client_id" />
        </div>

        <!-- Sandbox Client Secret -->
        <div>
          <InputLabel for="sandbox_client_secret" value="Sandbox Client Secret" />
          <TextInput
            id="sandbox_client_secret"
            v-model="form.sandbox_client_secret"
            type="text"
            class="mt-1 block w-full"
            required
          />
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.sandbox_client_secret" />
        </div>

        <!-- Live Client ID -->
        <div>
          <InputLabel for="live_client_id" value="Live Client ID" />
          <TextInput
            id="live_client_id"
            v-model="form.live_client_id"
            type="text"
            class="mt-1 block w-full"
          />
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.live_client_id" />
        </div>

        <!-- Live Client Secret -->
        <div>
          <InputLabel for="live_client_secret" value="Live Client Secret" />
          <TextInput
            id="live_client_secret"
            v-model="form.live_client_secret"
            type="text"
            class="mt-1 block w-full"
          />
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.live_client_secret" />
        </div>

        <!-- Moneda -->
        <div>
          <InputLabel for="currency" value="Moneda" />
          <select
            id="currency"
            v-model="form.currency"
            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            required
          >
            <option value="MXN">MXN</option>
            <option value="USD">USD</option>
            <!-- Agrega más opciones de moneda según sea necesario -->
          </select>
          <InputError class="mt-2 text-sm text-red-600" :message="form.errors.currency" />
        </div>

        <!-- Botón de Envío -->
        <div>
          <PrimaryButton
            class="w-full flex justify-center items-center py-2 px-4 bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 text-white font-semibold rounded-md shadow transition duration-150 ease-in-out"
            :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
            :disabled="form.processing"
          >
            <span v-if="!form.processing">Guardar Cambios</span>
            <svg v-else class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
          </PrimaryButton>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
  settings: Object,
});

// Inicializar el formulario con los datos existentes
const form = useForm({
  mode: "sandbox",               // Valores por defecto
  sandbox_client_id: "",         // Inicializar como vacío
  sandbox_client_secret: "",     // Inicializar como vacío
  live_client_id: "",            // Inicializar como vacío
  live_client_secret: "",        // Inicializar como vacío
  currency: "MXN",               // Valores por defecto
});

// Asignar los valores existentes al formulario cuando el componente se monta
onMounted(() => {
  if (props.settings) {
    form.mode = props.settings.mode || "sandbox";
    form.sandbox_client_id = props.settings.sandbox_client_id || "";
    form.sandbox_client_secret = props.settings.sandbox_client_secret || "";
    form.live_client_id = props.settings.live_client_id || "";
    form.live_client_secret = props.settings.live_client_secret || "";
    form.currency = props.settings.currency || "MXN";
  }
});

// Método para enviar el formulario
const submit = () => {
  form.put(route("settings.mercadopago.update", props.settings.id), {
    onSuccess: () => {
      // Opcional: Puedes agregar lógica para mostrar una notificación de éxito
      // Por ejemplo, usar un sistema de notificaciones o redirigir al usuario
      // Ejemplo: window.alert('Configuración actualizada con éxito');
    },
    onError: () => {
      // Opcional: Manejar errores de validación
      // Ejemplo: window.alert('Hubo errores en el formulario');
    },
  });
};
</script>

<style scoped>
/* Estilos adicionales para mejorar la apariencia */
input::placeholder {
  color: #a0aec0; /* Color del placeholder */
}

input:focus::placeholder {
  color: transparent;
}

button:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.5);
}

/* Transiciones suaves para los inputs */
input,
select {
  transition: border-color 0.3s, box-shadow 0.3s;
}

/* Mejora de la apariencia de los errores */
.text-red-600 {
  color: #e53e3e;
}

/* Ajustes para el contenedor principal */
.min-h-screen {
  min-height: 100vh;
}

.bg-gray-50 {
  background-color: #f9fafb;
}

.bg-white {
  background-color: #ffffff;
}

.shadow-lg {
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}

.rounded-lg {
  border-radius: 0.5rem;
}

.p-8 {
  padding: 2rem;
}

.mb-6 {
  margin-bottom: 1.5rem;
}

.text-2xl {
  font-size: 1.5rem;
}

.font-bold {
  font-weight: 700;
}

.text-center {
  text-align: center;
}

.text-gray-800 {
  color: #2d3748;
}

.text-gray-500 {
  color: #a0aec0;
}

.space-y-6 > :not(template) ~ :not(template) {
  margin-top: 1.5rem;
}

.focus\:ring-2:focus {
  ring-width: 2px;
}

.focus\:ring-indigo-500:focus {
  ring-color: #667eea;
}

.transition {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
}

.duration-150 {
  transition-duration: 150ms;
}

.ease-in-out {
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>

