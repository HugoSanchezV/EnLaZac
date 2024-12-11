<template>
  <div>
    <div @click="$emit('close-toast')">
      {{ message }}
    </div>

    <div class="flex justify-around">
      <button v-if="cancel" @click="$emit('cancel')">
        {{ textCancel }}
      </button>

      <button v-if="accept" @click="$emit('accept')">
        {{ textConfirm }}
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    message: {
      type: String,
      default: "",
    },
    accept: {
      type: Boolean,
      default: true,
    },

    cancel: {
      type: Boolean,
      default: false,
    },

    textConfirm: {
      type: String,
      default: "Aceptar",
    },

    textCancel: {
      type: String,
      default: "Cancelar",
    },
  },
};
</script>