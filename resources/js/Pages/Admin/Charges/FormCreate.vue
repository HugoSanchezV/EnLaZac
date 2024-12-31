<script setup>
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { useToast, TYPE, POSITION } from "vue-toastification";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  contracts: Array,
  
});
const customDescription = ref('');

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
    if(form.description == 'custom'){ form.description = customDescription.value}

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
                  {{ "Contracto: "+contract.id + " - Usuario: " + (contract.inventorie_device?.device?.user?.name || "Sin asignar")  + " - Plan: "+ (contract?.plan?.name || "Sin asignar") }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.contract_id" />
      </div>

      <div class="mt-4">
          <InputLabel for="description" value="Descripción" />

          <!-- Select para elegir una descripción -->
          <select
              v-model="form.description"
              @change="handleDescriptionChange"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
              <option disabled value="">Selecciona la descripción</option>
              <option value="fuera-corte">No pagó antes del día de corte</option>
              <option value="recargo-mes">Recargo del mes</option>
              <option value="renta-dispositivo">Renta del dispositivo</option>
              <option value="instalacion-inicial">Instalación inicial</option>
              <option value="cambio-domicilio">Cambio de domicilio</option>
              <option value="custom">Otra descripción</option>
          </select>

          <!-- Campo de texto que aparece solo si se elige "Otra descripción" -->
          <textarea
              v-if="form.description === 'custom'"
              id="description"
              v-model="customDescription"
              class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
              placeholder="Escribe una descripción personalizada"
              style="resize: none; border-radius: 1.5%;"
          ></textarea>

          <InputError class="mt-2" :message="form.errors.description" />
      </div>



      <div class="mt-4">
          <InputLabel for="amount" value="Monto" />
          <TextInput
            id="amount"
            v-model="form.amount"
            type="number"
            min="0"
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