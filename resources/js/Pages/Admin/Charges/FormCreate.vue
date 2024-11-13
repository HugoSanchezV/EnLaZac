<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  contracts: Array,
  
});

const form = useForm({
  contract_id: "",
  description: "",
  amount: 0, 
  paid:"",
  date_paid:"", 
});
const alert_insert_date = (dataMessage) =>{
  const toast = useToast();
  toast(
    {
      component: BaseQuestion,
      props: {
        message: dataMessage,
        accept: true,
        cancel: true,
        textConfirm: "Confirmar",
      },

      listeners: {
        accept: () => {
          form.post(route("charges.store"));
          //console.log('Realiza el registro desde el BaseQuestion');
          
        },
      },
    },

    {
      type: TYPE.WARNING,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
}
const date_actived = ()=>{
  if(form.date_paid.length == 0)
  {
    alert_insert_date("¿Desea confirmar el pago sin ingresar la fecha de pago?");
    return false;
  }
  return true;

}

const paid = () =>{
  var miCheckbox = document.getElementById('activated');
  if (miCheckbox.checked) {
    form.paid = true;
    return date_actived();

  } else {
    form.paid = false;
    if(form.date_paid.length != 0)
    {
      alert_insert_date("¿Desea insertar la fecha de pago sin confirmar el pago");
      return false;
    }else{
      return true;
    }
  }
}

const submit = () => {
  if(paid()){
    form.post(route("charges.store"));
  }
};


</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Crea un nuevo cargo
    </h2>
  </div>
  
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="contract_id" value="ID del contrato" />
        <div class="mt-2">
            <select
              v-model="form.contract_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option :value="null" selected>Selecciona una opción</option>
              <option v-for="contract in contracts" :key="contract.id" :value="contract.id">
                  {{ "Contracto: "+contract.id + " - Disp.: " + contract.device.address  + " - Plan: "+ contract.plan.name }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.contract_id" />
      </div>

      <div class="mt-4">
        <InputLabel for="description" value="Descripción" />
        <textarea
          id="description"
          v-model="form.description"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="description"
          style="resize: none; border-radius: 1.5%;"
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>

      <div class="mt-4">
          <InputLabel for="amount" value="Monto" />
          <TextInput
            id="amount"
            v-model="form.amount"
            type="number"
            class="mt-1 block w-full"
            autofocus
            autocomplete="amount"
          />
          <InputError class="mt-2" :message="form.errors.amount" />
      </div>
      <div class="mt-4 flex gap-4">
        <InputLabel for="active" value="¿Cargo pagado?" />
        <label class="switch">
          <input type="checkbox" id="activated"/>
          <span class="slider round"></span>
        </label>

      </div>
      
      <div class="mt-4">
          <InputLabel for="date_paid" value="Fecha de Pago" />
          <TextInput
            id="date_paid"
            v-model="form.date_paid"
            type="date"
            class="mt-1 block w-full"
            autofocus
            autocomplete="date_paid"
          />
          <InputError class="mt-2" :message="form.errors.date_paid" />
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

          Confirmar cargo
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>