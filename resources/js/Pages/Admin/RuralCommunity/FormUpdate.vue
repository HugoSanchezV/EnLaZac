<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  community: Object,
  contracts: Array,
});

const form = useForm({
  name: "",
  installation_cost: "",
  contract_id: "",
});

onMounted(() => {
  if (props.community) {
    form.name = props.community.name || "";
    form.installation_cost = props.community.installation_cost || "";
    form.contract_id = props.community.contract_id || "";
  }
});
const submit = () => {
  form.put(route("rural-community.update", { id: props.community.id }));
};
</script>


<template>
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
        <InputLabel for="installation_cost" value="Costo de Instalación" />
        <TextInput
          id="installation_cost"
          v-model="form.installation_cost"
          type="text"
          class="mt-1 block w-full"
          autocomplete="installation_cost"
        />
        <InputError class="mt-2" :message="form.errors.installation_cost" />
      </div>

      <div class="mt-4">
        <InputLabel for="contract_id" value="ID del contrato" />
        <div class="mt-2">
            <select
              v-model="form.contract_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option :value="null" selected>Selecciona una opción</option>
              <option v-for="contract in contracts" :key="contract.id" :value="contract.id">
                  {{ "Contracto: "+contract.id + " - Usuario: " + contract.user.name  + " - Plan: "+ contract.plan.name }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.user_id" />
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
