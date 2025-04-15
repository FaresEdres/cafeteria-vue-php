<template>
  <div class="super_container">
    <header class="header">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="header_content d-flex flex-row align-items-center justify-content-start">
              <div class="logo">
                <a href="#">
                  <div class="armane">The Armané</div>
                  <div class="cafe">Café</div>
                </a>
              </div>
              <nav class="main_nav">
                <ul class="d-flex flex-row align-items-center justify-content-start">
                  <li><router-link to="/">Home</router-link></li>
                  <li><router-link to="/orders">My Orders</router-link></li>
                  <li><router-link to="/menu">Menu</router-link></li>
                  <li><router-link to="/contact">Contact</router-link></li>
                  <li><router-link to="/about">About Us</router-link></li>
                  <li><router-link to="/login">LogIN</router-link></li>
                </ul>
              </nav>
              <div class="coffee_icon_wrapper ml-auto position-relative">
                <button @click="toggleSidebar" class="coffee_link">
                  <i class="fas fa-mug-hot coffee_icon"></i>
                  <span class="coffee_count_badge">{{ orderCount }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Sidebar -->
    <div class="sidebar" :class="{ open: isSidebarOpen }">
      <div class="sidebar-header flex justify-between items-center p-4 bg-gray-800 text-white">
        <h2 class="text-lg font-bold">Order Form</h2>
        <button @click="toggleSidebar" class="text-gray-300 hover:text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      <div class="sidebar-content p-4">
        <OrderForm />
      </div>
    </div>
  </div>
</template>

<script>
import OrderForm from './OrderForm.vue';
import { useOrderStore } from '../stores/order.js';

export default {
  name: "HeaderComponent",
  components: {
    OrderForm,
  },
  data() {
    return {
      isSidebarOpen: false,
    };
  },
  computed: {
    orderCount() {
      const orderStore = useOrderStore();
      return orderStore.getOrder().products.length;
    },
  },
  methods: {
    toggleSidebar() {
      this.isSidebarOpen = !this.isSidebarOpen;
    },
  },
};
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  right: -400px;
  width: 400px;
  height: 100%;
  background: #ffffff;
  color: #333333;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.2);
  transition: right 0.3s ease;
  z-index: 1000;
  overflow-y: auto;
}

.sidebar-header {
  background: #2c3e50;
  color: white;
  padding: 1rem;
  border-bottom: 1px solid #34495e;
}

.sidebar.open {
  right: 0;
}

.sidebar-content {
  padding: 1rem;
  background: #ffffff;
}
</style>