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

    <!-- Orders Table -->
    <table class="orders-table">
      <thead>
        <tr>
          <th>Order Status</th>
          <th>User</th>
          <th>Total Amount</th>
          <th>Creation Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>

        <tr v-for="order in orders" :key="order.id || order.order_id">
          <!-- <td>{{ formatDate(order.order_id) }}</td> -->
          <td>{{ order.status }}</td>
          <td>{{ order.user_name }}</td>
          <td>{{ order.total_price }} EGP</td>
          <td>{{ order.created_at }} </td>
          <td class="action-buttons">
           <button @click="showOrderDetails(order)" class="view-btn">View</button>
           <button @click="() => cancelOrder(order)" class="cancel-btn">Cancel</button>
           <button @click="() => doneOrder(order)" class="edit-btn">Done</button>
          </td>
        </tr>
      </tbody>
    </table>

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
                <td>
                  <img v-if="product.image" :src="product.image" :alt="product.name" class="product-image">
                  {{ product.name }}
                </td>
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

<script setup>
import { ref, onMounted, computed } from 'vue';  // Fixed: Added computed import
import { getRequest, patchRequest ,patchOrderRequest} from '@/services/httpClient';

const dateFrom = ref('');
const dateTo = ref('');
const selectedUserId = ref('');
const users = ref([]);
const orders = ref([]);
const showDetailsModal = ref(false);
const selectedOrder = ref(null);
const isLoading = ref(false);

// Now computed is properly defined
const usersMap = computed(() => {
  return users.value.reduce((map, user) => {
    map[user.id] = `${user.firstname} ${user.lastname}`;
    return map;
  }, {});
});

//=================== User Fetching====================  
// const fetchUsers = async () => {
//   try {
//     isLoading.value = true;
//     const response = await getRequest('users');
//     console.log("my users before ", response);
//     users.value = response.data || response;
//     console.log(" my debug users afterrr", users.value);
//   } catch (error) {
//     console.error('Error fetching users:', error);
//     alert(error.message || 'Failed to fetch users');
//   } finally {
//     isLoading.value = false;
//   }
// };

const fetchUsers = async () => {
  try {
    isLoading.value = true;

    const response = await getRequest('users');
    const data = response.data || response;

    // ✅ Ensure we only assign if the response is actually an array
    if (Array.isArray(data)) {
      users.value = data;
    } else {
      console.warn('Invalid user response structure:', data);
      users.value = []; // Set empty list to avoid crashing reduce()
    }

  } catch (error) {
    console.error('Error fetching users:', error);
    alert(error.message || 'Failed to fetch users');
    users.value = [];
  } finally {
    isLoading.value = false;
  }
};

 
//=================== Orders Fetching====================  
const fetchOrders = async () => {
  try {
    isLoading.value = true;
    // Determine the endpoint based on user selection
    const endpoint = selectedUserId.value
      ? `orders/${selectedUserId.value}`  // Specific user's orders
      : 'orders';                        // All orders

    // Prepare query params for date filtering
    const params = {};
    if (dateFrom.value) params.dateFrom = dateFrom.value;
    if (dateTo.value) params.dateTo = dateTo.value;


      // Don't send date filters to backend since it doesn't support them
       const response = await getRequest(endpoint);

       console.log(" my Orders  Debug", response);

        // Get raw orders list
        const rawOrders = response.data || response;

         // Apply frontend filtering based on selected dates
         orders.value = rawOrders.filter(order => {
          const orderDate = new Date(order.created_at);
         const from = dateFrom.value ? new Date(dateFrom.value) : null;
          const to = dateTo.value ? new Date(dateTo.value) : null;

          console.log(" my orders.value  Debug", orders.value);

         // Filtering logic
          if (from && orderDate < from) return false;
          if (to && orderDate > to) return false;

          return true; // Show this order
   });
    //======================================================
  } catch (error) {
    console.error('Error fetching orders:', error);
    alert(error.message || 'Failed to fetch orders');
    orders.value = [];
  } finally {
    isLoading.value = false;
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
      isLoading.value = true;
      
      await patchOrderRequest(`orders/${order.order_id}`, { 
        status: 'cancel' 
      });
      await fetchOrders(); // Refresh the orders list
      alert(`Order #${order.order_id} has been cancelled successfully`);
    } catch (error) {
      console.error('Error cancelling order:', error);
      alert(error.message || 'Failed to cancel order');
    } finally {
      isLoading.value = false;
    }
  }
};

