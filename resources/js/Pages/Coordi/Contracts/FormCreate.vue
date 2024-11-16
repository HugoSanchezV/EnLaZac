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
  }
});

//const selectedUser = ref(null);

const form = useForm({
  device_id: "",
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
  form.geolocation.latitude = position.lat.toFixed(10); // Asignar la latitud con precisión
  form.geolocation.longitude = position.lng.toFixed(10); // Asignar la longitud con precisión
};
const lat = ref(null);
const lng = ref(null); 
const getPosition = () =>{
  if(navigator.geolocation){
    var success = function(position){
      lat.value = form.geolocation.latitude = position.coords.latitude.toFixed(10),
      lng.value = form.geolocation.longitude = position.coords.longitude.toFixed(10);
    }
  
    navigator.geolocation.getCurrentPosition(success, function(msg)
    {
    console.error( msg );
    });
  }
}
onMounted(() => {
  getPosition();
  // Obtener el input de fecha
  // const datePicker = document.getElementById('start_date');

  // Inicializar el valor con el día 5 del mes actual
  const today = new Date();
  //alert(today);
  form.start_date = setDayToFive(today);
  onDateChange();

  // // Escuchar los cambios del input
  // datePicker.addEventListener('change', function(event) {
  //     // Obtener la fecha seleccionada por el usuario
  //     const selectedDate = new Date(event.target.value);
  //     // Forzar el día 5 en la fecha seleccionada
  //     event.target.value = setDayToFive(selectedDate);});

  //  console.log(form.start_date);

});
const getCurrentLocation = () =>
{
   form.geolocation.latitude = lat,
   form.geolocation.longitude = lng;
    getPosition();
}
const onDateChange= () =>{
  // console.log("ENTRA");
  // Imprimir la fecha seleccionada en la consola
  const date = new Date(form.start_date);
  date.setMonth(date.getMonth() + 1);
  form.end_date = date.toISOString().split('T')[0];
  }

const showWarning = (id) => {
  const toast = useToast();

  toast(
    {
      component: BaseQuestion,
      props: {
        message: "Seleccione el Id de los campos correspondientes",
      },

      listeners: {
      },
    },

    {
      type: TYPE.ERROR,
      position: POSITION.TOP_CENTER,
      timeout: 10000,
    }
  );
};

const submit = () => {
  var miCheckbox = document.getElementById('activated');
  if (miCheckbox.checked) {
    form.active = true;
  } else {
    form.active = false;
  }
  if(form.device_id == "" || form.plan_id == "" || form.rural_community_id == "")
  {
    showWarning();
  }else{
    console.log(form);
    form.post(route("contracts.store"));
  }

};


 // Función para siempre seleccionar el día 5 del mes
 function setDayToFive(date) {
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0'); // Mes en formato 2 dígitos
  const day = '05'; // Forzar el día 5
  return `${year}-${month}-${day}`;
}
</script>

<template>
  <div class="mt-5">
    {{ devices }}
    <form @submit.prevent="submit" class="border p-7 m-5 bg-white">
      <div>
        <InputLabel for="device_id" value="ID del dispositivo" />
        <div class="mt-2">
            <select
              v-model="form.device_id"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option v-if="devices.length === 0" disabled value="">No hay dispositivos con usuario</option>
              <option v-else value="" disabled>Selecciona una opción</option>
              <option v-for="device in devices" :key="device.id" :value="device.id">
                  {{ device.id + " - " + device.mac_address }}
              </option>
            </select>
        </div>
        <InputError class="mt-2" :message="form.errors.device_id" />
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
          <TextInput
            id="start_date"
            v-model="form.start_date"
            type="date"
            class="mt-1 block w-full"
            required
            autofocus
            autocomplete="start_date"
            @input="onDateChange"
          />
          <InputError class="mt-2" :message="form.errors.start_date" />
        </div>
        <div>
          <InputLabel for="end_date" value="Fecha de Terminación" />
          <TextInput
            id="end_date"
            v-model="form.end_date"
            type="date"
            class="mt-1 block w-full"
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
        :lat="parseInt(lat)"
        :lng="parseInt(lng)"
        :clic=true
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
