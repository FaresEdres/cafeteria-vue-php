<template>
    <div class="admin-orders-dashboard">
  
      <h2 class="page-title">Orders Dashboard</h2>

      <!-- Filters Section -->
      <div class="filters-section">
        <div class="date-filters">
          <label>From:</label>
          <input 
            type="date" 
            v-model="dateFrom" 
            @change="fetchOrders"
          >
          
          <label>To:</label>
          <input 
            type="date" 
            v-model="dateTo" 
            @change="fetchOrders"
          >
        </div>
  
        <div class="user-filter">
          <label>Filter by User:</label>
          <select 
            v-model="selectedUserId" 
            @change="fetchOrders"
          >
            <option value="">All Users</option>
            <option 
              v-for="user in users" 
              :key="user.id" 
              :value="user.id"
            >
              {{ user.firstname }} {{ user.lastname }} (Room: {{ user.room || 'N/A' }})
            </option>
          </select>
        </div>
      </div>
  
      <!-- Orders List -->
      <div class="orders-container">
        <div v-if="loading" class="loading-indicator">
          Loading orders...
        </div>
  
        <div v-else-if="error" class="error-message">
          {{ error }}
        </div>
  
        <div v-else-if="orders.length === 0" class="no-orders">
          No orders found for the selected criteria.
        </div>
  
        <div 
          v-else v-for="order in paginatedOrders" :key="order.id" class="order-card" >
          <!-- Order Header -->
          <div class="order-header">
            <span class="order-date">
              {{ formatDateTime(order.created_at) }}
            </span>
            <span 
              class="order-status"
              :class="order.status"
            >
              {{ order.status }}
            </span>
          </div>
  
          <!-- Customer Info -->
          <div class="customer-info">
            <h3>{{ order.user_name }}</h3>
            <p>Room: {{ order.room }} | Ext: {{ order.ext }}</p>
          </div>
  
          <!-- Products List -->
          <div class="products-list">
            <div 
              v-for="product in order.products"
              :key="product.product_id"
              class="product-item"
            >
              <span class="product-name">{{ product.name }}</span>
              <span class="product-quantity">{{ product.quantity }}</span>
            </div>
          </div>
  
          <div class="order-footer">
  <div class="total-amount">
    Total: {{ order.total_price }} EGP
  </div>

  <div class="button-column" v-if="order.status === 'processing'">
    <button @click="markAsDelivered(order.order_id)" class="deliver-btn">
      Mark as Delivered
    </button>
    <button @click="cancelOrder(order)" class="cancel-btn">
      Cancel order
    </button>
  </div>
</div>
    </div>
      </div>
    </div>
     <div class="pagination">
     <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1">Prev</button>
      <span>Page {{ currentPage }} of {{ totalPages }}</span>
     <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages">Next</button>
    </div>

  </template>
  
  <script setup>
  import { ref, onMounted,computed } from 'vue';
  import { getRequest, patchRequest, patchOrderRequest } from '@/services/httpClient';
  
  // Reactive data
  const filters = ref({
    dateFrom: '',
    dateTo: '',
    userId: ''
  });
  const dateFrom = ref('');
  const dateTo = ref('');
  const selectedUserId = ref('');
  
 //========================================
  const orders = ref([]);
  const users = ref([]);
  const loading = ref(false);
  const error = ref('');


 // Pagination
  const currentPage = ref(1);
  const itemsPerPage = 5;

//   const fetchOrders = async () => {
//   try {
//     loading.value = true;

//     // Determine the endpoint based on user selection
//     const endpoint = selectedUserId.value
//       ? `orders/${selectedUserId.value}`  // Specific user's orders
//       : 'orders';                        // All orders

//     // Prepare query params for date filtering
//     const params = {};
//     if (dateFrom.value) params.dateFrom = dateFrom.value;
//     if (dateTo.value) params.dateTo = dateTo.value;


//       // Don't send date filters to backend since it doesn't support them
//        const response = await getRequest(endpoint);

//         // Get raw orders list
//         const rawOrders = response.data || response;

