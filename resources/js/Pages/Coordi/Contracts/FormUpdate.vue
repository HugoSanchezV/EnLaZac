<script setup>
import { onMounted, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  contract: Object,
});

const form = useForm({
  user_id: "0",
  plan_id: "0",
  start_date: "",
  end_date: "",
  active: "0",
  address: "",
  geolocation:{
    latitude: "",
    longitude:"",
  },
});
const lat = ref(null);
const lng = ref(null); 
onMounted(() => {
  if (props.contract) {
    form.user_id = props.contract.user_id || "0";
    form.plan_id = props.contract.plan_id || "0";
    form.address = props.contract.address || "";
    form.start_date = props.contract.start_date || "";
    form.end_date = props.contract.end_date || "";
    form.active = props.contract.active || "0";
    lat.value = form.geolocation.latitude = props.contract.geolocation.latitude ||  "0";
    lng.value = form.geolocation.longitude = props.contract.geolocation.longitude || "0";
  }

  if(form.active == true){
    document.getElementById('activated').checked = true;
  }
});

const updateStatus = () =>{
  if (document.getElementById('activated').checked) {
    form.active = true;
    console.log(form.active);
  } else {
    form.active = false;
    console.log(form.active);
  }

}
const handlePositionClicked = (position) => {
  form.geolocation.latitude = position.lat.toFixed(6); // Asignar la latitud con precisión
  form.geolocation.longitude = position.lng.toFixed(6); // Asignar la longitud con precisión
};

const submit = () => {
  form.put(route("contracts.update", { id: props.contract.id }));
};

</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Actualizar contrato
    </h2>

  </div>
  <div class="mt-5 pl-5 pr-5">
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
          <input type="checkbox" id="activated" 
          @click="updateStatus"/>
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
        <p>Ubicación
        </p>
      </div>

      <div class="flex gap-2">
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
      <div class="flex mt-4">
        <GoogleMaps
        :lat="parseInt(lat)"
        :lng="parseInt(lng)"
        :clic=true
         @otherPos_clicked="handlePositionClicked" />
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


<script>
import GoogleMaps from '@/Components/GoogleMaps.vue'
export default {
  props: ["contract"],
  components: {
        GoogleMaps
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