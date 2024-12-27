<script setup>
import { router, useForm } from "@inertiajs/vue3";
import {ref, onMounted, toRefs} from 'vue';
import { useToast, TYPE, POSITION } from "vue-toastification";

import BaseQuestion from "@/Components/Base/BaseQuestion.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import flatpickr from "flatpickr";

import { monthSelectPlugin } from "flatpickr/dist/plugins/monthSelect";
import monthSelect from 'flatpickr/dist/plugins/monthSelect';
import "flatpickr/dist/plugins/monthSelect/style.css";


const props = defineProps({
  lastID:{
    type: Object
  },
  devices: {
    type: Array,
    default: () => [],
  },
  plans: {
    type: Array,
    default: () => [],
  },
  community:{
    type: Array,
    default: () => [],
  },
  mapKey:{
    type: String,
    required: true,
  }
});

//const selectedUser = ref(null);

const form = useForm({
  inv_device_id: "",
  plan_id: "",
  start_date: "",
  end_date: "",
  active: "",
  address: "",
  rural_community_id: "",
  geolocation:{
    latitude: "",
    longitude:"",
  },
});

const handlePositionClicked = (position) => {
  form.geolocation.latitude = position.lat.toFixed(15); // Asignar la latitud con precisión
  form.geolocation.longitude = position.lng.toFixed(15); // Asignar la longitud con precisión
};
const lat = ref(null);
const lng = ref(null); 
const getPosition = () =>{
  if(navigator.geolocation){
    var success = function(position){
      lat.value = form.geolocation.latitude = position.coords.latitude.toFixed(15),
      lng.value = form.geolocation.longitude = position.coords.longitude.toFixed(15);

     // alert("Latitude: "+lat.value+", Longitude: "+lng.value);
    }
  
    navigator.geolocation.getCurrentPosition(success, function(msg)
    {
    console.error( msg );
    });
  }
}
function addMonth(dateString) {
  const [year, month] = dateString.split("-").map(Number); // Divide "2024-11" en año y mes
  const date = new Date(year, month - 1); // Crea un objeto Date (mes indexado desde 0)

  date.setMonth(date.getMonth() + 1); // Sumar un mes

  // Formatea la nueva fecha a "Y-m"
  const newYear = date.getFullYear();
  const newMonth = String(date.getMonth() + 1).padStart(2, "0"); // Mes con dos dígitos

  return `${newYear}-${newMonth}`;
}
onMounted(() => {
  getPosition();
  // Obtener el input de fecha
  // const datePicker = document.getElementById('start_date');

  // Inicializar el valor con el día 5 del mes actual
  const today = new Date();
  //alert(today);

  flatpickr("#start_date", {
      plugins: [
      monthSelect({
              shorthand: true, // Usa nombres cortos de meses (ej: "Jan" en lugar de "January")
              dateFormat: "Y-m", // Formato de valor en el input
              altFormat: "F Y", // Formato de valor alternativo mostrado
              theme: "dark" // Tema oscuro
          })
      ],
      defaultDate: form.start_date || null, // Establece un valor predeterminado si existe
      onChange: function (selectedDates, dateStr) {
       // alert(dateStr);
        form.start_date = dateStr; // Asigna el valor al formato "m.y"
        form.end_date = addMonth(form.start_date);
      //  console.log("Fecha seleccionada:", form.start_date.value);
      },
  });
  flatpickr("#end_date", {
      plugins: [
      monthSelect({
              shorthand: true, // Usa nombres cortos de meses (ej: "Jan" en lugar de "January")
              dateFormat: "Y-m", // Formato de valor en el input
              altFormat: "F Y", // Formato de valor alternativo mostrado
              theme: "dark" // Tema oscuro
          })
      ]
  });
  // form.start_date = setDayToFive(today);
  // onDateChange();


});
const getCurrentLocation = () =>
{
  form.geolocation.latitude = lat,
  form.geolocation.longitude = lng;
  getPosition();
}
// const onDateChange= () =>{
//   // console.log("ENTRA");
//   // Imprimir la fecha seleccionada en la consola
//   const date = new Date(form.start_date);
//   date.setMonth(date.getMonth() + 1);
//   form.end_date = date.toISOString().split('T')[0];
//   }

const submit = () => {
  var miCheckbox = document.getElementById('activated');
  if (miCheckbox.checked) {
    form.active = true;
  } else {
    form.active = false;
  }

    console.log(form);
    form.post(route("contracts.store"));
};

</script>

