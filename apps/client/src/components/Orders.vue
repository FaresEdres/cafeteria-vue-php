<template>
  <div class="orders-container mx-auto p-6 max-w-7xl min-h-screen">
    <h1 class="text-4xl font-bold text-center mb-8 text-coffee-900 font-serif">My Orders</h1>

    <!-- Loading State -->
    <div v-if="isLoading" class="flex justify-center items-center py-20">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-coffee-600"></div>
      <span class="ml-4 text-coffee-700">Loading your orders...</span>
    </div>

    <!-- Empty State -->
    <div v-else-if="orders.length === 0" class="text-center py-16 bg-white rounded-xl shadow-md border border-coffee-100">
      <div class="w-40 h-40 mx-auto mb-6 rounded-full bg-coffee-50 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-coffee-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <h3 class="text-xl font-medium text-coffee-800 mb-2">No orders yet</h3>
      <p class="text-coffee-500 mb-6 max-w-md mx-auto">Your coffee adventures await! Browse our menu to place your first order.</p>
      <router-link to="/menu" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-coffee-600 to-coffee-800 text-white rounded-lg hover:shadow-md transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
        </svg>
        Explore Menu
      </router-link>
    </div>

    <!-- Orders List -->
    <div v-else class="space-y-6">
      <div v-for="order in orders" :key="order.order_id" class="order-card bg-white rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-coffee-100 overflow-hidden">
        <!-- Order Header -->
        <div class="bg-gradient-to-r from-coffee-50 to-coffee-100 px-6 py-5 border-b border-coffee-200">
          <div class="flex justify-between items-center">
            <div>
              <h2 class="text-xl font-semibold text-coffee-900">Order #{{ order.order_id }}</h2>
              <!-- <p class="text-sm text-coffee-500">{{ formatDate(order.created_at) }}</p> -->
            </div>
            <div class="flex items-center space-x-4">
              <span :class="{
                'px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide': true,
                'bg-amber-100 text-amber-800': order.status === 'processing',
                'bg-emerald-100 text-emerald-800': order.status === 'completed',
                'bg-rose-100 text-rose-800': order.status === 'cancelled',
                'bg-blue-100 text-blue-800': order.status === 'shipped'
              }">
                {{ order.status }}
              </span>
              <span class="text-xl font-bold text-coffee-800">${{ order.total_price }}</span>
            </div>
          </div>
        </div>

        <!-- Order Footer -->
        <div class="px-6 py-4 bg-coffee-50 border-t border-coffee-200 flex justify-end gap-3">
          <button 
            v-if="order.status === 'Processing'" 
            @click="selectOrder(order)" 
            class="px-4 py-2 bg-coffee-600 text-white rounded-lg hover:bg-coffee-700 transition-colors">
            Edit Order
          </button>
        </div>
      </div>
    </div>

    <!-- Order Edit Modal -->
    <!-- <OrderEditModal 
  v-if="selectedOrder" 
  :show="true" 
  :order="selectedOrder" 
  @close="closeEditModal" 
  @update="updateOrder"
/> -->
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useOrderStore } from '../stores/order.js';
import OrderEditModal from './OrderEditModal.vue';
import { postRequest, getRequest, deleteRequest } from "../services/httpClient.js";


export default {
  name: 'Orders',
  components: { OrderEditModal },
  setup() {
    const orders = ref([]);
    const isLoading = ref(false);
    const selectedOrder = ref(null);
    const orderStore = useOrderStore();

    const fetchOrders = async () => {
      try {
        isLoading.value = true;
        const user = await  postRequest('authenticated');
        console.log(user);
        const userId = user.id; // Replace with actual user ID
        const response = await axios.get(`http://localhost:8000/orders/${userId}`);
        orders.value = response.data;
        console.log(orders.value);

      } catch (error) {
        console.error('Failed to fetch orders:', error.message);
      } finally {
        isLoading.value = false;
      }
    };

    const selectOrder = async (order) => {
      console.log(order);
      if (order.status === 'Processing') {
        orderStore.addOrder(order);
        // await deleteRequest(`order/${order.order_id}`);
        
                
        selectedOrder.value = order;
      } else {
        alert('Only processing orders can be edited.');
      }
    };

    const closeEditModal = () => {
      selectedOrder.value = null;
    };

    const updateOrder = async (updatedOrder) => {
      try {
        await axios.patch(`http://localhost:8000/orders/${updatedOrder.order_id}`, updatedOrder);
        console.log(updatedOrder.order_id)
        await deleteRequest(`orders/${updatedOrder.order_id}`);
        await fetchOrders();
        closeEditModal();
      } catch (error) {
        console.error('Failed to update order:', error.message);
      }
    };

    onMounted(fetchOrders);

    return {
      orders,
      isLoading,
      selectedOrder,
      selectOrder,
      closeEditModal,
      updateOrder,
    };
  },
};
</script>

<style scoped>
/* Add your styles here */
</style>