<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  user_id: "",
  plan_id: "",
  address: "",
  latitude: "",
  longitudes: "",
});





const submit = () => {
  form.post(route("contracts.store"));
};

const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Crea un nuevo contrato
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="user_id" value="ID del Usuario" />
        <TextInput
          id="user_id"
          v-model="form.user_id"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="user_id"
        />
        <InputError class="mt-2" :message="form.errors.user_id" />
      </div>

      <div class="mt-4">
        <InputLabel for="plan_id" value="ID del plan" />
        <textarea
          id="plan_id"
          v-model="form.plan_id"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="plan_id"
          style="height: 250px; resize: none; border-radius: 1.5%;"
        />
        <InputError class="mt-2" :message="form.errors.plan_id" />
      </div>

      <div class="mt-4">
        <InputLabel for="address" value="Dirección" />
        <textarea
          id="address"
          v-model="form.address"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="address"
          style="height: 250px; resize: none; border-radius: 1.5%;"
        />
        <InputError class="mt-2" :message="form.errors.address" />
      </div>

      <div class="flex justify-between">
        <div class="mt-4">
          <InputLabel for="latitude" value="Latitud" />
          <textarea
            id="latitude"
            v-model="form.latitude"
            type="text"
            class="mt-1 block w-full"
            required
            autocomplete="latitude"
            style="height: 250px; resize: none; border-radius: 1.5%;"
          />
          <InputError class="mt-2" :message="form.errors.latitude" />
        </div>
        <div class="mt-4">
          <InputLabel for="longitude" value="Longitud" />
          <textarea
            id="longitude"
            v-model="form.longitude"
            type="text"
            class="mt-1 block w-full"
            required
            autocomplete="longitude"
            style="height: 250px; resize: none; border-radius: 1.5%;"
          />
          <InputError class="mt-2" :message="form.errors.longitude" />
        </div>

      </div>

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

          Enviar Contrato
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
