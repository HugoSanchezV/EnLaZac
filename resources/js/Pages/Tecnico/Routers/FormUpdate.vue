<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  router: Object,
});


const form = useForm({
  user: "",
  ip_address: "",
  password: "",
  password_confirmation: "",
});

onMounted(() => {
  if (props.router) {
    form.user = props.router.user || "";
    form.ip_address = props.router.ip_address || "";
  }
});

const submit = () => {
  console.log(form)
  form.put(route("routers.update", { id: props.router.id }), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Edita las credenciales de acceso al router 
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="user" value="Usuario" />
        <TextInput
          id="user"
          v-model="form.user"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="user"
        />
        <InputError class="mt-2" :message="form.errors.user" />
      </div>

      <div class="mt-4">
        <InputLabel for="ip_address" value="IP" />
        <TextInput
          id="ip_address"
          v-model="form.ip_address"
          type="text"
          class="mt-1 block w-full"
          autocomplete="ip_address"
        />
        <InputError class="mt-2" :message="form.errors.ip_address" />
      </div>
      
      <div class="flex justify-between items-center gap-2 mt-5">
        <p>Editar Password</p>
        <label class="switch">
          <input type="checkbox" checked v-model="modificarPassword" />
          <span class="slider round"></span>
        </label>
      </div>

      <div v-if="modificarPassword">
        <div class="mt-4">
          <InputLabel for="password" value="Password" />
          <TextInput
            id="password"
            v-model="form.password"
            type="password"
            class="mt-1 block w-full"
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
            autocomplete="new-password"
          />
          <InputError
            class="mt-2"
            :message="form.errors.password_confirmation"
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