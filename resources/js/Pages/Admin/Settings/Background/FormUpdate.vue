<script setup>
import { onMounted } from "vue";
import { router, useForm } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
  task: Object,
  title: String,
});

const form = useForm({
  name: "",
  period: "",
  status: "",
});


onMounted(() => {
  if (props.task) {
    form.name = props.task.name || "";
    form.period = props.task.period || "";
    form.status = props.task.status || ""
  }
});
const submit = () => {
  form.put(route("settings.background.update"));
};

</script>


<template>
  <div class="mt-5">
    <form @submit.prevent="submit" class="border p-14 m-5 bg-white">
      <div>
        <h2>Interes por fecha de corte: </h2>
        <div class="mt-2">
          
          <InputLabel for="amountCourt" value="Monto" />
          <TextInput
            id="amountCourt"
            v-model="form.amountCourt"
            type="number"
            class="mt-1 block w-full"
            autocomplete="amountCourt"
          />
          <InputError class="mt-2" :message="form.errors.amountCourt" />
        </div>
      </div>

      <div class="mt-4">
        <h2>Interes por adeudo de mes: </h2>
        <div class="mt-2">
          
          <InputLabel for="amountDebt" value="Monto" />
          <TextInput
            id="amountDebt"
            v-model="form.amountDebt"
            type="number"
            class="mt-1 block w-full"
            autocomplete="amountDebt"
          />
          <InputError class="mt-2" :message="form.errors.amountDebt" />
        </div>
      </div>

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