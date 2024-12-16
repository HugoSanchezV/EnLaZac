<script>
import DashboardBase from "@/Pages/DashboardBase.vue";
import CardInformation from "../Admin/Users/CardInformation.vue";
import CardInformationUser from "../Coordi/Users/CardInformationUser.vue";

export default {
  components: {
    DashboardBase,
    CardInformation,
    CardInformationUser,
  },
  props: {
    user: {
      type: Object,
      required: true,
    },
    ticket: {
      type: Number,
      required: true,
    },
    contracts: {
      type: Array,
      required: true,
    },
    mapKey:{
      type: String,
      required: true,
    }
  },
};
</script>

<template>
  <dashboard-base :applyStyles="false">
    <template v-slot:namePage>
      <div class="flex justify-between">
        <div>
          <h2>Inicio</h2>
        </div>
      </div>
    </template>
    <template v-slot:content>
      <!-- <div>
        <div class="flex justify-center flex-row flex-wrap gap-2 md:gap-8 mb-4">
          <div class="flex gap-2 md:gap-8">
            <Link
              :href="route('tickets')"
              class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
            >
              <div class="h-12">
                <span
                  class="material-symbols-outlined text-blue-500"
                  style="font-size: 2rem"
                  >confirmation_number</span
                >
              </div>
              <div class="my-2">
                <h2 class="text-4xl font-bold">
                  <span>2</span>
                </h2>
              </div>
              <div>
                <p class="mt-2 font-sans text-base font-medium text-gray-700">
                  Tickets Asignados
                </p>
              </div>
            </Link>

            <Link
              :href="route('technical.device.ping.historie')"
              class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
            >
              <div class="h-12">
                <span
                  class="material-symbols-outlined text-blue-500"
                  style="font-size: 2rem"
                  >network_ping</span
                >
              </div>
              <div class="my-2">
                <h2 class="text-4xl font-bold">
                  <span>3</span>
                </h2>
              </div>
              <div>
                <p class="mt-2 font-sans text-base font-medium text-gray-700">
                  Pings Asignados
                </p>
              </div>
            </Link>
          </div>

          <div class="flex gap-2 md:gap-8">
            <Link
              :href="route('technical.routers')"
              class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
            >
              <div class="h-12">
                <span
                  class="material-symbols-outlined text-blue-500"
                  style="font-size: 2rem"
                  >router</span
                >
              </div>
              <div class="my-2">
                <h2 class="text-4xl font-bold">
                  <span>4</span>
                </h2>
              </div>
              <div>
                <p class="mt-2 font-sans text-base font-medium text-gray-700">
                  Total de routers
                </p>
              </div>
            </Link>

            <Link
              :href="route('technical.devices')"
              class="block text-center bg-white rounded-3xl shadow-md hover:shadow-lg p-8"
            >
              <div class="h-12">
                <span
                  class="material-symbols-outlined text-blue-500"
                  style="font-size: 2rem"
                  >nest_wifi_router</span
                >
              </div>
              <div class="my-2">
                <h2 class="text-4xl font-bold">
                  <span>6</span>
                </h2>
              </div>
              <div>
                <p class="mt-2 font-sans text-base font-medium text-gray-700">
                  Dispositvos Act.
                </p>
              </div>
            </Link>
          </div>
        </div>
      </div> -->

      <div class="p-2 rounded-lg w-full">
        <CardInformationUser :user="user" />
        <div v-for="(item, index) in contracts" :key="index" class="mt-2">
          <CardInformation
            :device="item.device"
            :contract="item.contract"
            :plan="item.contract.plan"
            :mapKey="mapKey"
          />
        </div>
      </div>
    </template>
  </dashboard-base>
</template>

