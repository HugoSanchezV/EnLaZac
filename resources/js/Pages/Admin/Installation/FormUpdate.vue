<script setup>
import { onMounted, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import GoogleMaps from "@/Components/GoogleMaps.vue";

const props = defineProps({
  installation: Object,
  contracts: Array,
});

const form = useForm({
  contract_id: "0",
  description: "0",
  assigned_date: "",
});

onMounted(() => {
  if (props.installation) {
    form.contract_id = props.installation.contract_id || "";
    form.description = props.installation.description || "";
    form.assigned_date = props.installation.assigned_date || "";
  }
});

const submit = () => {
  form.put(route("installation.update", { id: props.installation }));
};
</script>


<template>
  <div class="mt-5 pl-5 pr-5">
    <form @submit.prevent="submit" class="border p-7 m-5 bg-white">
      <div>
        <InputLabel for="contract_id" value="ID del Usuario" />
        <div class="mt-2">
          <select
            v-model="form.contract_id"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option v-if="contracts.length === 0" disabled value="">
              No hay registro de usuarios
            </option>
            <option v-else value="" disabled>Selecciona una opción</option>
            <option
              v-for="contract in contracts"
              :key="contract.id"
              :value="contract.id"
            >
              {{
                contract.id +
                " - " +
                contract.inventorieDevice?.device?.user?.name
              }}
            </option>
          </select>
        </div>
        <InputError class="mt-2" :message="form.errors.contract_id" />
      </div>

      <div class="mt-2">
        <InputLabel for="description" value="Descripción" />
        <div class="mt-2">
          <select
            v-model="form.description"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option disabled value="">Selecciona la descripción</option>
            <option :value="1">Instalación en el domicilio</option>
            <option :value="2">Cambio de domicilio</option>
          </select>
        </div>
        <InputError class="mt-2" :message="form.errors.description" />
      </div>

      <div class="mt-2">
        <InputLabel for="assigned_date" value="Fecha Asignada" />
        <TextInput
          id="assigned_date"
          v-model="form.assigned_date"
          type="date"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="assigned_date"
          @input="onDateChange"
        />
        <InputError class="mt-2" :message="form.errors.assigned_date" />
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