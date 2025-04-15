<template>
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
</template>

<script>
import { inject, computed } from 'vue';
import { useOrderStore } from '../stores/order.js';

export default {
  name: 'Navbar',
  setup() {
    const toggleSidebar = inject('toggleSidebar');
    const orderStore = useOrderStore();

    const orderCount = computed(() => orderStore.getOrder().products.length);

    return {
      toggleSidebar,
      orderCount,
    };
  },
};
</script>

<style scoped>
.coffee_link {
  background: transparent;
  border: none;
  box-shadow: none;
  padding: 0;
  position: relative;
  display: flex;
  align-items: center;
}

.coffee_icon {
  color: #fff;
  transition: color 0.3s ease;
}

.coffee_link:hover .coffee_icon {
  color: #b49383;
}

.coffee_count_badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: red;
  color: white;
  border-radius: 50%;
  font-size: 12px;
  padding: 2px 6px;
  line-height: 1;
}
</style>
