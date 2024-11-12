<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({settings: Object,});

const form = useForm({
  transport: "",
  host: "",
  port: "",
  encryption: "",
  username: "",
  password: "",
  address: "",
  name: "",
});

onMounted(() => {
  if (props.settings) {
    form.transport = props.settings.transport || "";
    form.host = props.settings.host || "";
    form.port = props.settings.port || "";
    form.encryption = props.settings.encryption || "";
    form.username = props.settings.username || "";
    form.password = props.settings.password || "";
    form.address = props.settings.address || "";
    form.name = props.settings.name || "";
  }
});

const submit = () => {
  //console.log(form);
  form.post(route("settings.email.update"));
};
</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Envio de email
    </h2>
  </div>

  <div class="mt-5 ">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">

          <div class="">
            <InputLabel for="transport" value="Transporte de Correo" />
            <TextInput
              id="transport"
              placeholder="smtp"
              v-model="form.transport"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="transport"
            />
            <InputError class="mt-2" :message="form.errors.transport" />
          </div>
          <div class="mt-2">
            <InputLabel for="host" value="Host" />
            <TextInput
              id="host"
              v-model="form.host"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="host"
            />
            <InputError class="mt-2" :message="form.errors.host" />
          </div>
      
          <div class="mt-2">
            <InputLabel for="port" value="Puerto" />
            <TextInput
              id="port"
              v-model="form.port"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="port"
            />
            <InputError class="mt-2" :message="form.errors.port" />
          </div>
          <div class="mt-2">
            <InputLabel for="encryption" value="Encriptación" />
            <TextInput
              id="encryption"
              v-model="form.encryption"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="encryption"
            />
            <InputError class="mt-2" :message="form.errors.encryption" />
          </div>

          <div class="mt-2">
            <InputLabel for="username" value="Usuario" />
            <TextInput
              id="username"
              v-model="form.username"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="username"
            />
            <InputError class="mt-2" :message="form.errors.username" />
          </div>
          <div class="mt-2">
            <InputLabel for="password" value="Contraseña" />
            <TextInput
              id="password"
              v-model="form.password"
              type="password"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="password"
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>


          <div class="mt-2">
            <InputLabel for="address" value="Correo remitente" />
            <TextInput
              id="address"
              v-model="form.address"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="address"
            />
            <InputError class="mt-2" :message="form.errors.address" />
          </div>
          <div class="mt-2">
            <InputLabel for="name" value="Nombre del remitente" />
            <TextInput
              id="name"
              v-model="form.name"
              type="text"
              class="mt-1 block w-full"
              required
              autofocus
              autocomplete="name"
            />
            <InputError class="mt-2" :message="form.errors.name" />
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

<style scoped>
/* Contenedor del formulario */
.form-container {
  background-color: #ffffff;
  padding: 40px;
  max-width: 600px;
  width: 100%;
  border-radius: 10px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 1.5rem;
  color: #2c3e50;
  font-weight: 600;
  margin-bottom: 20px;
}

/* Estilos de los grupos de campos */
.form-group {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
}

.form-field {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.form-field.full-width {
  flex: 2;
}

/* Estilos para InputLabel */
InputLabel {
  font-size: 0.9rem;
  color: #666;
  margin-bottom: 8px;
  font-weight: bold;
}

/* Estilos para TextInput */
TextInput {
  padding: 10px;
  font-size: 1rem;
  border-radius: 6px;
  border: 1px solid #ddd;
  background-color: #f9f9f9;
  transition: border-color 0.3s ease;
}

TextInput:focus {
  border-color: #3498db;
  outline: none;
  background-color: #ffffff;
}
</style>