//          // Apply frontend filtering based on selected dates
//          orders.value = rawOrders.filter(order => {
//           const orderDate = new Date(order.created_at);
//          const from = dateFrom.value ? new Date(dateFrom.value) : null;
//           const to = dateTo.value ? new Date(dateTo.value) : null;

//          // Filtering logic
//           if (from && orderDate < from) return false;
//           if (to && orderDate > to) return false;

//           return true; // Show this order
//    });
//     //======================================================
//   } catch (error) {
//     console.error('Error fetching orders:', error);
//     alert(error.message || 'Failed to fetch orders');
//     orders.value = [];
//   } finally {
//     loading.value = false;
//   }
// };
  
  
// Fetch orders with frontend filtering
const fetchOrders = async () => {
  try {
    loading.value = true;
    error.value = '';

    const endpoint = selectedUserId.value
      ? `orders/${selectedUserId.value}`
      : 'orders';

    const response = await getRequest(endpoint);
    const rawOrders = response.data || response;

    const filteredOrders = rawOrders.filter(order => {
      const orderDate = new Date(order.created_at);
      const from = dateFrom.value ? new Date(dateFrom.value) : null;
      const to = dateTo.value ? new Date(dateTo.value) : null;

      if (from && orderDate < from) return false;
      if (to && orderDate > to) return false;

      return true;
    });

    orders.value = filteredOrders;

    // Reset to first page after filtering
    currentPage.value = 1;

  } catch (err) {
    console.error('Error fetching orders:', err);
    error.value = err.message || 'Failed to fetch orders';
    orders.value = [];
  } finally {
    loading.value = false;
  }
};

