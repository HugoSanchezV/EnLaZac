<script setup>
import { onMounted, ref } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import GoogleMaps from '@/Components/GoogleMaps.vue'

const props = defineProps({
  installationSetting: Object,
  installation: Object,
});

const form = useForm({
  exemption_months: "",
});

onMounted(() => {
  if (props.installationSetting) {
    form.exemption_months = props.installationSetting.exemption_months || "";
  }
});

const submit = () => {
  form.put(route("settings.installation.update", { id: props.installationSetting }));
};

</script>

<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-7 m-5 bg-white">
      <div>
        <InputLabel for="installation_id" value="ID de la instalación" />
        <div class="mt-2">
          <TextInput
            id="installation_id"
            v-model="installation.contract.inventorie_device.device.user.name"
            type="text"
            class="mt-1 block w-full"
            autofocus
            readonly  
          />
        </div>
        <InputError class="mt-2" :message="form.errors.installation_id" />
      </div>

      <div class="mt-2 ">
          <InputLabel for="exemption_months" value="Mes/es asignado" />
          <TextInput
            id="exemption_months"
            v-model="form.exemption_months"
            type="number"
            class="mt-1 block w-full"
            autofocus
            autocomplete="exemption_months"
            @input="onDateChange"
          />
          <InputError class="mt-2" :message="form.errors.exemption_months" />
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

          Guardar Configuracion
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