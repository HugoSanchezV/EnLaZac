<script setup>
import { computed } from "vue";
import AppLayoutAdmin from "@/Layouts/AppLayoutAdmin.vue";
import AppLayoutUser from "@/Layouts/AppLayoutUser.vue";
import LogoutOtherBrowserSessionsForm from "@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "@/Pages/Profile/Partials/UpdateProfileInformationForm.vue";

// Accediendo a las propiedades globales de la página
import { usePage } from "@inertiajs/vue3";

const { props } = usePage();

// Computar el layout según el tipo de usuario
const layoutComponent = computed(() => {
    return props.auth.user.admin === 1 ? AppLayoutAdmin : AppLayoutUser;
});

// Definir las propiedades que se reciben
defineProps({
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,
});
</script>

<template>
    <component :is="layoutComponent" title="Perfil">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Configura tu perfil
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                    <UpdateProfileInformationForm :user="$page.props.auth.user" />
                    <SectionBorder />
                </div>

                <div v-if="$page.props.jetstream.canUpdatePassword">
                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                    <SectionBorder />
                </div>

                <!-- Otros componentes -->
                <LogoutOtherBrowserSessionsForm :sessions="sessions" class="mt-10 sm:mt-0" />
            </div>
        </div>
    </component>
</template>