// Computed: sorted + paginated orders
const paginatedOrders = computed(() => {
  const sorted = [...orders.value].sort(
    (a, b) => new Date(b.created_at) - new Date(a.created_at)
  );
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return sorted.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(orders.value.length / itemsPerPage);
});

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};





  //========= Mark order as delivered ================================
  const markAsDelivered = async (orderId) => {
    if (confirm('Mark this order as delivered?')) {
      try {
        
        await patchOrderRequest(`orders/${orderId}`, { status: 'done' });
        fetchOrders(); // Refresh the orders list


      //   await patchOrderRequest(`orders/${order.order_id}`, { 
      //   status: 'cancel' 
      //  });


      } catch (err) {
        error.value = `Failed to update order: ${err.message}`;
      }
    }
  };


  //==================== Cancel Order ====================  
 const cancelOrder = async (order) => {
  if (!order?.order_id) {
    console.error('Invalid order object:', order);
    alert('Invalid order data');
    return;
  }

  if (confirm(`Are you sure you want to cancel order #${order.order_id}?`)) {
    try {
      loading.value = true;

      await patchOrderRequest(`orders/${order.order_id}`, { 
        status: 'cancel' 
      });
      await fetchOrders(); // Refresh the orders list
      alert(`Order #${order.order_id} has been cancelled successfully`);
    } catch (error) {
      console.error('Error cancelling order:', error);
      alert(error.message || 'Failed to cancel order');
    } finally {
      loading.value = false;
    }
  }
};
  
  //=================== Fetch all users for filter dropdown ==================================
  const fetchUsers = async () => {
    try {
      const response = await getRequest('users');
      users.value = response.data || response;
    } catch (err) {
      console.error('Failed to fetch users:', err);
    }
  };
  
  // Format date for display
  const formatDateTime = (dateString) => {
    if (!dateString) return '';
    const options = { 
      year: 'numeric', 
      month: 'short', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    return new Date(dateString).toLocaleDateString('en-US', options);
  };
  
  // Initialize component
  onMounted(() => {
    // Set default date range (last 7 days)
    const today = new Date();
    const lastWeek = new Date();
    lastWeek.setDate(today.getDate() - 7);
    
    filters.value.dateFrom = lastWeek.toISOString().split('T')[0];
    filters.value.dateTo = today.toISOString().split('T')[0];
    
    fetchUsers();
    fetchOrders();
  });
  </script>
  
  <style scoped>
  .page-title {
  font-size: 48px;
  color: #b49383;
  text-align: center;
  margin-bottom: 40px;
  font-family: 'Edward', cursive;
}

.admin-orders-dashboard {
  max-width: 1200px;
  margin: 0 auto;
  padding: 60px 20px;
  background-color: #fff;
  border: 2px solid #c4ab9f;
  box-shadow: 0px 20px 50px rgba(0, 0, 0, 0.1);
  font-family: 'PT Sans Narrow', sans-serif;
}

/* Filters */
.filters-section {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 20px;
  margin-bottom: 40px;
  background-color: #f8f4f2;
  border: 2px solid #c4ab9f;
  border-radius: 8px;
  padding: 20px;
}

.date-filters,
.user-filter {
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex: 1;
  min-width: 260px;
}

label {
  font-size: 16px;
  color: #282828;
}

input[type="date"],
select {
  padding: 10px 14px;
  border: 2px solid #c4ab9f;
  font-size: 16px;
  color: #232323;
  background: #fff;
  font-family: 'PT Sans Narrow', sans-serif;
  outline: none;
  border-radius: 4px;
}

/* Orders Container */
.orders-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* Order Card */
.order-card {
  border: 2px solid #c4ab9f;
  border-radius: 10px;
  padding: 20px;
  background: #fff;
  box-shadow: 0 15px 35px rgba(0,0,0,0.05);
  transition: transform 0.2s ease;
}

.order-card:hover {
  transform: translateY(-4px);
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 15px;
  border-bottom: 1px solid #e0e0e0;
  padding-bottom: 10px;
}

.order-date {
  font-size: 14px;
  color: #999;
}

.order-status {
  padding: 6px 12px;
  border-radius: 4px;
  font-size: 14px;
  font-weight: bold;
  text-transform: capitalize;
}

.order-status.processing {
  background-color: #fff7e6;
  color: #ff9800;
}

.order-status.done {
  background-color: #e6f4ea; /* light green */
  color: #4caf50;            /* green */
}

.order-status.cancel {
  background-color: #fdecea; /* light red/pink */
  color: #f44336;            /* red */
}

.customer-info h3 {
  font-size: 18px;
  margin: 0;
  color: #232323;
}

.customer-info p {
  margin: 4px 0 0;
  font-size: 14px;
  color: #666;
}

.products-list {
  margin: 16px 0;
  border-top: 1px dashed #dadada;
  padding-top: 10px;
}

.product-item {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  font-size: 15px;
  border-bottom: 1px dashed #eee;
}

.product-item:last-child {
  border-bottom: none;
}

.product-name {
  font-weight: 600;
  color: #232323;
}

.product-quantity {
  color: #999;
}

/* Footer */
.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 12px;
  border-top: 1px solid #e0e0e0;
  margin-top: 15px;
}

.total-amount {
  font-size: 16px;
  font-weight: bold;
  color: #232323;
}

.deliver-btn {
  background-color: #5baa49;
  color: #fff;
  padding: 8px 14px;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s ease;
}

.cancel-btn {
  background-color: #f40d09;
  color: #fff;
  padding: 8px 14px;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: 600;
  transition: background-color 0.2s ease;
}

.deliver-btn:hover {
  background-color: #a07a6c;
}

/* Loading / Error / Empty states */
.loading-indicator,
.error-message,
.no-orders {
  padding: 20px;
  text-align: center;
  background-color: #f4f0ee;
  border-radius: 8px;
  font-size: 16px;
  color: #b49383;
  border: 2px dashed #c4ab9f;
}

.error-message {
  background-color: #ffebee;
  color: #d32f2f;
  border-color: #f44336;
}
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  gap: 10px;
}

.pagination button {
  padding: 8px 16px;
  background-color: #b49383;
  color: white;
  border: none;
  border-radius: 4px;
  font-family: 'PT Sans Narrow', sans-serif;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.pagination button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
}

.pagination span {
  font-weight: bold;
  color: #232323;
}

  </style>