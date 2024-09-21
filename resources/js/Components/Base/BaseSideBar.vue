<template>
    <div class="flex flex-col h-screen w-64 bg-gray-50 border-r border-gray-200">
      <!-- Logo -->
      <div class="px-4 py-6">
        <div class="flex items-center space-x-2">
          <span class="text-purple-600 text-3xl font-bold">V</span>
          <span class="text-gray-800 text-xl font-bold">Vuexy</span>
        </div>
      </div>
  
      <!-- Dashboard -->
      <div class="px-4 py-2">
        <div class="flex items-center justify-between">
          <span class="text-sm font-medium text-gray-700">Dashboards</span>
          <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">5</span>
        </div>
        <ul class="mt-2 space-y-1">
          <li class="text-gray-700 cursor-pointer flex items-center space-x-2 hover:bg-gray-200 p-2 rounded-md">
            <span class="material-icons">analytics</span>
            <span>Analytics</span>
          </li>
          <li class="text-gray-700 cursor-pointer flex items-center space-x-2 hover:bg-gray-200 p-2 rounded-md">
            <span class="material-icons">dashboard</span>
            <span>CRM</span>
          </li>
        </ul>
      </div>
  
      <!-- Main Sections -->
      <div class="px-4 py-2">
        <span class="text-xs font-medium uppercase text-gray-500">Apps & Pages</span>
        <ul class="mt-2 space-y-2">
          <li>
            <button @click="toggleSection('ecommerce')" class="flex justify-between w-full text-left text-gray-700 hover:bg-gray-200 p-2 rounded-md">
              <div class="flex items-center space-x-2">
                <span class="material-icons">shopping_cart</span>
                <span>Ecommerce</span>
              </div>
              <span v-if="isOpen('ecommerce')" class="material-icons">expand_less</span>
              <span v-else class="material-icons">expand_more</span>
            </button>
            <transition name="slide-fade">
              <ul v-if="isOpen('ecommerce')" class="ml-8 mt-1 space-y-1">
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">Products</li>
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">Orders</li>
              </ul>
            </transition>
          </li>
          <li>
            <button @click="toggleSection('academy')" class="flex justify-between w-full text-left text-gray-700 hover:bg-gray-200 p-2 rounded-md">
              <div class="flex items-center space-x-2">
                <span class="material-icons">school</span>
                <span>Academy</span>
              </div>
              <span v-if="isOpen('academy')" class="material-icons">expand_less</span>
              <span v-else class="material-icons">expand_more</span>
            </button>
            <transition name="slide-fade">
              <ul v-if="isOpen('academy')" class="ml-8 mt-1 space-y-1">
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">Courses</li>
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">My Learning</li>
              </ul>
            </transition>
          </li>
          <li>
            <button @click="toggleSection('logistics')" class="flex justify-between w-full text-left text-gray-700 hover:bg-gray-200 p-2 rounded-md">
              <div class="flex items-center space-x-2">
                <span class="material-icons">local_shipping</span>
                <span>Logistics</span>
              </div>
              <span v-if="isOpen('logistics')" class="material-icons">expand_less</span>
              <span v-else class="material-icons">expand_more</span>
            </button>
            <transition name="slide-fade">
              <ul v-if="isOpen('logistics')" class="ml-8 mt-1 space-y-1">
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">Shipments</li>
                <li class="text-gray-500 cursor-pointer hover:text-gray-700">Tracking</li>
              </ul>
            </transition>
          </li>
        </ul>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        openSections: [],
      };
    },
    methods: {
      toggleSection(section) {
        if (this.openSections.includes(section)) {
          this.openSections = this.openSections.filter((s) => s !== section);
        } else {
          this.openSections.push(section);
        }
      },
      isOpen(section) {
        return this.openSections.includes(section);
      },
    },
  };
  </script>
  
  <style scoped>
  .material-icons {
    font-size: 20px;
  }
  
  .slide-fade-enter-active, .slide-fade-leave-active {
    transition: all 0.3s ease;
  }
  .slide-fade-enter, .slide-fade-leave-to /* .slide-fade-leave-active en Vue 2 */ {
    transform: translateY(-10px);
    opacity: 0;
  }
  </style>
  