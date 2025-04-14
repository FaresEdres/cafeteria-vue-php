<template>
    <div class="admin-checks-container">
      <h2>Checks</h2>
      
      <!-- Date Range Filter -->
      <div class="filters">
        <div class="date-range">
          <label>Date from</label>
          <input type="date" v-model="dateFrom" @change="fetchOrders">
          
          <label>Date to</label>
          <input type="date" v-model="dateTo" @change="fetchOrders">
        </div>
        
        <div class="user-filter">
          <label>User</label>
          <select v-model="selectedUserId" @change="fetchOrders">
            <option value="">All Users</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.firstname }} {{ user.lastname }}
            </option>
          </select>
        </div>
      </div>
      
      <!-- Users Summary Section -->
      <div class="users-summary" v-if="selectedUserId === ''">
        <h3>Users Summary</h3>
        <table class="summary-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="summary in usersSummary" :key="summary.user_id">
              <td>{{ summary.user_name }}</td>
              <td>{{ summary.total_amount }} EGP</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Orders Details Section -->
      <div class="orders-details">
        <h3>Orders</h3>
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order Date</th>
              <th>Name</th>
              <th>Amount</th>
              <th>Details</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.order_id">
              <td>{{ formatDate(order.created_at) }}</td>
              <td>{{ order.user_name }}</td>
              <td>{{ order.total_price }} EGP</td>
              <td>
                <button @click="showOrderDetails(order.order_id)">View Details</button>
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination" v-if="totalPages > 1">
          <button @click="prevPage" :disabled="currentPage === 1">Previous</button>
          <span>Page {{ currentPage }} of {{ totalPages }}</span>
          <button @click="nextPage" :disabled="currentPage === totalPages">Next</button>
        </div>
      </div>
      
      <!-- Order Details Modal -->
      <div v-if="showDetailsModal" class="modal">
        <div class="modal-content">
          <span class="close" @click="showDetailsModal = false">&times;</span>
          <h3>Order Details</h3>
          <div v-if="selectedOrder">
            <p><strong>Order Date:</strong> {{ formatDate(selectedOrder.created_at) }}</p>
            <p><strong>User:</strong> {{ selectedOrder.user_name }}</p>
            
            <table class="order-items">
              <thead>
                <tr>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Price</th>
                  <th>Total</th>
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
            
            <p class="total-amount">
              <strong>Total Amount:</strong> {{ selectedOrder.total_price }} EGP
            </p>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'AdminChecks',
    data() {
      return {
        dateFrom: '',
        dateTo: '',
        selectedUserId: '',
        users: [],
        usersSummary: [],
        orders: [],
        currentPage: 1,
        itemsPerPage: 10,
        totalPages: 1,
        showDetailsModal: false,
        selectedOrder: null
      };
    },
    mounted() {
      this.fetchUsers();
      this.fetchOrders();
    },
    methods: {
      async fetchUsers() {
        try {
          const response = await axios.get('/users');
          this.users = response.data;
        } catch (error) {
          console.error('Error fetching users:', error);
        }
      },
      async fetchOrders() {
        try {
          const params = {
            page: this.currentPage,
            limit: this.itemsPerPage
          };
          
          if (this.dateFrom) {
            params.dateFrom = this.dateFrom;
          }
          
          if (this.dateTo) {
            params.dateTo = this.dateTo;
          }
          
          if (this.selectedUserId) {
            params.userId = this.selectedUserId;
          }
          
          // Fetch user summaries if no specific user is selected
          if (!this.selectedUserId) {
            const summaryResponse = await axios.get('/orders/summary', { params });
            this.usersSummary = summaryResponse.data;
          }
          
          // Fetch orders
          const ordersResponse = await axios.get('/orders', { params });
          this.orders = ordersResponse.data.orders;
          this.totalPages = Math.ceil(ordersResponse.data.total / this.itemsPerPage);
        } catch (error) {
          console.error('Error fetching orders:', error);
        }
      },
      async showOrderDetails(orderId) {
        try {
          const response = await axios.get(`/orders/${orderId}`);
          this.selectedOrder = response.data;
          this.showDetailsModal = true;
        } catch (error) {
          console.error('Error fetching order details:', error);
        }
      },
      formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString('en-US', options);
      },
      prevPage() {
        if (this.currentPage > 1) {
          this.currentPage--;
          this.fetchOrders();
        }
      },
      nextPage() {
        if (this.currentPage < this.totalPages) {
          this.currentPage++;
          this.fetchOrders();
        }
      }
    }
  };
  </script>
  
  <style scoped>
  .admin-checks-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
  }
  
  .filters {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
  }
  
  .date-range, .user-filter {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }
  
  th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
  }
  
  th {
    background-color: #f2f2f2;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
  }
  
  .modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
  }
  
  .modal-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 800px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
  }
  
  .close {
    float: right;
    font-size: 24px;
    cursor: pointer;
  }
  
  .order-items {
    margin: 20px 0;
  }
  
  .total-amount {
    font-size: 1.2em;
    text-align: right;
    margin-top: 10px;
  }
  </style>