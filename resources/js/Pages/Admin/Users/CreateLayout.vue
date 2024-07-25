
<template>
  
  <div class="border p-14 m-5 rounded-md flex justify-center">
    <div class="">
      <div>
        <register-admin></register-admin>
      </div>
    </div>
  </div>
</template>


<script>
import { Link } from "@inertiajs/vue3";
import RegisterAdmin from "@/Pages/Auth/RegisterAdmin.vue";

export default {
  components: {
    Link,
    RegisterAdmin,
  },
};
</script>