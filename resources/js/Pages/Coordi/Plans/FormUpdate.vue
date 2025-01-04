<script setup>
import { useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

// Los datos del plan se pasan como propiedad (simulando desde el controlador)
const props = defineProps(['plan']);

// Cargar los datos iniciales del plan en el formulario
const form = useForm({
  name: props.plan.name,
  description: props.plan.description,
  price: props.plan.price,
  burst_limit: {
    upload_limits: props.plan.burst_limit.upload_limits.replace('M', ''),
    download_limits: props.plan.burst_limit.download_limits.replace('M', ''),
  },
  burst_threshold: {
    upload_limits: props.plan.burst_threshold.upload_limits.replace('M', ''),
    download_limits: props.plan.burst_threshold.download_limits.replace('M', ''),
  },
  burst_time: {
    upload_limits: props.plan.burst_time.upload_limits.replace('s', ''),
    download_limits: props.plan.burst_time.download_limits.replace('s', ''),
  },
  limite_at: {
    upload_limits: props.plan.limite_at.upload_limits.replace('K', ''),
    download_limits: props.plan.limite_at.download_limits.replace('K', ''),
  },
  max_limit: {
    upload_limits: props.plan.max_limit.upload_limits.replace('M', ''),
    download_limits: props.plan.max_limit.download_limits.replace('M', ''),
  },
});

// Añade las unidades a los valores antes de enviar el formulario
const addUnits = () => {
  form.burst_limit.upload_limits += 'M';
  form.burst_limit.download_limits += 'M';

  form.burst_threshold.upload_limits += 'M';
  form.burst_threshold.download_limits += 'M';

  form.burst_time.upload_limits += 's';
  form.burst_time.download_limits += 's';

  form.limite_at.upload_limits += 'K';
  form.limite_at.download_limits += 'K';

  form.max_limit.upload_limits += 'M';
  form.max_limit.download_limits += 'M';
};

// Elimina las unidades de los valores existentes
const removeUnits = () => {
  form.burst_limit.upload_limits = form.burst_limit.upload_limits.replace('M', '');
  form.burst_limit.download_limits = form.burst_limit.download_limits.replace('M', '');

  form.burst_threshold.upload_limits = form.burst_threshold.upload_limits.replace('M', '');
  form.burst_threshold.download_limits = form.burst_threshold.download_limits.replace('M', '');

  form.burst_time.upload_limits = form.burst_time.upload_limits.replace('s', '');
  form.burst_time.download_limits = form.burst_time.download_limits.replace('s', '');

  form.limite_at.upload_limits = form.limite_at.upload_limits.replace('K', '');
  form.limite_at.download_limits = form.limite_at.download_limits.replace('K', '');

  form.max_limit.upload_limits = form.max_limit.upload_limits.replace('M', '');
  form.max_limit.download_limits = form.max_limit.download_limits.replace('M', '');
};

// Envía el formulario para actualizar
const submit = () => {
  addUnits(); // Agrega las unidades antes de enviar el formulario
  form.put(route("plans.update", { id: props.plan.id }), {
    onError: () => {
      // Si hay errores, elimina las unidades para mantener los valores originales
      removeUnits();
    },
  });
};
</script>

<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <!-- Name -->
      <div>
        <InputLabel for="name" value="Nombre" />
        <TextInput
          id="name"
          v-model="form.name"
          type="text"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="name"
        />
        <InputError class="mt-2" :message="form.errors.name" />
      </div>

      <!-- Description -->
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

      <!-- Price -->
      <div>
        <InputLabel for="price" value="Precio" />
        <TextInput
          id="price"
          v-model="form.price"
          type="number"
          min="0"
          class="mt-1 block w-full"
          required
          autofocus
          autocomplete="price"
        />
        <InputError class="mt-2" :message="form.errors.price" />
      </div>

      <!-- Burst Limit -->
      <div class="mt-4">
        <InputLabel value="Burst Limit (Mbps)" />
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_limit.upload_limits" value="Subida" />
            <TextInput
              id="burst_limit.upload_limits"
              v-model="form.burst_limit.upload_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['burst_limit.upload_limits']" />
          </div>
          <div>
            <InputLabel for="burst_limit.download_limits" value="Bajada" />
            <TextInput
              id="burst_limit.download_limits"
              v-model="form.burst_limit.download_limits"
              min="0"
              type="number"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['burst_limit.download_limits']" />
          </div>
        </div>
      </div>

      <!-- Burst Threshold -->
      <div class="mt-4">
        <InputLabel value="Burst Threshold (Mbps)" />
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_threshold.upload_limits" value="Subida" />
            <TextInput
              id="burst_threshold.upload_limits"
              v-model="form.burst_threshold.upload_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['burst_threshold.upload_limits']" />
          </div>
          <div>
            <InputLabel for="burst_threshold.download_limits" value="Bajada" />
            <TextInput
              id="burst_threshold.download_limits"
              v-model="form.burst_threshold.download_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['burst_threshold.download_limits']" />
          </div>
        </div>
      </div>

      <!-- Burst Time -->
      <div class="mt-4">
        <InputLabel value="Burst Time (S)" />
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="burst_time.upload_limits" value="Subida" />
            <TextInput
              id="burst_time.upload_limits"
              v-model="form.burst_time.upload_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="S"
            />
            <InputError class="mt-2" :message="form.errors['burst_time.upload_limits']" />
          </div>
          <div>
            <InputLabel for="burst_time.download_limits" value="Bajada" />
            <TextInput
              id="burst_time.download_limits"
              v-model="form.burst_time.download_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="S"
            />
            <InputError class="mt-2" :message="form.errors['burst_time.download_limits']" />
          </div>
        </div>
      </div>

      <!-- Limite At -->
      <div class="mt-4">
        <InputLabel value="Limite At (Kbps)" />
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="limite_at.upload_limits" value="Subida" />
            <TextInput
              id="limite_at.upload_limits"
              v-model="form.limite_at.upload_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Kbps"
            />
            <InputError class="mt-2" :message="form.errors['limite_at.upload_limits']" />
          </div>
          <div>
            <InputLabel for="limite_at.download_limits" value="Bajada" />
            <TextInput
              id="limite_at.download_limits"
              v-model="form.limite_at.download_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Kbps"
            />
            <InputError class="mt-2" :message="form.errors['limite_at.download_limits']" />
          </div>
        </div>
      </div>

      <!-- Max Limit -->
      <div class="mt-4">
        <InputLabel value="Max Limit (Mbps)" />
        <div class="mt-1 flex gap-2">
          <div>
            <InputLabel for="max_limit.upload_limits" value="Subida" />
            <TextInput
              id="max_limit.upload_limits"
              v-model="form.max_limit.upload_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['max_limit.upload_limits']" />
          </div>
          <div>
            <InputLabel for="max_limit.download_limits" value="Bajada" />
            <TextInput
              id="max_limit.download_limits"
              v-model="form.max_limit.download_limits"
              type="number"
              min="0"
              class="mt-1 block w-full"
              placeholder="Mbps"
            />
            <InputError class="mt-2" :message="form.errors['max_limit.download_limits']" />
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Actualizar Plan
        </PrimaryButton>
      </div>
    </form>
  </div>
</template>
