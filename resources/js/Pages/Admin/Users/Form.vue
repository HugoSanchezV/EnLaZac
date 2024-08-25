<script setup>
import { router, useForm} from "@inertiajs/vue3";
import {ref, onMounted} from 'vue';
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";



const form = useForm({
  name: "",
  alias: "",
  email: "",
  password: "",
  password_confirmation: "",
  coordinates:{
    latitude: "",
    longitude:"",
  },
  admin: 0,
});

const submit = () => {
  form.post(route("usuarios.store"), {
    onFinish: () => form.reset("password", "password_confirmation"),
    onSuccess: () => {
        router.back()
    }
  });
};

const handlePositionClicked = (position) => {
  form.coordinates.latitude = position.lat.toFixed(6); // Asignar la latitud con precisión
  form.coordinates.longitude = position.lng.toFixed(6); // Asignar la longitud con precisión
};
const lat = ref(null);
const lng = ref(null); 
onMounted(() => {
  if(navigator.geolocation){
    var success = function(position){
      lat.value = form.coordinates.latitude = position.coords.latitude.toFixed(6),
      lng.value = form.coordinates.longitude = position.coords.longitude.toFixed(6);
    }

    navigator.geolocation.getCurrentPosition(success, function(msg)
    {
    console.error( msg );
    });
  }
});
const getCurrentLocation = () =>
{
   form.coordinates.latitude = lat,
   form.coordinates.longitude = lng;
}
const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Selecciona el tipo de usuario que deseas crear
    </h2>

    <div class="flex justify-center mt-5">
      <button
        @click="seleccionar(0)"
        :class="{
          'border py-2 px-3 rounded-l-md hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 0,
        }"
      >
        Cliente
      </button>
      <button
        @click="seleccionar(2)"
        :class="{
          'border py-2 px-3 hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 2,
        }"
      >
        Coordinador
      </button>
      <button
        @click="seleccionar(3)"
        :class="{
          'border py-2 px-3 rounded-r-md hover:bg-slate-200': true,
          'bg-blue-500 hover:bg-blue-500 text-white': form.admin === 3,
        }"
      >
        Técnico
      </button>
    </div>
  </div>

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
        <InputLabel for="alias" value="Alias" />
        <TextInput
          id="alias"
          v-model="form.alias"
          type="text"
          class="mt-1 block w-full"
          autocomplete="alias"
        />
      </div>

      <div class="mt-4">
        <InputLabel for="email" value="Email" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Password" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4">
        <InputLabel for="password_confirmation" value="Confirmar Password" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>
      <div class="mt-4">
        <p>Su ubicación actual será tomada de manera automática 
          o ingresela manualmente
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
            v-model="form.coordinates.latitude"
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
            v-model="form.coordinates.longitude"
            readonly 
            class="mt-1 block w-full"
            autocomplete="longitude"
          />
          <InputError class="mt-2" :message="form.errors.longitude" />
        </div>

      </div>
      <div v-if="ubicacionManual" class="flex mt-4">
        <GoogleMaps
        :lat="lat"
        :lng="lng"
         @otherPos_clicked="handlePositionClicked" />
      </div>
      
      <div v-else>
        <div class="mt-4">
          <TextInput
            id="latitude"
            v-model="form.coordinates.latitude"
            type="hidden"
            class="mt-1 block w-full"
            autocomplete="latitude"
          />
          <TextInput
            id="longitude"
            v-model="form.coordinates.longitude"
            type="hidden"
            class="mt-1 block w-full"
            autocomplete="longitude"
          />
      </div>
      </div>
      <input type="hidden" id="admin" :value="form.admin" readonly />

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Registrar
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
  props: ["user"],
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