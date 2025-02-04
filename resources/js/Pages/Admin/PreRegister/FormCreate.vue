<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { POSITION, useToast } from "vue-toastification";

const toast = useToast();

const form = useForm({
  phone: "",
});

const submit = () => {
  if (!/^(52|1)\d{10}$/.test(form.phone)) {
    toast.error(
      "El número de teléfono debe comenzar con 52(mx) o 1(usa) y tener exactamente 10 dígitos después",
      {
        position: POSITION.TOP_CENTER,
        draggable: true,
      }
    );
    return;
  }

  const url = route("usuarios.pre.register.store");
  form.post(url);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Este formulario es exclusivo para poder pre registrar clientes
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div class="mt-4">
        <InputLabel for="phone" value="Teléfono" />
        <p class="text-sm mt-1 text-gray-600">52(mx) o 1(usa) seguido de 10 dígitos <span class="text-red-500">*</span> </p>
        <TextInput
          minlength="12"
          maxlength="12"
          id="phone"
          v-model="form.phone"
          type="number"
          class="mt-1 block w-full"
          autocomplete="phone"
        />
        <InputError class="mt-2" :message="form.errors.phone" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Registrar
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>


<script>
export default {
  props: ["user"],
  data() {
    return {
      ubicacionManual: false,
    };
  },
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