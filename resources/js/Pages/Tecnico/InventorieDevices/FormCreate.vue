<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { watch } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  mac_address: "",
  //status: "",
  description: "",
  brand: "",
});

const submit = () => {
  form.post(route("inventorie.devices.store"), {
    onSuccess: () => {
      router.back();
    },
  });
};

watch(() => form.mac_address, (newVal) => {
  form.mac_address = newVal.toUpperCase();
})

const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Agrega un nuevo dispositivo al inventario
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="mac_address" value="Mac Address" />
        <TextInput
          id="mac_address"
          v-model="form.mac_address"
          type="text"
          class="mt-1 block w-full"
          placeholder="Ingrese la MAC (ej. 00:1A:2B:3C:4D:5E)"
          required
          autofocus
          autocomplete="mac_address"
        />
        <InputError class="mt-2" :message="form.errors.mac_address" />
      </div>

      <div class="mt-4">
        <InputLabel for="description" value="Descripción" />
        <TextInput
          id="description"
          v-model="form.description"
          placeholder="Modelo del dispositivo (ej. TD-8816)"
          type="text"
          class="mt-1 block w-full"
          
          required
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>

      <div class="mt-4">
        <InputLabel for="brand" value="Marca del Dispositivo" />
        <TextInput
          id="brand"
          v-model="form.brand"
          type="text"
          placeholder="Marca (ej. Netgear)"
          class="mt-1 block w-full"
          required
          autocomplete="brand"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <input type="hidden" id="admin" :value="form.admin" readonly />

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4 flex items-center gap-2"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            stroke-width="1.5"
            stroke="currentColor"
            class="size-6"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
            />
          </svg>

          Agregar Dispositivo
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
