<template>
  <div class="super_container">
    <header class="header">
      <div class="container">
        <div class="row">
          <div class="col">
            <!-- Navbar Component -->
            <Navbar />
          </div>
        </div>
      </div>

      <!-- Home Section -->
      <div class="home_h" :style="{ height: backgroundHeight }">
        <div class="parallax_background" :style="{ backgroundImage: `url(${backgroundImage})` }"></div>
        <div class="home_container">
          <div class="container">
            <div class="row">
              <div class="col">
                <div class="home_content text-center">
                  <div class="home_subtitle page_subtitle">{{ subtitle }}</div>
                  <div class="home_title">
                    <h1>{{ title }}</h1>
                  </div>
                  <div class="home_text ml-auto mr-auto">
                    <p>{{ description }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="scroll_icon"></div>
      </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" :class="{ open: isSidebarOpen }">
      <div class="sidebar-header flex justify-between items-center px-6 py-4 bg-[#b49383] text-white shadow-md">
        <h2 class="text-xl font-semibold tracking-wide armane">Order Form</h2>
        <button @click="toggleSidebar" class="hover:text-white transition duration-300">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div class="sidebar-content p-6 space-y-6 overflow-y-auto">
        <OrderForm />
        <router-link to="/"
          class="mt-8 block w-full text-center px-6 py-3 bg-[#b49383] hover:bg-[#a17d6f] text-white font-semibold rounded-full shadow-lg transition-all duration-300">
          ← Back to Home
        </router-link>
      </div>
    </div>

  </div>
</template>

<script>
import { provide, computed, ref } from 'vue';
import { useRoute } from 'vue-router';
import { useOrderStore } from '../stores/order.js';
import Navbar from './NavBar.vue';
import OrderForm from './OrderForm.vue';

export default {
  name: 'HeaderComponent',
  components: {
    Navbar,
    OrderForm,
  },
  setup() {
    const route = useRoute();
    const orderStore = useOrderStore();
    const isSidebarOpen = ref(false);

    const toggleSidebar = () => {
      isSidebarOpen.value = !isSidebarOpen.value;
    };

    // Provide the toggleSidebar function for Navbar to use
    provide('toggleSidebar', toggleSidebar);

    const pageData = computed(() => {
      switch (route.path) {
        case '/':
          return {
            background: 'images/home.jpg',
            title: 'An Extraordinary Experience',
            subtitle: 'The Armané Café is',
            description: 'Welcome to The Armané Café, where rich aromas, handcrafted flavors, and a cozy atmosphere come together...',
            height: '100vh',
          };
        default:
          return { height: '0vh' };
      }
    });

    const backgroundImage = computed(() => pageData.value.background);
    const title = computed(() => pageData.value.title);
    const subtitle = computed(() => pageData.value.subtitle);
    const description = computed(() => pageData.value.description);
    const backgroundHeight = computed(() => pageData.value.height);

    return {
      isSidebarOpen,
      toggleSidebar,
      backgroundImage,
      title,
      subtitle,
      description,
      backgroundHeight,
    };
  },
};
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  width: 100%;
  z-index: 100;
  background-color: rgba(0, 0, 0, 0.5);
}

.home_h {
  position: relative;
  width: 100%;
  overflow: hidden;
  transition: height 0.4s ease;
}

.parallax_background {
  background-size: cover;
  background-position: center;
  position: absolute;
  width: 100%;
  height: 100%;
  z-index: -1;
}

.page_subtitle {
  font-family: 'Dancing Script', cursive;
  color: #b49383;
}

.home_title h1 {
  font-size: 48px;
  color: white;
}

.home_text p {
  font-size: 18px;
  color: #f2f2f2;
  max-width: 600px;
  margin: 0 auto;
}

.sidebar {
  position: fixed;
  top: 0;
  right: -100%;
  width: 400px;
  max-width: 90%;
  height: 100%;
  background: #fff;
  transition: right 0.4s ease;
  z-index: 1000;
  box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  border-left: 5px solid #b49383;
}

.sidebar.open {
  right: 0;
}

.sidebar-header {
  background: #b49383;
  color: white;
  font-family: 'Dancing Script', cursive;
}

.sidebar-content {
  flex-grow: 1;
  overflow-y: auto;
  background-color: #fdfaf7;
  border-top: 1px solid #e5e5e5;
  padding-bottom: 2rem;
}

.router-link-exact-active {
  font-weight: bold;
}
</style>
