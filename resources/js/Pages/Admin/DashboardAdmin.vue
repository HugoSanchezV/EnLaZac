<script setup>
import { ref } from "vue";
import ModalContracts from "./Components/ModalStatsContracts.vue";
import ModalMorrosos from "./Components/ModalStatsMorrosos.vue";
import ModalTickets from "./Components/ModalStatsTickets.vue";
import ModalUsers from "./Components/ModalStatsUsers.vue";
import useGeneralNotifications from "@/Components/Base/hooks/useGeneralFlashNotifications";
import Graphics from "./Components/Graphics.vue";
import {Link} from "@inertiajs/vue3";

const props = defineProps({
  applyStyles: {
      type: Boolean,
      default: true,
    },
    morrosos: {
      type: Object,
    },
    activeDevice: {
      type: Object,
    },
    new_tickets: {
      type: Object,
    },
    currentUsers: {
      type: Object,
    },
    activeContract: {
      type: Object,
    },
    target: {
      type: Array,
    },
    upload_rate:{
      type: Array,
    },
    download_rate: {
      type: Array,
    },
    upload_byte: {
      type: Array,
    },
    download_byte: {
      type: Array,
    },
    routers: {
      type: Array,
    }
});


const isModalContractsOpen = ref({});
const isModalMorrososOpen = ref({});
const isModalTicketsOpen = ref({});
const isModalUsersOpen = ref({});

isModalContractsOpen.value = false;
isModalMorrososOpen.value = false;
isModalTicketsOpen.value = false;
isModalUsersOpen.value = false;

const openContractsModal = () => {
  isModalContractsOpen.value = true;
};

const closeContractsModal = () => {
  isModalContractsOpen.value = false;
};

const openMorrososModal = () => {
  isModalMorrososOpen.value = true;
};

const closeMorrososModal = () => {
  isModalMorrososOpen.value = false;
};
//---------------------------------------------



const openTicketsModal = () => {
  isModalTicketsOpen.value = true;
};

const closeTicketsModal = () => {
  isModalTicketsOpen.value = false;
};

const openUsersModal = () => {
  isModalUsersOpen.value = true;
};

const closeUsersModal = () => {
  isModalUsersOpen.value = false;
};




useGeneralNotifications();
</script>
<style>
.frame-stats{
    display: grid;
    grid-template-columns: repeat(2, 1fr); /* 2 columnas iguales */
    gap: 10px;
  }
  .graficas{
    max-width: 100%;
    display: flex;
    flex-direction: column;
  }
  .graphic-container{

    /* justify-content: center; */
    width: 100%;
  }
  .rate, .byte{
    display: flex;
    justify-content: center;
    flex-direction: column;
    width: 99%;
  }
  .frame-content{
    display: flex;
    align-items: center;
    flex-direction: column;
    width: 14rem;
    height: 14rem;

  }
@media (min-width: 1450px) {
  .frame-stats{
    display: flex;
    justify-content: space-between;
    max-width: 100%;
  }
  .frame{
    width: 15rem;
    /* border: 1px solid #000000; */
  }
  .graficas{
    display: flex;
    flex-direction: row;
    width: 100%;
  }
  .rate, .byte{
    width: 50%;
    display: flex;
    flex-direction: column;
  }
  .graphic-container{
   display: block;
   width: 100%;

  }
  canvas{
    width: 50%;
  }
}
</style>

