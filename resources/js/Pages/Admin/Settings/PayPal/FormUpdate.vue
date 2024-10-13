<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  settings: Object,
});

const form = useForm({
  live_client_id: "",
  live_client_secret: "",
});

onMounted(() => {
  if (props.settings) {
    form.live_client_id = props.settings.live_client_id || "";
    form.live_client_secret = props.settings.live_client_secret || "";
  }
});

const submit = () => {
  console.log(form);
  form.post(route("settings.paypal.update"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Edita las credenciales de acceso a Paypal
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="live_client_id" value="ID Cliente" />
        <TextInput
          id="live_client_id"
          v-model="form.live_client_id"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="live_client_id"
        />
        <InputError class="mt-2" :message="form.errors.live_client_id" />
      </div>

      <div>
        <InputLabel for="live_client_secret" value="Clave de cliente" />
        <TextInput
          id="live_client_secret"
          v-model="form.live_client_secret"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="live_client_secret"
        />
        <InputError class="mt-2" :message="form.errors.live_client_secret" />
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
export default {
  props: ["router"],
  data() {
    return {
      modificarPassword: false,
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