<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-7 m-5 bg-white">
      <div>
        <InputLabel for="inv_device_id" value="ID del dispositivo" />
        <div class="mt-2">
            <select
              v-model="form.inv_device_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option v-if="devices.length === 0" disabled value="">No hay dispositivos con usuario</option>
              <option v-else value="" disabled>Selecciona una opción</option>
              <option v-for="device in devices" :key="device.id" :value="device.id">
                  {{ device.id + " - " + device.mac_address }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.inv_device_id" />
      </div>

      <div class="mt-4">
        <InputLabel for="plan_id" value="ID del plan" />
        <div class="mt-2">
            <select
              v-model="form.plan_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option disabled v-if="plans.length === 0" value="">No hay registro de comunidades</option>
              <option  disabled v-else value="">Selecciona una opción</option>
              <option v-for="plan in plans" :key="plan.id" :value="plan.id">
                  {{ plan.id + " - " + plan.name }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.plan_id" />
      </div>
      <div class="mt-4">
        <InputLabel for="rural_community_id" value="ID de la comunidad" />
        <div class="mt-2">
            <select
              v-model="form.rural_community_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
            <option disabled v-if="community.length === 0" value="">No hay registro de comunidades</option>
            <option disabled v-else value="">Selecciona una opción</option>
            <option v-for="com in community" :key="com.id" :value="com.id">
                  {{com.id+" - " + com.name}}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.rural_community_id" />
      </div>
      
      <div class="mt-4 flex justify-between">
        <div>
          <InputLabel for="start_date" value="Fecha de Inicio" />
          <input
            id="start_date"
            v-model="form.start_date"
            type="text"
            class="flatpickr mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
            required
            autofocus
            autocomplete="start_date"
            @input="onDateChange"
          />
          
          <InputError class="mt-2" :message="form.errors.start_date" />
        </div>
        <div>
          <InputLabel for="end_date" value="Fecha de Terminación" />
          <input
            id="end_date"
            v-model="form.end_date"
            type="text"
            class="flatpickr mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" 
            required
            autofocus
            autocomplete="end_date"
          />
          <InputError class="mt-2" :message="form.errors.end_date" />
        </div>
          
      </div>

      <div class="mt-4 flex gap-4">
        <InputLabel for="active" value="¿Contrato Activo?" />
        
        <label class="switch">
          <input type="checkbox" id="activated"/>
          <span class="slider round"></span>
        </label>

      </div>

      <div class="mt-4">
        <InputLabel for="address" value="Dirección" />
        <TextInput
          id="address"
          v-model="form.address"
          type="text"
          class="mt-1 block w-full"
          required
          autocomplete="address"
          autofocus
        />
        <InputError class="mt-2" :message="form.errors.address" />
      </div>

      <div class="mt-4">
        <p>Su ubicación actual será tomada de manera automática 
          para obtener la locación del cliente o ingrese la ubicación manualmente. 
        </p>
      </div>
      <div class="flex justify-between items-center gap-2 mt-5">
        <p>Ingresar ubicación manualmente</p>
        <label class="switch">
          <input type="checkbox" 
          @change="getCurrentLocation"  
          checked 
          v-model="ubicacionManual" />
          <span class="slider round"></span>
        </label>
      </div>

      <div v-if="ubicacionManual" class="flex gap-2">
        <div class="mt-4">
          <InputLabel for="latitude" value="Latitude" />
          <TextInput
            id="latitude"
            v-model="form.geolocation.latitude"
            readonly 
            class="mt-1 block w-full"
            autocomplete="latitude"
          />
          <InputError class="mt-2" :message="form.errors.latitude" />
        </div>

        <div class="mt-4">
          <InputLabel for="longitude" value="Longitude" />
          <TextInput
            id="longitude"
            v-model="form.geolocation.longitude"
            readonly 
            class="mt-1 block w-full"
            autocomplete="longitude"
          />
          <InputError class="mt-2" :message="form.errors.longitude" />
        </div>
      </div>
      <div v-if="ubicacionManual" class="flex mt-4">
        <GoogleMaps
        :lat="parseFloat(lat)"
        :lng="parseFloat(lng)"
        :clic=true
        :mapKey="mapKey"
        @otherPos_clicked="handlePositionClicked" />
      </div>
      
      <div v-else>
        <div class="mt-4">
          <TextInput
            id="latitude"
            v-model="form.geolocation.latitude"
            type="hidden"
            class="mt-1 block w-full"
            autocomplete="latitude"
          />
          <TextInput
            id="longitude"
            v-model="form.geolocation.longitude"
            type="hidden"
            class="mt-1 block w-full"
            autocomplete="longitude"
          />
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
<script>
import GoogleMaps from '@/Components/GoogleMaps.vue'
export default {
  components: {
        GoogleMaps
    },
  props: 
    ["contract"],

  data() {
    return {
      ubicacionManual: false,
      form: {
        latitude: '',
        longitude: '',
      },

    };
  },
};

</script>
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
