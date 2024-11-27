<template>
  <div class="flex flex-col items-center justify-center">
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
        <!-- Activar Servicio -->
        <div class="flex items-center gap-2">
          <InputLabel for="active" value="Activar Servicio " />
          <label class="switch">
            <input type="checkbox" v-model="form.active" true-value="1" false-value="0" />
            <span class="slider round"></span>
          </label>
          <!-- <span>{{ form.active === "1" ? "Activo" : "Inactivo" }}</span> -->
        </div>

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
  active: 0, // Nuevo campo 'active' para el toggle
  mode: "sandbox",
  sandbox_client_id: "",
  sandbox_client_secret: "",
  live_client_id: "",
  live_client_secret: "",
  currency: "MXN",
});

// Asignar los valores existentes al formulario cuando el componente se monta
onMounted(() => {
  if (props.settings) {
    form.active = props.settings.active || 0; // Asignar valor de 'active'
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
  form.put(route("settings.mercadopago.update"), {
    onSuccess: () => {
      // Opcional: lógica adicional al tener éxito
    },
    onError: () => {
      // Opcional: Manejo de errores
    },
  });
};
</script>

<style scoped>
/* Estilos adicionales para el toggle */
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
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #2196f3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196f3;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 17px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>
