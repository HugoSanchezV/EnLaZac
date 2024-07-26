<template>
  <div v-if="links.length > 0" class="mt-5">
    <div class="flex justify-between -mb-1">
      <button
        @click="prevPage"
        :disabled="!pagination.prev_page_url"
        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded text-gray-600 hover:bg-gray-200 focus:border-indigo-500 focus:text-indigo-500"
      >
        Anterior
      </button>
      <div class="mr-1 mb-1 px-4 py-3 text-sm leading-4 text-gray-600 border rounded">
        Página {{ current }} de {{ total }}
      </div>
      <button
        @click="nextPage"
        :disabled="!pagination.next_page_url"
        class="mr-1 mb-1 px-4 py-3 text-sm leading-4 border rounded text-gray-600 hover:bg-gray-200 focus:border-indigo-500 focus:text-indigo-500"
      >
        Siguiente
      </button>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
  props: {
    links: {
      type: Array,
      required: true,
    },
    pagination: {
      type: Object,
      required: true,
    },
    current: {
      type: String,
    },
    
    total: {
      type: String,
    }
  },
  computed: {
    currentPage() {
      const activeLink = this.links.find(link => link.active);
      return activeLink ? parseInt(activeLink.label) : 1;
    },
    totalPages() {
      const lastPageLink = this.links.find(link => link.label.match(/^\d+$/));
      return lastPageLink ? parseInt(lastPageLink.label) : 1;
    },
  },
  methods: {
    prevPage() {
      if (this.pagination.prev_page_url) {
        this.$inertia.get(this.pagination.prev_page_url);
      }
    },
    nextPage() {
      if (this.pagination.next_page_url) {
        this.$inertia.get(this.pagination.next_page_url);
      }
    },
  },
};
</script>
