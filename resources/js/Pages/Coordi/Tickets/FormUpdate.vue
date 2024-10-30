<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  ticket: Object,
  users: {
    type: Array,
    default: () => [],
  },
  technicals: {
    type: Array,
    default: () => [],
  },
});

const form = useForm({
  subject: "",
  description: "",
  status: "0",
  user_id: "",
  technical_id: "",
});

onMounted(() => {
  if (props.ticket) {
    form.subject = props.ticket.subject || "";
    form.description = props.ticket.description || "";
    form.status = props.ticket.status || "0";
    form.user_id = props.ticket.user_id || "Selecciona una opción";
    form.technical_id = props.ticket.technical_id || "Selecciona una opción";
  }
});

const submit = () => {
  if (form.user_id === "Selecciona una opción") {
    form.user_id = null;
  }

  if (form.technical_id === "Selecciona una opción") {
    form.technical_id = null;
  }

  form.put(route("tickets.update", { id: props.ticket.id }));
};

const seleccionar = (valor) => {
  form.status = valor.toString();
};
</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">Actualizar estado del ticket</h2>

    <div class="mt-4">
      <div class="flex justify-center mt-5">
        <button
          @click="seleccionar('0')"
          class="border py-2 px-3 rounded-l-md"
          :class="{
            'bg-red-500 text-white': form.status === '0',
            'hover:bg-red-100': form.status !== '0',
          }"
        >
          Pendiente
        </button>
        <button
          @click="seleccionar('1')"
          class="border py-2 px-3"
          :class="{
            'bg-yellow-500 text-white': form.status === '1',
            'hover:bg-yellow-100': form.status !== '1',
          }"
        >
          En espera
        </button>
        <button
          @click="seleccionar('2')"
          class="border py-2 px-3"
          :class="{
            'bg-blue-500 text-white': form.status === '2',
            'hover:bg-blue-100': form.status !== '2',
          }"
        >
          Trabajando
        </button>
        <button
          @click="seleccionar('3')"
          class="border py-2 px-3 rounded-r-md"
          :class="{
            'bg-green-500 text-white': form.status === '3',
            'hover:bg-green-100': form.status !== '3',
          }"
        >
          Solucionado
        </button>
      </div>
    </div>
  </div>
  <div class="mt-5 pl-5 pr-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div v-if="$page.props.auth.user.admin == 1">
        <div v-if="users.length !== 0">
          <InputLabel for="user_id" value="Usuario" />
          <select
            v-model="form.user_id"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="Selecciona una opción" selected>
              Selecciona una opción
            </option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.id + " - " + user.name }}
            </option>
          </select>
        </div>
        <div v-else class="mt-4">
          <span class="bg-yellow-100 p-2 justify-center flex rounded-md"
            >No tienes usuarios para asignar</span
          >
        </div>

        <div class="mt-4" v-if="technicals.length !== 0">
          <InputLabel for="technical_id" value="Técnico encargado" />
          <select
            v-model="form.technical_id"
            class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
          >
            <option value="Selecciona una opción" selected>
              Selecciona una opción
            </option>
            <option
              v-for="technical in technicals"
              :key="technical.id"
              :value="technical.id"
            >
              {{ technical.id + " - " + technical.name }}
            </option>
          </select>
        </div>
        <div v-else class="mt-4">
          <span class="bg-yellow-100 p-2 justify-center flex rounded-md"
            >No tienes técnicos para asignar la tarea</span
          >
        </div>
      </div>

      <div class="mt-4">
        <InputLabel for="subject" value="Asunto" />
        <TextInput
          id="subject"
          v-model="form.subject"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="subject"
        />
        <InputError class="mt-2" :message="form.errors.subject" />
      </div>

      <div class="mt-4">
        <InputLabel for="description" value="Descripción" />
        <textarea
          id="description"
          v-model="form.description"
          type="text"
          class="mt-1 block w-full border-gray-300"
          required
          autocomplete="description"
          style="height: 250px; resize: none; border-radius: 1.5%"
        />
      </div>

      <input type="hidden" id="status" :value="form.status" readonly />

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
  props: ["ticket"],
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