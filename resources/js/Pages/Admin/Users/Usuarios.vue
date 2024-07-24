<template>
  <dashboard-base>
    <template v-slot:namePage>
        <div class="flex justify-between">
            <div>
                <h2>Usuarios</h2>
            </div>

            <div>
                <Link :href="route('usuarios.create')" method="get" class="text-white bg-cyan-500 hover:bg-cyan-600 py-2 px-3 text-sm">Crear Usuario</Link>
            </div>
        </div>
    </template>
    <template v-slot:content>
        <div>
            <div>
                <h2>Lista de usuarios</h2>
            </div>
        </div>
    </template>
  </dashboard-base>
</template>

<script>
import DashboardBase from '@/Pages/DashboardBase.vue';
import { Link } from '@inertiajs/vue3';

export default {
  components: {
    Link,
    DashboardBase,
  },
};
</script>