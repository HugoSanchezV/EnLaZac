<script setup>
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  description: "",  

  burst_limit: {
    upload_limits: "",
    download_limits: "",
  },
  burst_threshold: {
    upload_limits: "",
    download_limits: "",
  },
  burst_time: {
    upload_limits: "",
    download_limits: "",
  },
  limite_at: {
    upload_limits: "",
    download_limits: "",
  },
  max_limit: {
    upload_limits: "",
    download_limits: "",
  },
});


const submit = () => {
  form.post(route("plans.store"));
};

</script>

<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Crea un nuevo plan de internet
    </h2>
  </div>

  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <InputLabel for="description" value="Descripción" />
        <TextInput
          id="description"
          v-model="form.description"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="description"
        />
        <InputError class="mt-2" :message="form.errors.description" />
      </div>
      <!-- BURST LIMIT-->
       <div>
        <div class="mt-4">
          <InputLabel for="burst_limit" value="Burst Limit (Mbps)" />
        </div>
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_limit" value="Subida" class="opacity-80" />
            <TextInput
              id="burst_limit.upload_limits"
              v-model="form.burst_limit.upload_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "Mbps"
              required
              autocomplete="burst_limit.upload_limits"

            />
            <InputError class="mt-2" :message="form.errors.upload_limits" />
          </div>
          <div>
            <InputLabel for="burst_limit" value="Bajada" class="opacity-80" />
            <TextInput
              id="burst_limit.download_limits"
              v-model="form.burst_limit.download_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "Mbps"
              required
              autocomplete="burst_limit.download_limits"
    
            />
            <InputError class="mt-2" :message="form.errors.burst_limit" />
          </div>
         
        </div>
      </div>
      <div>
        <div class="mt-4">
          <InputLabel for="burst_threshold" value="Burst Threshold (Mbps)" />
        </div>
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_threshold" value="Subida" class="opacity-80" />
            <TextInput
              id="burst_threshold.upload_limits"
              v-model="form.burst_threshold.upload_limits"
              type="number"
              class="mt-1 block w-full"
              required
              placeholder = "Mbps"
              autocomplete="burst_threshold.upload_limits"

            />
            <InputError class="mt-2" :message="form.errors.burst_threshold" />
          </div>
          <div>
            <InputLabel for="burst_threshold" value="Bajada" class="opacity-80"/>
            <TextInput
              id="burst_threshold.download_limits"
              v-model="form.burst_threshold.download_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "Mbps"
              required
              autocomplete="burst_threshold.download_limits"
    
            />
            <InputError class="mt-2" :message="form.errors.burst_threshold" />
          </div>
         
        </div>
      </div>
      <div>
        <div class="mt-4">
          <InputLabel for="burst_time" value="Burst Time (S)" />
        </div>
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_time" value="Subida" class="opacity-80" />
            <TextInput
              id="burst_time.upload_limits"
              v-model="form.burst_time.upload_limits"
              type="number"
              class="mt-1 block w-full"
              required
              placeholder = "S"
              autocomplete="burst_time.upload_limits"

            />
            <InputError class="mt-2" :message="form.errors.burst_time" />
          </div>
          <div>
            <InputLabel for="burst_time" value="Bajada" class="opacity-80"/>
            <TextInput
              id="burst_time.download_limits"
              v-model="form.burst_time.download_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "S"
              required
              autocomplete="burst_time.download_limits"
    
            />
            <InputError class="mt-2" :message="form.errors.burst_time" />
          </div>
         
        </div>
      </div>
      <div>
        <div class="mt-4">
          <InputLabel for="limite_at" value="Limite At (Kbps)" />
        </div>
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="limite_at" value="Subida" class="opacity-80" />
            <TextInput
              id="limite_at.upload_limits"
              v-model="form.limite_at.upload_limits"
              type="number"
              class="mt-1 block w-full"
              required
              placeholder = "Kbps"
              autocomplete="limite_at.upload_limits"

            />
            <InputError class="mt-2" :message="form.errors.limite_at" />
          </div>
          <div>
            <InputLabel for="limite_at" value="Bajada" class="opacity-80"/>
            <TextInput
              id="limite_at.download_limits"
              v-model="form.limite_at.download_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "Kbps"
              required
              autocomplete="limite_at.download_limits"
    
            />
            <InputError class="mt-2" :message="form.errors.limite_at" />
          </div>
         
        </div>
      </div>

      <div>
        <div class="mt-4">
          <InputLabel for="max_limit" value="Max Limit (Mbps)" />
        </div>
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="max_limit" value="Subida" class="opacity-80" />
            <TextInput
              id="max_limit.upload_limits"
              v-model="form.max_limit.upload_limits"
              type="number"
              class="mt-1 block w-full"
              required
              placeholder = "Mbps"
              autocomplete="max_limit.upload_limits"

            />
            <InputError class="mt-2" :message="form.errors.max_limit" />
          </div>
          <div>
            <InputLabel for="max_limit" value="Bajada" class="opacity-80"/>
            <TextInput
              id="max_limit.download_limits"
              v-model="form.max_limit.download_limits"
              type="number"
              class="mt-1 block w-full"
              placeholder = "Mbps"
              required
              autocomplete="max_limit.download_limits"
    
            />
            <InputError class="mt-2" :message="form.errors.max_limit" />
          </div>
         
        </div>
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

          Enviar Ticket
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
