<template>
  <div class="sidebar" :class="{ open: isOpen }" role="dialog" aria-modal="true">
    <div class="sidebar-header">
      <h2 class="sidebar-title">Your Orders</h2>
      <button @click="closeSidebar" class="close-btn" aria-label="Close Sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>
    <div class="sidebar-content">
      <div v-if="isLoading" class="loading-state">
        <p>Loading orders...</p>
      </div>
      <div v-else-if="orders.length === 0" class="empty-state">
        <p>No orders found.</p>
      </div>
      <div v-else class="orders-list">
        <div v-for="order in orders" :key="order.order_id" class="order-card">
          <h3 class="order-title">Order #{{ order.order_id }}</h3>
          <p class="order-total">Total: ${{ order.total_price }}</p>
          <div class="order-products">
            <div
              v-for="product in order.products"
              :key="product.product_id"
              class="product-item"
            >
              <img
                :src="'http://localhost:8000/public/uploads/' + product.image"
                alt="Product Image"
                class="product-image"
              />
              <div class="product-details">
                <h4 class="product-name">{{ product.name }}</h4>
                <p class="product-description">{{ product.description }}</p>
                <p class="product-price">Price: ${{ product.price }}</p>
                <p class="product-quantity">Quantity: {{ product.quantity }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  name: 'Sidebar',
  props: {
    isOpen: Boolean,
  },
  emits: ['close'],
  setup(_, { emit }) {
    const orders = ref([]);
    const isLoading = ref(false);

    const fetchOrders = async () => {
      try {
        isLoading.value = true;
        const userId = 1; // Replace with the actual user ID
        const response = await axios.get(`http://localhost:8000/orders/${userId}`);
        orders.value = response.data;
      } catch (error) {
        console.error('Failed to fetch orders:', error.message);
      } finally {
        isLoading.value = false;
      }
    };

    const closeSidebar = () => {
      emit('close');
    };

    onMounted(fetchOrders);

    return {
      orders,
      isLoading,
      closeSidebar,
    };
  },
};
</script>

<style scoped>
.sidebar {
  position: fixed;
  top: 0;
  right: -300px;
  width: 300px;
  height: 100%;
  background: #fff;
  box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
  transition: right 0.3s ease;
  overflow-y: auto;
  z-index: 1000;
}
.sidebar.open {
  right: 0;
}
.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f4f4f4;
  border-bottom: 1px solid #ddd;
}
.sidebar-title {
  font-size: 1.25rem;
  font-weight: bold;
}
.close-btn {
  background: none;
  border: none;
  cursor: pointer;
}
.sidebar-content {
  padding: 1rem;
}
.loading-state,
.empty-state {
  text-align: center;
  color: #666;
  font-size: 1rem;
}
.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}
.order-card {
  padding: 1rem;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #f9f9f9;
}
.order-title {
  font-size: 1.1rem;
  font-weight: bold;
  margin-bottom: 0.5rem;
}
.order-total {
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 1rem;
}
.order-products {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.product-item {
  display: flex;
  gap: 0.5rem;
  align-items: center;
}
.product-image {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
}
.product-details {
  flex: 1;
}
.product-name {
  font-size: 0.9rem;
  font-weight: bold;
}
.product-description {
  font-size: 0.8rem;
  color: #666;
}
.product-price,
.product-quantity {
  font-size: 0.8rem;
  color: #333;
}
</style>