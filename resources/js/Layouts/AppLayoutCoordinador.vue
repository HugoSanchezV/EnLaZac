<script setup>
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import SidebarCoordinador from "./SidebarCoordinador.vue";
import BaseSearchHeaderTechnical from "@/Components/BaseSearchHeaderTechnical.vue";
defineProps({
  title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
  router.put(
    route("current-team.update"),
    {
      team_id: team.id,
    },
    {
      preserveState: false,
    }
  );
};
const logout = () => {
  router.post(route("logout"));
};

const emit = defineEmits(["cambio"]);
const holaRef = ref(null);
const menuIsOpen = ref(false);

const hello = () => {
  alert("hola");
};
const closeMenu = () => {
  menuIsOpen.value = false;
};

const openMenu = () => {
  menuIsOpen.value = true;
};
const formattedDate = (dateCreation) => {
  // Convertimos la fecha ISO a un objeto Date
  const date = new Date(dateCreation);

  // Formateamos como "DD/MM/YYYY HH:mm"
  const formattedDate =
    date.toLocaleDateString("en-GB") +
    " " +
    date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
  // alert("");
  return formattedDate;
};
</script>

<template>
  <div class="flex h-screen">
    <div
      class="lg:block top-0 left-0 w-auto min-w-[200px] h-screen z-50 bg-white shadow-2xl"
      :class="{ 'block fixed': menuIsOpen, hidden: !menuIsOpen }"
    >
      <sidebar-coordinador @close-menu-emit="closeMenu"></sidebar-coordinador>
    </div>
    <div class="flex-grow bg-gray-100">
      <Head :title="title" />

      <Banner />

      <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
          <!-- Primary Navigation Menu -->
          <div
            class="max-w-3xl lg:max-w-7xl md:max-w-5xl start lg:mx-auto px-4 sm:px-6 lg:px-8"
          >
            <div class="flex justify-between h-16">
              <div class="hidden lg:block">
                <base-search-header-technical></base-search-header-technical>
              </div>

              <div class="lg:hidden flex items-center">
                <button
                  class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                  @click="openMenu"
                >
                  <svg
                    class="h-6 w-6"
                    stroke="currentColor"
                    fill="none"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"
                    />
                  </svg>
                </button>
              </div>

              <div class="flex items-center sm:ms-6">
                <div class="ms-3 relative">
                  <!-- Teams Dropdown -->
                  <Dropdown
                    v-if="$page.props.jetstream.hasTeamFeatures"
                    align="right"
                    width="60"
                  >
                    <template #trigger>
                      <span class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                        >
                          {{ $page.props.auth.user.current_team.name }}

                          <svg
                            class="ms-2 -me-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <div class="w-60">
                        <!-- Team Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                          Manage Team
                        </div>

                        <!-- Team Settings -->
                        <DropdownLink
                          :href="
                            route(
                              'teams.show',
                              $page.props.auth.user.current_team
                            )
                          "
                        >
                          Team Settings
                        </DropdownLink>

                        <DropdownLink
                          v-if="$page.props.jetstream.canCreateTeams"
                          :href="route('teams.create')"
                        >
                          Create New Team
                        </DropdownLink>

                        <!-- Team Switcher -->
                        <template
                          v-if="$page.props.auth.user.all_teams.length > 1"
                        >
                          <div class="border-t border-gray-200" />

                          <div class="block px-4 py-2 text-xs text-gray-400">
                            Switch Teams
                          </div>

                          <template
                            v-for="team in $page.props.auth.user.all_teams"
                            :key="team.id"
                          >
                            <form @submit.prevent="switchToTeam(team)">
                              <DropdownLink as="button">
                                <div class="flex items-center">
                                  <svg
                                    v-if="
                                      team.id ==
                                      $page.props.auth.user.current_team_id
                                    "
                                    class="me-2 h-5 w-5 text-green-400"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                  >
                                    <path
                                      stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                  </svg>

                                  <div>{{ team.name }}</div>
                                </div>
                              </DropdownLink>
                            </form>
                          </template>
                        </template>
                      </div>
                    </template>
                  </Dropdown>
                </div>
                <!-- Notifications-->
                <div class="ms-3 relative">
                  <div class="notification-dropdown">
                    <button class="notification-button" @click="toggleDropdown">
                      <i class="fas fa-bell"></i>
                      <span
                        v-if="unreadNotifications.length > 0"
                        class="notification-count"
                      >
                        {{ unreadNotifications.length }}
                      </span>
                    </button>
                    <div
                      v-if="dropdownOpen"
                      id="dropdown-content"
                      class="dropdown-content"
                    >
                      <div class="dropdown-header">
                        Notificaciones no leídas
                      </div>
                      <div
                        v-if="unreadNotifications.length > 0"
                        class="notifications-container"
                      >
                        <div
                          v-for="notification in unreadNotifications"
                          :key="notification.id"
                        >
                          <div
                            v-if="
                              notification.type ==
                              'App\\Notifications\\TicketNotification'
                            "
                          >
                            <Link
                              :href="
                                route('tickets.show', notification.data.id)
                              "
                              class="dropdown-item"
                              @click.prevent="
                                handleNotificationClick(notification)
                              "
                            >
                              <i class="fas fa-envelope"></i> Nuevo ticket No.
                              {{ notification.data.id }}
                              <span class="time">{{
                                notification.created_at
                              }}</span>
                            </Link>
                          </div>
                          <div
                            v-else-if="
                              notification.type ==
                              'App\\Notifications\\RouterDiagnosisNotification'
                            "
                          >
                            <Link
                              :href="route('routers')"
                              class="dropdown-item"
                              @click.prevent="
                                handleNotificationClick(notification)
                              "
                            >
                              <i class="fas fa-envelope"></i> Ping:
                              {{ notification.data.message }}
                              <span class="time">{{
                                formattedDate(notification.created_at)
                              }}</span>
                            </Link>
                          </div>
                          <div
                            v-else-if="
                              notification.type ==
                              'App\\Notifications\\PingTecnicoNotification'
                            "
                          >
                            <Link
                              :href="route('devices')"
                              class="dropdown-item"
                              @click.prevent="
                                handleNotificationClick(notification)
                              "
                            >
                              <i class="fas fa-envelope"></i>
                              Dispositivo {{ notification.data.device_id }} con
                              problemas
                              <span class="time">{{
                                formattedDate(notification.created_at)
                              }}</span>
                            </Link>
                          </div>
                          <div v-else>
                            <Link
                              :href="
                                route('usuarios.show', notification.data.id)
                              "
                              class="dropdown-item"
                              @click.prevent="
                                handleNotificationClick(notification)
                              "
                            >
                              <i class="fas fa-envelope"></i> Nuevo usuario
                              registrado id {{ notification.data.id }}
                              <span class="time">{{
                                notification.created_at
                              }}</span>
                            </Link>
                          </div>
                        </div>
                      </div>
                      <div class="pl-3 mt-3 mb-3" v-else>
                        <p>No tienes notificaciones no leídas</p>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Settings Dropdown -->
                <div class="ms-3 relative">
                  <Dropdown align="right" width="48">
                    <template #trigger>
                      <span
                        class="bg-slate-500 text-gray-50 rounded-md py-1 px-2"
                        >Coordinador</span
                      >
                      <button
                        v-if="$page.props.jetstream.managesProfilePhotos"
                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                      >
                        <img
                          class="h-8 w-8 rounded-full object-cover"
                          :src="$page.props.auth.user.profile_photo_url"
                          :alt="$page.props.auth.user.name"
                        />
                      </button>
                      <span v-else class="inline-flex rounded-md">
                        <button
                          type="button"
                          class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                        >
                          {{ $page.props.auth.user.name }}

                          <svg
                            class="ms-2 -me-0.5 h-4 w-4"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                          >
                            <path
                              stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                            />
                          </svg>
                        </button>
                      </span>
                    </template>

                    <template #content>
                      <!-- Account Management -->
                      <!-- <div class="block px-4 py-2 text-xs text-gray-400">
                        Administrar Cuenta
                      </div> -->

                      <!-- <DropdownLink :href="route('profile.show')">
                        Perfil
                      </DropdownLink> -->

                      <DropdownLink
                        v-if="$page.props.jetstream.hasApiFeatures"
                        :href="route('api-tokens.index')"
                      >
                        API Tokens
                      </DropdownLink>

                      <div class="border-t border-gray-200" />

                      <!-- Authentication -->
                      <form @submit.prevent="logout">
                        <DropdownLink as="button"> Cerrar Sesión </DropdownLink>
                      </form>
                    </template>
                  </Dropdown>
                </div>
              </div>
            </div>
          </div>

          <!-- Responsive Navigation Menu -->
          <div
            :class="{
              block: showingNavigationDropdown,
              hidden: !showingNavigationDropdown,
            }"
            class="sm:hidden"
          >
            <div class="pt-2 pb-3 space-y-1">
              <ResponsiveNavLink
                :href="route('dashboard')"
                :active="route().current('dashboard')"
              >
                Dashboard
              </ResponsiveNavLink>

              <ResponsiveNavLink
                :href="route('usuarios')"
                :active="route().current('usuarios')"
              >
                usuarios
              </ResponsiveNavLink>
            </div>

            <!-- Responsive Settings Options -->
            <div class="pt-4 pb-1 border-t border-gray-200">
              <div class="flex items-center px-4">
                <div
                  v-if="$page.props.jetstream.managesProfilePhotos"
                  class="shrink-0 me-3"
                >
                  <img
                    class="h-10 w-10 rounded-full object-cover"
                    :src="$page.props.auth.user.profile_photo_url"
                    :alt="$page.props.auth.user.name"
                  />
                </div>

                <div>
                  <div class="font-medium text-base text-gray-800">
                    {{ $page.props.auth.user.name }}
                  </div>
                  <div class="font-medium text-sm text-gray-500">
                    {{ $page.props.auth.user.email }}
                  </div>
                </div>
              </div>

              <div class="mt-3 space-y-1">
                <ResponsiveNavLink
                  :href="route('profile.show')"
                  :active="route().current('profile.show')"
                >
                  Perfil
                </ResponsiveNavLink>

                <ResponsiveNavLink
                  v-if="$page.props.jetstream.hasApiFeatures"
                  :href="route('api-tokens.index')"
                  :active="route().current('api-tokens.index')"
                >
                  API Tokens
                </ResponsiveNavLink>

                <!-- Authentication -->
                <form method="POST" @submit.prevent="logout">
                  <ResponsiveNavLink as="button">
                    Cerrar Sesión
                  </ResponsiveNavLink>
                </form>

                <!-- Team Management -->
                <template v-if="$page.props.jetstream.hasTeamFeatures">
                  <div class="border-t border-gray-200" />

                  <div class="block px-4 py-2 text-xs text-gray-400">
                    Manage Team
                  </div>

                  <!-- Team Settings -->
                  <ResponsiveNavLink
                    :href="
                      route('teams.show', $page.props.auth.user.current_team)
                    "
                    :active="route().current('teams.show')"
                  >
                    Team Settings
                  </ResponsiveNavLink>

                  <ResponsiveNavLink
                    v-if="$page.props.jetstream.canCreateTeams"
                    :href="route('teams.create')"
                    :active="route().current('teams.create')"
                  >
                    Create New Team
                  </ResponsiveNavLink>

                  <!-- Team Switcher -->
                  <template v-if="$page.props.auth.user.all_teams.length > 1">
                    <div class="border-t border-gray-200" />

                    <div class="block px-4 py-2 text-xs text-gray-400">
                      Switch Teams
                    </div>

                    <template
                      v-for="team in $page.props.auth.user.all_teams"
                      :key="team.id"
                    >
                      <form @submit.prevent="switchToTeam(team)">
                        <ResponsiveNavLink as="button">
                          <div class="flex items-center">
                            <svg
                              v-if="
                                team.id == $page.props.auth.user.current_team_id
                              "
                              class="me-2 h-5 w-5 text-green-400"
                              xmlns="http://www.w3.org/2000/svg"
                              fill="none"
                              viewBox="0 0 24 24"
                              stroke-width="1.5"
                              stroke="currentColor"
                            >
                              <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              />
                            </svg>
                            <div>{{ team.name }}</div>
                          </div>
                        </ResponsiveNavLink>
                      </form>
                    </template>
                  </template>
                </template>
              </div>
            </div>
          </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white shadow">
          <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <BaseButtonBack class="mb-1"></BaseButtonBack>
            <slot name="header" />
          </div>
        </header>

        <!-- Page Content -->
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  data() {
    return {
      unreadNotifications: [],
      dropdownOpen: false,
    };
  },
  methods: {
    toggleDropdown() {
      this.dropdownOpen = !this.dropdownOpen;
      if (this.dropdownOpen) {
        this.fetchUnreadNotifications();
      }
    },
    fetchUnreadNotifications() {
      axios
        .get("/notifications/unread")
        .then((response) => {
          this.unreadNotifications = response.data;
        })
        .catch((error) => {
          console.error("Error al obtener las notificaciones:", error);
        });
    },
    handleNotificationClick(notification) {
      this.markAsRead(notification.id, notification.data.id);
    },
    markAsRead(notificationId, ticketID) {
      axios
        .post(`/notifications/read/${notificationId}`)
        .then((response) => {
          if (response.data.status === "success") {
            this.unreadNotifications = this.unreadNotifications.filter(
              (notification) => notification.id !== notificationId
            );
          }
        })
        .catch((error) => {
          console.error("Error al marcar la notificación como leída:", error);
        });
    },
  },
  mounted() {
    this.fetchUnreadNotifications(); // Cargar notificaciones al montar el componente
  },
  filters: {
    timeAgo(value) {
      const date = new Date(value);
      return date.toLocaleDateString() + " " + date.toLocaleTimeString();
    },
  },
};
</script>