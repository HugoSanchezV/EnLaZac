<script setup>
// import { ref } from "vue";
// import ModalContracts from "./Components/ModalStatsContracts.vue";
// import ModalMorrosos from "./Components/ModalStatsMorrosos.vue";
// import ModalTickets from "./Components/ModalStatsTickets.vue";
// import ModalUsers from "./Components/ModalStatsUsers.vue";
import useGeneralNotifications from "@/Components/Base/hooks/useGeneralFlashNotifications";
import Graphics from "./Components/Graphics.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  applyStyles: {
    type: Boolean,
    default: true,
  },
  morrosos: {
    type: Array,
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
  upload_rate: {
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
  },
  all_routers: {
    type: Array,
  },
});

const changeRouter = (item) => {
  const url = route("dashboard", {
    router: item.id,
  });

  router.visit(url, { preserveScroll: true });
};

useGeneralNotifications();
</script>
<style>
.frame-stats {
  display: grid;
  grid-template-columns: repeat(2, 1fr); /* 2 columnas iguales */
  gap: 10px;
}
.graficas {
  max-width: 100%;
  display: flex;
  flex-direction: column;
}
.graphic-container {
  /* justify-content: center; */
  width: 100%;
}
.rate,
.byte {
  display: flex;
  justify-content: center;
  flex-direction: column;
  width: 99%;
}
.frame-content {
  display: flex;
  align-items: center;
  flex-direction: column;
  width: 14rem;
  height: 14rem;
}
@media (min-width: 1450px) {
  .frame-stats {
    display: flex;
    justify-content: space-between;
    max-width: 100%;
  }
  .frame {
    width: 15rem;
    /* border: 1px solid #000000; */
  }
  .graficas {
    display: flex;
    flex-direction: row;
    width: 100%;
  }
  .rate,
  .byte {
    width: 50%;
    display: flex;
    flex-direction: column;
  }
  .graphic-container {
    display: block;
    width: 100%;
  }
  canvas {
    width: 50%;
  }
}

.icon {
  font-size: 2rem; /* Tamaño por defecto */
}

@media (min-width: 768px) {
  /* Tamaño en pantallas medianas */
  .icon {
    font-size: 3rem;
  }
}

@media (min-width: 1024px) {
  /* Tamaño en pantallas grandes */
  .icon {
    font-size: 4rem;
  }
}
</style>

