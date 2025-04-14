<template>
    <div class="checks-container">
      <h2>Order Checks</h2>
      
      <!-- Date Range Filter -->
      <div class="filters">
        <div class="date-range">
          <label for="dateFrom">From:</label>
          <input type="date" id="dateFrom" v-model="filters.dateFrom" @change="fetchChecks">
          
          <label for="dateTo">To:</label>
          <input type="date" id="dateTo" v-model="filters.dateTo" @change="fetchChecks">
        </div>
        
        <!-- User Filter -->
        <div class="user-filter">
          <label for="userSelect">Filter by User:</label>
          <select id="userSelect" v-model="filters.userId" @change="fetchChecks">
            <option value="">All Users</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} (Room: {{ user.room }})
            </option>
          </select>
        </div>
      </div>
      
      <!-- Loading State -->
      <div v-if="loading" class="loading">Loading...</div>
      
      <!-- Error Message -->
      <div v-if="error" class="error">{{ error }}</div>
      
      <!-- User Summary Table -->
      <div v-if="summary.length > 0" class="summary-section">
        <h3>User Summary</h3>
        <table class="summary-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in summary" :key="item.user_id">
              <td>{{ item.user_name }}</td>
              <td>{{ item.total_amount }} EGP</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Order Details Table -->
      <div v-if="orders.length > 0" class="orders-section">
        <h3>Order Details</h3>
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order Date</th>
              <th>User</th>
              <th>Room</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id">
              <td>{{ formatDate(order.order_date) }}</td>
              <td>{{ order.user_name }}</td>
              <td>{{ order.room }}</td>
              <td>{{ order.total_price }} EGP</td>
              <td>
                <select v-model="order.status" @change="updateOrderStatus(order)">
                  <option value="processing">Processing</option>
                  <option value="out_for_delivery">Out for Delivery</option>
                  <option value="done">Done</option>
                </select>
              </td>
              <td>
                <button @click="viewOrderDetails(order)">View Details</button>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination" v-if="totalPages > 1">
          <button 
            @click="changePage(currentPage - 1)" 
            :disabled="currentPage === 1"
          >
            &lt;
          </button>
          
          <span 
            v-for="page in visiblePages" 
            :key="page"
            @click="changePage(page)"
            :class="{ active: page === currentPage }"
          >
            {{ page }}
          </span>
          
          <button 
            @click="changePage(currentPage + 1)" 
            :disabled="currentPage === totalPages"
          >
            &gt;
          </button>
        </div>
      </div>
      
      <div v-else-if="!loading" class="no-orders">
        No orders found for the selected criteria.
      </div>
      
      <!-- Order Details Modal -->
      <div v-if="selectedOrder" class="modal">
        <div class="modal-content">
          <span class="close" @click="selectedOrder = null">&times;</span>
          <h3>Order Details - {{ formatDate(selectedOrder.order_date) }}</h3>
          <p><strong>User:</strong> {{ selectedOrder.user_name }}</p>
          <p><strong>Room:</strong> {{ selectedOrder.room }}</p>
          <p><strong>Total:</strong> {{ selectedOrder.total_price }} EGP</p>
          <p><strong>Status:</strong> {{ selectedOrder.status }}</p>
          <p><strong>Comment:</strong> {{ selectedOrder.comment || 'None' }}</p>
          
          <h4>Products:</h4>
          <table class="products-table">
            <thead>
              <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in selectedOrder.products" :key="product.product_id">
                <td>{{ product.name }}</td>
                <td>{{ product.quantity }}</td>
                <td>{{ product.price }} EGP</td>
                <td>{{ (product.price * product.quantity).toFixed(2) }} EGP</td>
              </tr>
            </tbody>
          </table>
          
          <button v-if="selectedOrder.status === 'processing'" 
                  @click="cancelOrder(selectedOrder)" 
                  class="cancel-btn">
            Cancel Order
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, computed } from 'vue';
  import { getRequest, patchRequest } from '../services/httpClient';
  
  export default {
    setup() {
      // State management
      const filters = ref({
        dateFrom: '',
        dateTo: '',
        userId: ''
      });
      
      const users = ref([]);
      const orders = ref([]);
      const summary = ref([]);
      const loading = ref(false);
      const error = ref('');
      const selectedOrder = ref(null);
      const currentPage = ref(1);
      const itemsPerPage = 10;
      const totalOrders = ref(0);
  
      // Computed properties
      const totalPages = computed(() => Math.ceil(totalOrders.value / itemsPerPage));
      
      const visiblePages = computed(() => {
        const pages = [];
        const maxVisible = 5;
        let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages.value, start + maxVisible - 1);
        
        if (end - start + 1 < maxVisible) {
          start = Math.max(1, end - maxVisible + 1);
        }
        
        for (let i = start; i <= end; i++) {
          pages.push(i);
        }
        
        return pages;
      });
  
      // Methods
      const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
      };
  
      const setDefaultDates = () => {
        const today = new Date();
        const lastWeek = new Date();
        lastWeek.setDate(today.getDate() - 7);
        
        filters.value.dateFrom = lastWeek.toISOString().split('T')[0];
        filters.value.dateTo = today.toISOString().split('T')[0];
      };
  
      const fetchUsers = async () => {
        try {
          const response = await getRequest('users');
          users.value = response || [];
        } catch (err) {
          error.value = 'Failed to fetch users: ' + (err.message || err);
        }
      };
  
      const fetchChecks = async () => {
        loading.value = true;
        error.value = '';
        
        try {
          const params = {
            page: currentPage.value,
            limit: itemsPerPage,
            ...(filters.value.dateFrom && { dateFrom: filters.value.dateFrom }),
            ...(filters.value.dateTo && { dateTo: filters.value.dateTo }),
            ...(filters.value.userId && { userId: filters.value.userId })
          };
  
          const response = await getRequest('orders/', params);
          
          orders.value = response.orders || [];
          summary.value = response.summary || [];
          totalOrders.value = response.total || 0;
          
        } catch (err) {
          error.value = 'Failed to fetch orders: ' + (err.message || err);
          orders.value = [];
          summary.value = [];
        } finally {
          loading.value = false;
        }
      };
  
      const viewOrderDetails = (order) => {
        selectedOrder.value = order;
      };
  
      const updateOrderStatus = async (order) => {
        try {
          await patchRequest(`orders/${order.id}`, { status: order.status });
        } catch (err) {
          error.value = 'Failed to update order status: ' + (err.message || err);
          fetchChecks(); // Refresh data to revert any UI changes
        }
      };
  
      const cancelOrder = async (order) => {
        if (confirm('Are you sure you want to cancel this order?')) {
          try {
            await patchRequest(`orders/${order.id}`, { status: 'cancelled' });
            selectedOrder.value = null;
            fetchChecks();
          } catch (err) {
            error.value = 'Failed to cancel order: ' + (err.message || err);
          }
        }
      };
  
      const changePage = (page) => {
        if (page >= 1 && page <= totalPages.value) {
          currentPage.value = page;
          fetchChecks();
        }
      };
  
      // Lifecycle hooks
      onMounted(() => {
        setDefaultDates();
        fetchUsers();
        fetchChecks();
      });
  
      return {
        filters,
        users,
        orders,
        summary,
        loading,
        error,
        selectedOrder,
        currentPage,
        totalPages,
        visiblePages,
        formatDate,
        fetchChecks,
        viewOrderDetails,
        updateOrderStatus,
        cancelOrder,
        changePage
      };
    }
  };
  </script>
  
  <style scoped>
  /* (Keep all your existing styles - they're good!) */
  .checks-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .filters {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
    padding: 15px;
    background: #f5f5f5;
    border-radius: 5px;
  }
  
  .date-range, .user-filter {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  label {
    font-weight: bold;
  }
  
  input[type="date"], select {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
  }
  
  th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
  }
  
  th {
    background-color: #f2f2f2;
    font-weight: bold;
  }
  
  tr:hover {
    background-color: #f5f5f5;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
  }
  
  .pagination button, .pagination span {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    cursor: pointer;
  }
  
  .pagination span.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
  }
  
  .pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  
  .modal-content {
    background: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
  }
  
  .close {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 24px;
    cursor: pointer;
  }
  
  .products-table th, .products-table td {
    padding: 8px;
  }
  
  .cancel-btn {
    background: #dc3545;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 15px;
  }
  
  .cancel-btn:hover {
    background: #c82333;
  }
  
  .loading, .error, .no-orders {
    padding: 20px;
    text-align: center;
    font-size: 18px;
  }
  
  .error {
    color: #dc3545;
  }
  </style>