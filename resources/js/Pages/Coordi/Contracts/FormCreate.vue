<script setup>
import { router, useForm } from "@inertiajs/vue3";
import {ref, onMounted} from 'vue';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  user_id: "",
  plan_id: "",
  address: "",
  geolocation:{
    latitude: "",
    longitude:"",
  },
});

const handlePositionClicked = (position) => {
  form.geolocation.latitude = position.lat.toFixed(6); // Asignar la latitud con precisión
  form.geolocation.longitude = position.lng.toFixed(6); // Asignar la longitud con precisión
};
const lat = ref(null);
const lng = ref(null); 
onMounted(() => {
  if(navigator.geolocation){
    var success = function(position){
      lat.value = form.geolocation.latitude = position.coords.latitude.toFixed(6),
      lng.value = form.geolocation.longitude = position.coords.longitude.toFixed(6);
    }

    navigator.geolocation.getCurrentPosition(success, function(msg)
    {
    console.error( msg );
    });
  }
});
const getCurrentLocation = () =>
{
   form.geolocation.latitude = lat,
   form.geolocation.longitude = lng;
}
const submit = () => {
  form.post(route("contracts.store"));
};

</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Crea un nuevo contrato
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="user_id" value="ID del Usuario" />
        <TextInput
          id="user_id"
          v-model="form.user_id"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="user_id"
        />
        <InputError class="mt-2" :message="form.errors.user_id" />
      </div>

      <div class="mt-4">
        <InputLabel for="plan_id" value="ID del plan" />
        <TextInput
          id="plan_id"
          v-model="form.plan_id"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="plan_id"
        />
        <InputError class="mt-2" :message="form.errors.plan_id" />
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
  props: ["contract"],
  data() {
    return {
      ubicacionManual: false,
      form: {
        latitude: '',
        longitude: ''
      }
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
