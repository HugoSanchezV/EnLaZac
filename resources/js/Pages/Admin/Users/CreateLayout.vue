
<template>
  <div>
    <h2 class="flex justify-center border m-5 p-10">
      Selecciona el tipo de usuario que deseas crear
    </h2>
  </div>
  <div class="border p-14 m-5 rounded-md flex justify-center">
    <div>
      <Link href="" class="border py-2 px-3 rounded-l-md hover:bg-slate-200"
        >Cliente</Link>
      <Link class="border py-2 px-3 hover:bg-slate-200">Coordinador</Link>
      <Link class="border py-2 px-3 rounded-r-md hover:bg-slate-200"
        >Técnico</Link>
    </div>
  </div>
</template>


<script>
import { Link } from "@inertiajs/vue3";

export default {
  components: {
    Link,
  },
};
</script>