//==================== Complete Order ====================  
const doneOrder = async (order) => {
  if (!order?.order_id) {
    console.error('Invalid order object:', order);
    alert('Invalid order data');
    return;
  }

  if (confirm(`Mark order #${order.order_id} as completed?`)) {
    try {
      isLoading.value = true;
      await patchOrderRequest(`orders/${order.order_id}`, { 
        status: 'done' 
      });
      await fetchOrders(); // Refresh the orders list
      alert(`Order #${order.order_id} has been marked as completed`);
    } catch (error) {
      console.error('Error completing order:', error);
      alert(error.message || 'Failed to complete order');
    } finally {
      isLoading.value = false;
    }
  }
};

//========== Show Data Details ===================
const showOrderDetails = (order) => {
  selectedOrder.value = order;
  showDetailsModal.value = true;
};

const formatDate = (dateString) => {
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

onMounted(async () => {
  fetchUsers();
  fetchOrders();
});
</script>

<style scoped>
.admin-checks-container {
  padding: 60px 20px;
  max-width: 1200px;
  margin: 0 auto;
  background-color: #fff;
  border: 2px solid #c4ab9f;
  box-shadow: 0px 20px 50px rgba(0, 0, 0, 0.1);
}

h2 {
  font-size: 48px;
  color: #b49383;
  text-align: center;
  margin-bottom: 40px;
  font-family: 'Edward', cursive;
}

.filters {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  margin-bottom: 40px;
  gap: 20px;
}

.date-range,
.user-filter {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 250px;
}

label {
  font-size: 16px;
  color: #282828;
  margin-bottom: 6px;
}

input[type="date"],
select {
  padding: 10px 15px;
  border: 2px solid #c4ab9f;
  font-size: 16px;
  font-family: 'PT Sans Narrow', sans-serif;
  color: #232323;
  outline: none;
  background: #fff;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 40px;
  font-family: 'PT Sans Narrow', sans-serif;
}

th,
td {
  padding: 14px;
  border: 1px solid #dadada;
  text-align: left;
  font-size: 16px;
  color: #232323;
}

th {
  background-color: #f8f4f2;
  color: #b49383;
  font-weight: 700;
  text-transform: uppercase;
}

.action-buttons button {
  margin-right: 5px;
  padding: 8px 12px;
  border-radius: 4px;
  border: none;
  font-size: 14px;
  font-weight: 600;
  font-family: 'PT Sans Narrow', sans-serif;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-btn {
  background-color: #b49383;
  color: #fff;
}

.cancel-btn {
  background-color: #f44336;
  color: white;
}

.edit-btn {
  background-color: #2196F3;
  color: white;
}

.action-buttons button:hover {
  filter: brightness(1.1);
}

/* Modal Styling */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

.modal-content {
  background: white;
  padding: 30px;
  border: 2px solid #c4ab9f;
  width: 90%;
  max-width: 800px;
  max-height: 90vh;
  overflow-y: auto;
  border-radius: 10px;
  position: relative;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
}

.close {
  position: absolute;
  top: 12px;
  right: 20px;
  font-size: 28px;
  font-weight: bold;
  color: #b49383;
  cursor: pointer;
}

.order-items {
  margin-top: 20px;
  width: 100%;
  border-collapse: collapse;
}

.order-items th,
.order-items td {
  border: 1px solid #eee;
  padding: 12px;
  text-align: left;
}

.order-items th {
  background-color: #f4f0ee;
  color: #b49383;
}

.total-amount {
  font-size: 20px;
  text-align: right;
  margin-top: 20px;
  font-weight: bold;
  color: #232323;
}

.product-image {
  width: 40px;
  height: 40px;
  object-fit: cover;
  margin-right: 10px;
  border-radius: 4px;
  vertical-align: middle;
}

.loading-message {
  padding: 20px;
  text-align: center;
  font-style: italic;
  color: #666;
}

button {
  padding: 5px 10px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #fdc304;
}

.view-btn {
  background-color: #4CAF50;
  /* Green */
  color: white;
}

.cancel-btn {
  background-color: #f44336;
  /* Red */
  color: white;
}

.edit-btn {
  background-color: #2196F3;
  /* Blue */
  color: white;
}
</style>