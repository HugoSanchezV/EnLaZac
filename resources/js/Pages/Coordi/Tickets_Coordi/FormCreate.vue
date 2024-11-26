<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  users: {
    type: Array,
    default: () => [],
  },

  technicals: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  subject: "",
  description: "",
  user_id: "Selecciona una opción",
  technical_id: "Selecciona una opción",
});

const submit = () => {
  if (form.user_id === "Selecciona una opción") {
    form.user_id = null;
  }

  if (form.technical_id === "Selecciona una opción") {
    form.technical_id = null;
  }
  form.post(route("tickets.store"));
};

const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">Crea un nuevo ticket de soporte técnico</h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div v-if="$page.props.auth.user.admin == 1">
        <div v-if="users.length !== 0">
          <InputLabel for="user_id" value="Usuario" />
          <select
            v-model="form.user_id"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="Selecciona una opción" selected>
              Selecciona una opción
            </option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.id + " - " + user.name }}
            </option>
          </select>
        </div>
        <div v-else class="mt-4">
          <span class="bg-yellow-100 p-2 justify-center flex rounded-md"
            >No tienes usuarios para asignar</span
          >
        </div>

        <div class="mt-4" v-if="technicals.length !== 0">
          <InputLabel for="technical_id" value="Técnico encargado" />
          <select
            v-model="form.technical_id"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="Selecciona una opción" selected>
              Selecciona una opción
            </option>
            <option
              v-for="technical in technicals"
              :key="technical.id"
              :value="technical.id"
            >
              {{ technical.id + " - " + technical.name }}
            </option>
          </select>
        </div>
        <div v-else class="mt-4">
          <span class="bg-yellow-100 p-2 justify-center flex rounded-md"
            >No tienes técnicos para asignar la tarea</span
          >
        </div>
      </div>
      <div class="mt-4">
        <InputLabel for="subject" value="Asunto" />
        <TextInput
          id="subject"
          v-model="form.subject"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="subject"
        />
        <InputError class="mt-2" :message="form.errors.subject" />
      </div>

      <div class="mt-4">
        <InputLabel for="description" value="Descripción" />
        <textarea
          id="description"
          v-model="form.description"
          type="text"
          class="mt-1 block w-full border-gray-300"
          required
          autocomplete="description"
          style="height: 250px; resize: none; border-radius: 1.5%"
        />
        <InputError class="mt-2" :message="form.errors.description" />
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

          Enviar Ticket
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
