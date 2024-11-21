<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  settings: Object,
});

// Configuración inicial del formulario
const form = useForm({
  provider: "",
  account_sid: "",
  auth_token: "",
  phone_number: "",
});

// Cargar configuraciones existentes al montar el componente
onMounted(() => {
  if (props.settings) {
    form.provider = props.settings.provider || "";
    form.account_sid = props.settings.account_sid || "";
    form.auth_token = props.settings.auth_token || "";
    form.phone_number = props.settings.phone_number || "";
  }
});

// Función para enviar el formulario
const submit = () => {
  console.log(form);
  form.post(route("settings.sms.update"), {
    onFinish: () => form.reset(),
  });
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Edita las credenciales de acceso a SMS
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="provider" value="Proveedor de SMS" />
        <TextInput
          id="provider"
          v-model="form.provider"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="provider"
        />
        <InputError class="mt-2" :message="form.errors.provider" />
      </div>

      <div>
        <InputLabel for="account_sid" value="SID de la Cuenta" />
        <TextInput
          id="account_sid"
          v-model="form.account_sid"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="account_sid"
        />
        <InputError class="mt-2" :message="form.errors.account_sid" />
      </div>

      <div>
        <InputLabel for="auth_token" value="Token de Autenticación" />
        <TextInput
          id="auth_token"
          v-model="form.auth_token"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="auth_token"
        />
        <InputError class="mt-2" :message="form.errors.auth_token" />
      </div>

      <div>
        <InputLabel for="phone_number" value="Número de Teléfono" />
        <TextInput
          id="phone_number"
          v-model="form.phone_number"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="phone_number"
        />
        <InputError class="mt-2" :message="form.errors.phone_number" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Guardar Cambios
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  props: ["router"],
  data() {
    return {
      modificarPassword: false,
    };
  },
};
</script>

<style scoped>
/* Opcional: Estilo para un diseño consistente */
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
