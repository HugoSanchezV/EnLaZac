<script setup>
import { Head, Link } from "@inertiajs/vue3";

defineProps({
  canLogin: Boolean,
  canRegister: Boolean,
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
});
</script>

<template>
  <Head title="Bienvenida" />
  <div class="min-h-screen bg-white text-gray-800">
    <div class="flex flex-col items-center justify-center min-h-screen px-6 py-12">
      <h1 class="text-5xl font-extrabold text-gray-900 mb-6">
        ¡Bienvenido a EnLaZac!
      </h1>
      <p class="text-lg text-gray-600 mb-8 text-center">
        Revisa cualquier característica de tu cuenta o escribenos sí encuentras algún error en nuestro servicio
      </p>

      <div class="flex space-x-4">
        <Link
          v-if="canLogin"
          :href="route('login')"
          class="px-6 py-3 bg-indigo-600 text-white rounded-lg shadow-lg hover:bg-indigo-700 transition focus:outline-none focus:ring focus:ring-indigo-300"
        >
          Iniciar Sesión
        </Link>
        <Link
          v-if="canRegister"
          :href="route('register')"
          class="px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg hover:bg-green-600 transition focus:outline-none focus:ring focus:ring-green-300"
        >
          Registrarse
        </Link>
      </div>

      <footer class="mt-12 text-sm text-gray-500">
        Todos los derechos reservados 2024
      </footer>
    </div>
  </div>
</template>
