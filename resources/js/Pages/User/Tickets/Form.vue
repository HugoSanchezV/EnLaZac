<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  number: "",
  description: "",
  ubication: "",
  create_date: "",
  status: "",
  user_id: "",
});

const submit = () => {
  form.post(route("tickets.store"), {
    onFinish: () => form.reset("password", "password_confirmation"),
    onSuccess: () => {
        router.back()
    }
  });
};

const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Selecciona el tipo de usuario que deseas crear
    </h2>

    <div class="flex justify-center mt-5">
      <button
        @click="seleccionar(0)"
        :class="{
          'border py-2 px-3 rounded-l-md hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 0,
        }"
      >
        Cliente
      </button>
      <button
        @click="seleccionar(2)"
        :class="{
          'border py-2 px-3 hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 2,
        }"
      >
        Coordinador
      </button>
      <button
        @click="seleccionar(3)"
        :class="{
          'border py-2 px-3 rounded-r-md hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 3,
        }"
      >
        Técnico
      </button>
    </div>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="name" value="Nombre" />
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

      <div class="mt-4">
        <InputLabel for="alias" value="Alias" />
        <TextInput
          id="alias"
          v-model="form.alias"
          type="text"
          class="mt-1 block w-full"
          autocomplete="alias"
        />
      </div>

      <div class="mt-4">
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4">
        <InputLabel for="password_confirmation" value="Confirmar Password" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <input type="hidden" id="admin" :value="form.admin" readonly />

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
