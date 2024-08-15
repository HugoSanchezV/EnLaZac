<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { ref, watch } from "vue";

const props = defineProps({
  devices: {
    type: Array,
    default: () => [],
  },
  users: {
    type: Array,
    default: () => [],
  },
  router: {
    type: Object,
  },
});

const devicesCount = props.devices.length;

let user_active = ref(false);
let device_active = ref(false);

const setUserStatus = () => {
  user_active.value = !user_active.value;
};

const setDeviceStatus = () => {
  device_active.value = !device_active.value;
};

const getIpAvalible = (devices) => {
  const allIps = Array.from({ length: 254 }, (_, i) => i + 1);
  const usedIps = devices.map((device) =>
    parseInt(device.address.split(".").pop())
  );

  return allIps.filter((ip) => !usedIps.includes(ip));
};

const ips = ref([]);
watch(
  () => props.devices,
  (newDevices) => {
    ips.value = getIpAvalible(newDevices);
  },
  { immediate: true }
);

const form = useForm({
  address: "",
  comment: "",
  user: "",
  device: "",
  router_id: "",
});

let address = "";
let comment = "";
let user = "";
let device = "";
form.router_id = route().params.router;

const submit = () => {
  form.address = props.router.initial_device_ip + address;
  form.comment = comment
  form.user = user
  form.user = device

  if (!user_active.value) {
    form.user = null;
  }

  if (!device_active.value) {
    form.device_active = null;
  }

  form.post(route("devices.store"), {
    onSuccess: () => {
      router.back();
    },
  });
};

const seleccionar = (valor) => {
  form.admin = Number(valor);
};
</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="block text-center" v-if="devicesCount < 254">
      Configura una nueva conexion en el Router con Dirrección
      <span class="bg-gray-400 text-white py-1 px-2 rounded-sm">{{
        props.router.ip_address
      }}</span>
    </h2>
    <h2 class="text-center" v-else>
      Todas las Direcciones IP de este Dispositivo estan Siendo Usadas
    </h2>
  </div>

  <div class="mt-5" v-if="devicesCount < 254">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="address" value="Ip Address" />
        <div class="flex justify-center items-center">
          <p
            class="bg-slate-600 pr-1 text-end text-white font-semibold rounded-l-sm py-2 px-3 mt-1"
          >
            {{ props.router.initial_device_ip }}
          </p>
          <select
            id="address"
            v-model="address"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-r-md shadow-sm"
            required
          >
            <option
              v-for="ipAvalible in ips"
              :key="ipAvalible"
              :value="ipAvalible"
            >
              {{ ipAvalible }}
            </option>
          </select>
        </div>
        <InputError class="mt-2" :message="form.errors.address" />
      </div>

      <div class="mt-4">
        <InputLabel for="comment" value="Comentario" />
        <TextInput
          id="comment"
          v-model="comment"
          type="text"
          class="mt-1 block w-full"
          required
        />
        <InputError class="mt-2" :message="form.errors.comment" />
      </div>

      <div class="mt-4">
        <div class="flex justify-between">
          <InputLabel for="user" value="Usuario" />

          <label class="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              class="sr-only peer"
              @click="setUserStatus"
              v-model="user_active"
            />
            <div
              class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-100 peer-focus:ring-gray-100 rounded-full peer bg-gray-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-300 peer-checked:bg-blue-400"
            ></div>
          </label>
        </div>
        <div v-if="user_active">
          <select
            id="user"
            v-model="user"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            required
          >
            <option value="">Selecciona un Usuario</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.id }} - {{ user.name }}
            </option>
          </select>
        </div>
        <p v-else class="text-gray-500 text-sm">
          ( Conectar usuario en otro momento )
        </p>
        <InputError class="mt-2" :message="form.errors.address" />
      </div>

      <div class="mt-4">
        <div class="flex justify-between">
          <InputLabel for="device" value="Dispositivo" />
          <label class="inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              class="sr-only peer"
              @click="setDeviceStatus"
              v-model="device_active"
            />
            <div
              class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-gray-100 peer-focus:ring-gray-100 rounded-full peer bg-gray-300 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-300 peer-checked:bg-blue-400"
            ></div>
          </label>
        </div>
        <div v-if="device_active">
          <select
            id="device"
            v-model="device"
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            required
          >
            <option value="">Selecciona un Dispositivo</option>
          </select>
        </div>
        <p v-else class="text-gray-500 text-sm">
          ( Conectar dispositivo en otro momento )
        </p>
        <InputError class="mt-2" :message="form.errors.address" />
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

          Realizar Conexión
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