<template>
  <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div
      class="overflow-hidden sm:rounded-lg"
      :class="{
        'bg-gray-100  sm:rounded-lg': applyStyles,
        'bg-transparent shadow-none': !applyStyles,
      }"
    >
      <slot name="content">
        <div class="flex justify-center flex-wrap items-center">
          <div
            class="grid grid-cols-2 gap-4 md:grid-cols-2 lg:grid-cols-4 lg:w-full mx-auto place-items-center"
          >
            <Link
              :href="route('contracts', { expired: 'true' })"
              class="flex flex-col justify-between items-center bg-white rounded-3xl shadow-md hover:shadow-lg p-6 h-40 w-40 md:h-48 md:w-48"
            >
              <div
                class="flex items-center justify-center h-12 md:h-16 lg:h-20"
              >
                <span class="material-symbols-outlined text-blue-500">
                  person 
                </span>
              </div>
              <h2 class="text-xl md:text-3xl lg:text-4xl font-bold">
                <span>{{ morrosos.length }}</span>
              </h2>
              <p
                class="mt-2 text-center text-sm md:text-lg lg:text-xl text-gray-700"
              >
                Usuarios Deudores
              </p>
            </Link>

            <Link
              :href="route('contracts', { active: 1 })"
              class="flex flex-col justify-between items-center bg-white rounded-3xl shadow-md hover:shadow-lg p-6 h-40 w-40 md:h-48 md:w-48"
            >
              <div
                class="flex items-center justify-center h-12 md:h-16 lg:h-20"
              >
                <span
                  class="material-symbols-outlined text-blue-500 text-4xl md:text-5xl lg:text-6xl"
                >
                  contract
                </span>
              </div>
              <h2 class="text-xl md:text-3xl lg:text-4xl font-bold">
                <span>{{ activeContract.length }}</span>
              </h2>
              <p
                class="mt-2 text-center text-sm md:text-lg lg:text-xl text-gray-700"
              >
                Contratos Activos
              </p>
            </Link>

            <Link
              :href="route('tickets', { current: 'true' })"
              class="flex flex-col justify-between items-center bg-white rounded-3xl shadow-md hover:shadow-lg p-6 h-40 w-40 md:h-48 md:w-48"
            >
              <div
                class="flex items-center justify-center h-12 md:h-16 lg:h-20"
              >
                <span
                  class="material-symbols-outlined text-blue-500 text-4xl md:text-5xl lg:text-6xl"
                >
                  router
                </span>
              </div>
              <h2 class="text-xl md:text-3xl lg:text-4xl font-bold">
                <span>{{ new_tickets.length }}</span>
              </h2>
              <p
                class="mt-2 text-center text-sm md:text-lg lg:text-xl text-gray-700"
              >
                Cantidad de Tickets
              </p>
            </Link>

            <Link
              :href="route('usuarios')"
              class="flex flex-col justify-between items-center bg-white rounded-3xl shadow-md hover:shadow-lg p-6 h-40 w-40 md:h-48 md:w-48"
            >
              <div
                class="flex items-center justify-center h-12 md:h-16 lg:h-20"
              >
                <span
                  class="material-symbols-outlined text-blue-500 text-4xl md:text-5xl lg:text-6xl"
                >
                  account_circle
                </span>
              </div>
              <h2 class="text-xl md:text-3xl lg:text-4xl font-bold">
                <span>{{ currentUsers.length }}</span>
              </h2>
              <p
                class="mt-2 text-center text-sm md:text-lg lg:text-xl text-gray-700"
              >
                Total de Usuarios
              </p>
            </Link>
          </div>
        </div>

        <div class="mt-20 flex justify-center">
          <div
            class="pt-3 pb-3 pl-5 pr-5 rounded-md bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-gray-50"
          >
            <h2 class="text-2xl">Estadísticas de velocidad y consumo</h2>
          </div>
        </div>

        <div v-if="props.all_routers.length > 0" class="gap-2">
          <div
            class="flex justify-center items-center flex-row flex-wrap mt-4 gap-2"
          >
            <!-- {{ props.all_routers }} -->

            <button
              v-for="(item, index) in props.all_routers"
              @click="changeRouter(item)"
              :key="index"
              class="py-2 px-4 mb-1 rounded-md"
              :class="{
                'bg-blue-500 text-white': item.id === props.routers[0],
                'bg-gray-300 text-black': item.id !== props.routers[0],
              }"
            >
              {{ item.id }}
            </button>
          </div>
          <div class="gap-2 m-2 flex justify-end text-gray-600">
            Total de Routers Sincronizados
            <span class="font-bold text-md">
              {{ props.all_routers.length }}</span
            >
          </div>

          <div v-for="(targetItem, index) in target" :key="index">
            <div
              class="flex gap-1 mb-3 justify-center rounded-md bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-gray-50"
            >
              <h2 class="text-2xl">Router</h2>
              <span class="text-2xl">{{ routers[index] }}</span>
            </div>
            <div class="graphic-container">
              <Graphics
                :target="targetItem"
                :upload_rate="upload_rate[index]"
                :download_rate="download_rate[index]"
                :upload_byte="upload_byte[index]"
                :download_byte="download_byte[index]"
                :index="index"
              />
            </div>
          </div>
        </div>
        <div v-else>
          <h2>No hay routers disponibles</h2>
        </div>
      </slot>
    </div>
  </div>
</template>