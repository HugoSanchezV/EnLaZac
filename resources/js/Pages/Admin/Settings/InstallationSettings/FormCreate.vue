<script setup>
import { router, useForm } from "@inertiajs/vue3";
import {ref, onMounted, toRefs} from 'vue';
import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";


const props = defineProps({
  installations: {
    type: Array,

  },
});

const form = useForm({
  installation_id: "",
  exemption_months: "",
});

const submit = () => {
  //alert(form.installation_id);
  form.post(route("settings.installation.store"));
  };
</script>

<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-7 m-5 bg-white">
      <div>
        <InputLabel for="installation_id" value="ID de la instalación" />
        <div class="mt-2">
            <select
              v-model="form.installation_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option v-if="installations.length === 0" disabled value="">No hay registro de instalaciones disponibles</option>
              <option v-else value="" disabled>Selecciona una opción</option>
              <option v-for="installation in installations" :key="installation.id" :value="installation.id">
                  {{ installation.id + " - "+ installation.contract.inventorie_device.device.user.name}}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.installation_id" />
      </div>

      <div class="mt-2 ">
          <InputLabel for="exemption_months" value="Mes/es asignado" />
          <TextInput
            id="exemption_months"
            v-model="form.exemption_months"
            type="number"
            class="mt-1 block w-full"
            max="12"
            min="0"
            autofocus
            autocomplete="exemption_months"
            @input="onDateChange"
          />
          <InputError class="mt-2" :message="form.errors.exemption_months" />
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

          Guardar Configuracion
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
