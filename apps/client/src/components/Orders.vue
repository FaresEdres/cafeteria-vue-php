<template>
    <div class="orders-container mx-auto p-6 max-w-4xl">
      <h1 class="text-3xl font-bold text-center mb-6">Your Orders</h1>
      <div v-if="isLoading" class="text-center">
        <p class="text-lg">Loading orders...</p>
      </div>
      <div v-else-if="orders.length === 0" class="text-center">
        <p class="text-lg text-gray-500">No orders found.</p>
      </div>
      <div v-else>
        <div v-for="order in orders" :key="order.order_id" class="order-card bg-white shadow-md rounded-lg p-4 mb-6">
          <div class="order-header flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Order #{{ order.order_id }}</h2>
            <p class="text-gray-600">Total: ${{ order.total_price }}</p>
          </div>
          <div class="order-products grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="product in order.products" :key="product.product_id" class="product-card flex items-center bg-gray-100 p-3 rounded-lg">
              <img :src="'http://localhost:8000/public/uploads/' + product.image" alt="Product Image" class="w-16 h-16 rounded-lg object-cover mr-4">
              <div>
                <h3 class="text-lg font-medium">{{ product.name }}</h3>
                <p class="text-sm text-gray-500">{{ product.description }}</p>
                <p class="text-sm text-gray-700">Price: ${{ product.price }}</p>
                <p class="text-sm text-gray-700">Quantity: {{ product.quantity }}</p>
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
    name: 'Orders',
    setup() {
      const orders = ref([]);
      const isLoading = ref(false);
  
      const fetchOrders = async () => {
        try {
          isLoading.value = true;
          const userId = 1; // Replace with the actual user ID
          const response = await axios.get(`http://localhost:8000/orders/${userId}`);
          orders.value = response.data;
          console.log('Fetched orders:', orders.value);
        } catch (error) {
          console.error('Failed to fetch orders:', error.message);
        } finally {
          isLoading.value = false;
        }
      };
  
      onMounted(fetchOrders);
  
      return {
        orders,
        isLoading,
      };
    },
  };
  </script>
  
  <style scoped>
  .orders-container {
    background-color: #f9fafb;
    border-radius: 8px;
  }
  
  .order-card {
    border: 1px solid #e5e7eb;
  }
  
  .product-card img {
    border: 1px solid #e5e7eb;
  }
  </style>