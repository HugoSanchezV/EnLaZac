<script setup>
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import DashboardBase from "@/Pages/DashboardBase.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
const props = defineProps({
  user: Object,
});

const form = useForm({
  message: "",
  user_id: props.user.id,
  chat_id: props.user.telegram_account.chat_id,
});

const submit = () => {
  const url = route("usuarios.telegram.send.message");
  form.post(url, {});
};
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="block md:flex md:justify-between">
        <div class="mb-2 md:mb-0">
          <h4>Telegram para {{ user.name }}</h4>
        </div>
        <div class="md:flex gap-1">
          <div
            method="get"
            class="mb-1 md:mb-0 flex justify-center md:justify-between items-center gap-2 text-white bg-blue-500 hover:bg-blue-600 py-2 px-3 text-sm rounded-md"
          >
            <span class="material-symbols-outlined" style="font-size: 16px">
              phone
            </span>
            {{ user.phone }}
          </div>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <div
        class="flex justify-center m-auto border flex-col p-10 bg-white md:w-1/2"
      >
        <h2 class="text-center">Envia un mensaje directo a tus cliente</h2>
      </div>

      <div
        class="bg-white m-auto mt-4 p-5 flex justify-center flex-col columns-1 md:w-1/2 w-full"
      >
        <form @submit.prevent="submit">
          <div class="mt-2 p-5">
            <InputLabel for="message" value="Mensaje" />
            <textarea
              id="message"
              v-model="form.message"
              type="text"
              class="mt-1 block w-full border border-gray-300"
              required
              autocomplete="message"
              placeholder="Escribe tu mensaje aqui..."
              style="height: 250px; resize: none; border-radius: 1.5%"
            />
            <InputError class="mt-2" :message="form.errors.message" />
          </div>

          <div class="md:flex md:items-center md:justify-end mt-4">
            <button
              class="flex items-center justify-center w-full md:w-1/5 gap-2 bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded-md"
              :class="{ 'opacity-25': form.processing }"
              :disabled="form.processing"
            >
              <span>Enviar</span>
              <span class="material-symbols-outlined" style="font-size: 18px">
                send
              </span>
            </button>
          </div>
        </form>
      </div>
    </template>
  </dashboard-base>
</template>