<template>
    <div class="py-12 ">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div
          class="overflow-hidden sm:rounded-lg"
          :class="{
            'bg-gray-100  sm:rounded-lg': applyStyles,
            'bg-transparent shadow-none': !applyStyles,
          }"
        >
          <slot name="content">
            <div class="frame-stats flex gap-4 w-full">
               <div class="frame">
                  <button
                  @click="openMorrososModal()">
                    <div class="frame-content w-fit rounded-[25px] bg-white p-8 aspect">
                        <div class="h-12">
                          <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">person</span>
                        </div>
                        <div class="my-2">
                            <h2 class="text-4xl font-bold"><span>{{ morrosos.length }}</span></h2>
                        </div>

                        <div>
                            <p class="mt-2 font-sans text-base font-medium text-gray-700">Usuarios deudores (Morrosos)</p>
                        </div>
                    </div>
                  </button>
                  <modal-contracts
                    :show="isModalContractsOpen"
                    @close="closeContractsModal()"
                    @selectData="confirmSelectionContracts($event)"
                    :data="activeContract"
                    :title="
                      'Selecciona un dispositivo del inventario para la conexión '
                    "
                    item-text="mac_address"
                  >
                </modal-contracts>
                </div>
                <div class="frame">
                  <button
                  @click="openContractsModal()">
                    <div class="frame-content w-fit rounded-[25px] bg-white p-8 aspect">
                        <div class="h-12">
                          <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">contract</span>
                        </div>
                        <div class="my-2">
                            <h2 class="text-4xl font-bold"><span>{{ activeContract.length }}</span></h2>
                        </div>

                        <div>
                            <p class="mt-2 font-sans text-base font-medium text-gray-700">Contratos activos</p>
                        </div>
                    </div>
                  </button>

                  <modal-morrosos
                    :show="isModalMorrososOpen"
                    @close="closeMorrososModal()"
                    @selectData="confirmSelectionMorrosos($event)"
                    :data="morrosos"
                    :title="
                      'Selecciona un dispositivo del inventario para la conexión '
                    "
                    item-text="mac_address"
                  >
                </modal-morrosos>
                </div>

                <div class="frame">
                  <button
                  @click="openTicketsModal()">
                    <div class="frame-content w-fit rounded-[25px] bg-white p-8 aspect">
                        <div class="h-12">
                          <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">confirmation_number</span>
                        </div>
                        <div class="my-2">
                            <h2 class="text-4xl font-bold"><span>{{ new_tickets.length }}</span></h2>
                        </div>

                        <div>
                            <p class="mt-2 font-sans text-base font-medium text-gray-700">Tickets nuevos</p>
                        </div>
                    </div>
                  </button>

                  <modal-tickets
                    :show="isModalTicketsOpen"
                    @close="closeTicketsModal()"
                    @selectData="confirmSelectionTickets($event)"
                    :data="new_tickets"
     
                    :title="
                      'Selecciona un dispositivo del inventario para la conexión '
                    "
                    item-text="mac_address"
                  >
                </modal-tickets>
                </div>
                <div class="frame">
                  <Link
                  :href="route('usuarios')">
                      <div class="frame-content w-fit rounded-[25px] bg-white p-8 aspect">
                          <div class="h-12">
                            <span class="material-symbols-outlined text-blue-500" style="font-size: 2rem;">account_circle</span>                      </div>
                          <div class="my-2">
                              <h2 class="text-4xl font-bold"><span>{{ currentUsers.length }}</span></h2>
                          </div>
  
                          <div>
                              <p class="mt-2 font-sans text-base font-medium text-gray-700">Usuarios registrados</p>
                          </div>
                      </div>
               
                  </Link>
                  <modal-users
                    :show="isModalUsersOpen"
                    @close="closeUsersModal()"
                    @selectData="confirmSelectionUsers($event)"
                    :data="currentUsers"
                    :title="
                      'Selecciona un dispositivo del inventario para la conexión '
                    "
                    item-text="mac_address"
                  >
                </modal-users>
                </div>
              
            </div>
            <div class="mt-20 flex justify-center">
              <div class="pt-3 pb-3 pl-5 pr-5 rounded-md bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-gray-50">
                <h2 class="text-2xl">Estadísticas de velocidad y consumo</h2>
              </div>
              
            </div>
            <div  v-if="props.routers.length > 0" class="mt-5" v-for="(targetItem, index) in target" :key="index" >
              <div class="flex gap-1 mb-3">
                <h2 class="text-blue-500 text-2xl">Router</h2><span class="text-blue-500 text-2xl">{{ routers[index] }}</span>
              </div>
              <div class="graphic-container">
                  <Graphics
                  :target = "targetItem"
                  :upload_rate = "upload_rate[index]"
                  :download_rate = "download_rate[index]"
                  :upload_byte = "upload_byte[index]"
                  :download_byte = "download_byte[index]"
                  :index = "index"
                />
              </div>
            </div>
            <div v-else>
              <h2>No hay routers disponibles</h2>
            </div>
              
          </slot>
        </div>
      </div>
    </div>
</template>