<script setup>
import { defineProps, ref } from "vue";
import ModalImportExcel from "@/Pages/Admin/Components/ModalImportExcel.vue";
import NoticeForImport from "./NoticeForImport.vue";

const props = defineProps({
  toImportRoute: String,
  urlComplete: {
    type: String,
    default: null,
    toImportRoute: String,
  },
  headings: String
});

const urlImport = ref("");

if (props.urlComplete) {
  urlImport.value = props.urlComplete;
} else {
  urlImport.value = route(props.toImportRoute);
}
// console.log("Estas en el boton ");
// console.log(urlImport.value);
const isModalOpen = ref(false);

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};
</script>
<template>
  <div class="flex justify-center md:justify-start pb-2 pr-2 gap-1 bg">
    <a
      class="py-2 px-3 bg-slate-600 text-white flex gap-1 justify-center text-sm hover:bg-slate-500 rounded-md"
      href="#"
      @click="openModal()"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
        stroke-width="1.5"
        stroke="currentColor"
        class="size-4"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12-3-3m0 0-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
        />
      </svg>

      Importar
    </a>

    <modal-import-excel
      v-model:show="isModalOpen"
      @close="closeModal"
      :to-import-route="urlImport"
      :title="'Selecciona un archivo excel para poder cargar los datos'"
      item-text="mac_address"
    >

    <template v-slot:content>
        
      <notice-for-import
      :data="headings"
      >
      </notice-for-import>
    </template>
    </modal-import-excel>
  </div>
</template>