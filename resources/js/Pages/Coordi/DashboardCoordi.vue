<script setup>
import DashboardBase from "../DashboardBase.vue";
import { defineProps } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  tickets: Number,
  users: Number,
  contracts: Number,
  active_devices: Number,
  user: Object,
});

const formattedDate = (date) => {
  const dateObj = new Date(date);
  const options = { year: "numeric", month: "long", day: "numeric" };
  return dateObj.toLocaleDateString("es-ES", options);
};

</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Dashboard</h2>
        </div>
      </div>
    </template>
    <template v-slot:content>

      <div class="flex justify-center flex-row flex-wrap gap-2 md:gap-8 mb-4">
        <div class="flex gap-2 md:gap-8">
          <Link
            :href="route('tickets')"
            class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
          >
            <div class="h-12">
              <span
                class="material-symbols-outlined text-blue-500"
                style="font-size: 2rem"
                >confirmation_number</span
              >
            </div>
            <div class="my-2">
              <h2 class="text-4xl font-bold">
                <span>{{ props.tickets }}</span>
              </h2>
            </div>
            <div>
              <p class="mt-2 font-sans text-base font-medium text-gray-700">
                Tickets Creados
              </p>
            </div>
          </Link>

          <Link
            :href="route('usuarios')"
            class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
          >
            <div class="h-12">
              <span
                class="material-symbols-outlined text-blue-500"
                style="font-size: 2rem"
                >person</span
              >
            </div>
            <div class="my-2">
              <h2 class="text-4xl font-bold">
                <span>{{ props.users }}</span>
              </h2>
            </div>
            <div>
              <p class="mt-2 font-sans text-base font-medium text-gray-700">
                Total Usuarios
              </p>
            </div>
          </Link>
        </div>

        <div class="flex gap-2 md:gap-8">
          <Link
            :href="route('routers')"
            class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
          >
            <div class="h-12">
              <span
                class="material-symbols-outlined text-blue-500"
                style="font-size: 2rem"
                >router</span
              >
            </div>
            <div class="my-2">
              <h2 class="text-4xl font-bold">
                <span>{{ props.contracts }}</span>
              </h2>
            </div>
            <div>
              <p class="mt-2 font-sans text-base font-medium text-gray-700">
                Total Contratos
              </p>
            </div>
          </Link>

          <Link
            :href="route('devices')"
            class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
          >
            <div class="h-12">
              <span
                class="material-symbols-outlined text-blue-500"
                style="font-size: 2rem"
                >nest_wifi_router</span
              >
            </div>
            <div class="my-2">
              <h2 class="text-4xl font-bold">
                <span>{{ props.active_devices }}</span>
              </h2>
            </div>
            <div>
              <p class="mt-2 font-sans text-base font-medium text-gray-700">
                Dispositvos Act.
              </p>
            </div>
          </Link>
        </div>
      </div>

      <div
        class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6 m-4"
      >
        <div class="md:flex">
          <div class="ml-6">
            <div
              class="uppercase tracking-wide text-sm text-indigo-500 font-semibold"
            >
              {{ id }}
            </div>
            <h1 class="text-xl font-bold text-gray-900">{{ user.name }}</h1>
            <p class="mt-2 text-gray-500">Email: {{ user.email }}</p>
            <p class="mt-1 text-gray-500">Teléfono: {{ user.phone }}</p>
            <p class="mt-1 text-gray-500">
              Registrado en: {{ formattedDate(user.created_at)}}
            </p>
          </div>
        </div>
      </div>

    </template>
  </dashboard-base>
</template>
