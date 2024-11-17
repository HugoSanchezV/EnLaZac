<script setup>
import { onMounted } from "vue";

import { useToast, TYPE, POSITION } from "vue-toastification";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import flatpickr from "flatpickr";
import BaseQuestion from "@/Components/Base/BaseQuestion.vue";


const props = defineProps({
  cutoffday: Number,
  exemptionPeriod: Object,
});

const formCutOff = useForm({
  day: "0",
});
const formExemptionPeriod = useForm({
  key: "",
  start_day: "1",
  end_day: "1",
  month_next: "",
  month_after_next: "",
});
onMounted(() => {
  if (props.cutoffday) {
    formCutOff.day = props.cutoffday || "";
    
  }
  if (props.exemptionPeriod) {
    formExemptionPeriod.start_day = props.exemptionPeriod.start_day || "",
    formExemptionPeriod.end_day = props.exemptionPeriod.end_day || "",
    formExemptionPeriod.month_next = props.exemptionPeriod.month_next || "",
    formExemptionPeriod.month_after_next = props.exemptionPeriod.month_after_next || "";
  }

        flatpickr("#end_day", {
    dateFormat: "j", // Solo muestra el día
    defaultDate: [
        new Date(new Date().getFullYear(), new Date().getMonth(), formExemptionPeriod.start_day), // Día de inicio predeterminado
        new Date(new Date().getFullYear(), new Date().getMonth(), formExemptionPeriod.end_day),   // Día de fin predeterminado
    ],
    minDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1), // Primer día del mes actual
    maxDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0), // Último día del mes actual
    mode: "range", // Permitir rango de días
    disableMobile: true, // Forzar el comportamiento de escritorio en móviles
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length === 2) {
            const [startDate, endDate] = selectedDates;
            formExemptionPeriod.start_day = startDate.getDate();
            formExemptionPeriod.end_day = endDate.getDate();
        } else {
            console.log("Selecciona el rango completo");
        }
    },
});
});
const submitCutOffDay = () => {
  formCutOff.put(route("settings.service.variable.update.cuttoffday"));
  
};
const warningStartDay = () => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "Los dias deben ser en fechas intermedias",
        textConfirm: "",
      },
    },

    {
      type: TYPE.ERROR,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
};

const submitExemptionPeriod = () => {
  if(formExemptionPeriod.start_day == 1 
  || formExemptionPeriod.start_day > 28
  || formExemptionPeriod > 28){
    warningStartDay();
  }else{
    formExemptionPeriod.put(route("settings.service.variable.exemptionperiod"));
  }
  
};

</script>


<template>
  <div class="mt-5">
    <form @submit.prevent="submitCutOffDay" class="border p-14 m-5 bg-white">
      <div>
        <h2>Día de corte: </h2>
        <div class="mt-2">
          <InputLabel for="period" value="Selecciona del dia de corte" />
          <TextInput 
          type="number" 
          class="mt-1 block w-full"
         
          v-model="formCutOff.day" 
          required
          placeholder="Selecciona un día" />
          <InputError class="mt-2" :message="formCutOff.errors.day" />
        </div>
      </div>
      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': formCutOff.processing }"
          :disabled="formCutOff.processing"
        >
          Guardar Cambios
        </PrimaryButton>
      </div>
    </form>
  </div>

  <div class="mt-3">
    <form @submit.prevent="submitExemptionPeriod" class="border p-14 m-5 bg-white">
      <div>
        <h2>Cobro después de la instalación</h2>
        <div class="mt-2">
          <InputLabel for="" value="Selecciona un rango de días" />
          <input 
          class="flatpickr mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
          type="text" 
          id="end_day"
          required
          placeholder="Selecciona un día" />
          <InputError class="mt-2" :message="formExemptionPeriod.errors.start_day" />
          <InputError class="mt-2" :message="formExemptionPeriod.errors.end_day" />
        </div>    

        <div class="mt-3">
          <InputLabel for="month_next" value="Mes/es dentro del rango" />
          <TextInput 
          id="subject"
          v-model="formExemptionPeriod.month_next"
          type="number"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="subject" 
          placeholder="Selecciona un día" />
          <InputError class="mt-2" :message="formExemptionPeriod.errors.month_next" />
        </div>    
        <div class="mt-2">
          <InputLabel for="month_after_next" value="Mes/es fuera del rango" />
          <TextInput 
          id="subject"
          v-model="formExemptionPeriod.month_after_next"
          type="number"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="subject" 
          placeholder="Selecciona un día" />
          <InputError class="mt-2" :message="formExemptionPeriod.errors.month_after_next" />
        </div>    
      </div>
      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': formExemptionPeriod.processing }"
          :disabled="formExemptionPeriod.processing"
        >
          Guardar Cambios
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