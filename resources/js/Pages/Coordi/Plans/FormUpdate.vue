<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  plan: Object,
});

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

onMounted(() => {
  if (props.plan) {
    form.description = props.plan.description || "";

    form.burst_limit.upload_limits = props.plan.burst_limit.upload_limits || 0;
    form.burst_limit.download_limits = props.plan.burst_limit.download_limits || 0;

    form.burst_threshold.upload_limits = props.plan.burst_threshold.upload_limits || 0;
    form.burst_threshold.download_limits = props.plan.burst_threshold.download_limits || 0;

    form.burst_time.upload_limits = props.plan.burst_time.upload_limits || 0;
    form.burst_time.download_limits = props.plan.burst_time.download_limits || 0;

    form.limite_at.upload_limits = props.plan.limite_at.upload_limits || 0;
    form.limite_at.download_limits = props.plan.limite_at.download_limits || 0;

    form.max_limit.upload_limits = props.plan.max_limit.upload_limits || 0;
    form.max_limit.download_limits = props.plan.max_limit.download_limits || 0;
  }
});

const submit = () => {
  form.put(route("plans.update", { id: props.plan.id }));
};
</script>


<template>
  <div class="flex justify-center border flex-col m-5 p-10 bg-white">
    <h2 class="flex justify-center">
      Actualizar el Plan de Internet
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

          Guardar Cambios
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>


<script>
export default {
  props: ["plan"],
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