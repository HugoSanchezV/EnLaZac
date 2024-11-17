<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  task: Object,
  title: String,
});

const form = useForm({
  name: "",
  period: "",
  status: "",
});


onMounted(() => {
  if (props.task) {
    form.name = props.task.task_name || "";
    form.period = props.task.period || "";
    form.status = props.task.status || ""
  }
  if(form.status == true)
  {
    document.getElementById('activated').checked = true;
  }else{
    form.status == false;
  }
  
});
const updateStatus = () => {
  if(document.getElementById('activated').checked){
    form.status = true;

  }else{
    form.status = false;
  }
}
const submit = () => {
  if(document.getElementById('activated').checked){
    form.status = true
  }else{
    form.status = false;
  }
  if(props.task==null)
  {
    form.name = props.title;
    form.post(route("settings.background.store"));
  }else{
    form.put(route("settings.background.update",{ id: props.task.id }));
  }
};

</script>


<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <div class="mt-2">
          <InputLabel for="period" value="Periodo" />

          <select
              v-model="form.period"
              class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
            >
              <option disabled value="">Selecciona una opción</option>
              <option value="everyFiveMinutes">Cada 5 minutos</option>
              <option value="everyFifteenMinutes">Cada 15 minutos</option>
              <option value="everyThirtyMinutes">Cada 30 minutos</option>
              <option value="hourly">Cada hora</option>
              <option value="daily">Diariamente</option>
            </select>
          <InputError class="mt-2" :message="form.errors.period" />
          <InputError class="mt-2" :message="form.errors.name" />
        </div>
        <div class="mt-4">
          <div class="flex gap-3">

            <InputLabel for="status" value="¿Activo?" />
            <label class="switch">
              <input
               type="checkbox" 
               id="activated" 
               @click="updateStatus"
              />
              <span class="slider round"></span>
            </label>
          </div>
          <InputError class="mt-2" :message="form.errors.status" />
        </div>
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