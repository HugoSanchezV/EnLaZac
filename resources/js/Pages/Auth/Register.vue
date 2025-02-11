<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
  name: "",
  email: "",
  phone: "",
  password: "",
  password_confirmation: "",
  terms: false,
});

const submit = () => {
  if (!/^(52|1)\d{10}$/.test(form.phone)) {
    toast.error(
      "El número de teléfono debe comenzar con 52(mx) o 1(usa) y tener exactamente 10 dígitos después",
      {
        position: POSITION.TOP_CENTER,
        draggable: true,
      }
    );
    return;
  }
  form.post(route("register"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>

<template>
  <Head title="Registrarme" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <form @submit.prevent="submit">
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

      <!-- <div>
                <InputLabel for="alias" value="Alias" />
                <TextInput
                    id="alias"
                    v-model="form.alias"
                    type="text"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="alias"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div> -->

      <div class="mt-4">
        <InputLabel for="email" value="Correo" />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          class="mt-1 block w-full"
          required
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="phone" value="Télefono" />
        <p class="text-sm mt-1 text-gray-600">
          52(mx) o 1(usa) seguido de 10 dígitos
          <span class="text-red-500">*</span>
        </p>
        <TextInput
          id="phone"
          v-model="form.phone"
          type="number"
          class="mt-1 block w-full"
          required
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.phone" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" value="Contraseña" />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="mt-4">
        <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
        <TextInput
          id="password_confirmation"
          v-model="form.password_confirmation"
          type="password"
          class="mt-1 block w-full"
          required
          autocomplete="new-password"
        />
        <InputError class="mt-2" :message="form.errors.password_confirmation" />
      </div>

      <div
        v-if="$page.props.jetstream.hasTermsAndPrivacyPolicyFeature"
        class="mt-4"
      >
        <InputLabel for="terms">
          <div class="flex items-center">
            <Checkbox
              id="terms"
              v-model:checked="form.terms"
              name="terms"
              required
            />

            <div class="ms-2">
              Estoy de acuerdo con los
              <a
                target="_blank"
                :href="route('terms.show')"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >Terminos del servicio</a
              >
              y
              <a
                target="_blank"
                :href="route('policy.show')"
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >Políticas de Privacidad</a
              >
            </div>
          </div>
          <InputError class="mt-2" :message="form.errors.terms" />
        </InputLabel>
      </div>

      <div class="flex items-center justify-end mt-4">
        <Link
          :href="route('login')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          ¿Tienes una Cuenta?
        </Link>

        <PrimaryButton
          class="ms-4"
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
        >
          Registrarme
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>
