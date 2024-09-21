<template>
    <Head>
      <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    </Head>
    <dashboard-base>
        
    </dashboard-base>
</template> 

<script>
import { Head } from '@inertiajs/vue3';
import DashboardBase from '../DashboardBase.vue';
export default {
    components: {
        DashboardBase,
        Head
    }
}
</script>