<template>
  <div class="order-form">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">Your Order</h1>
    <div v-if="products.length === 0" class="empty-cart">
      <div class="text-gray-600 text-center py-8">
        <i class="fas fa-shopping-cart text-4xl mb-4"></i>
        <p class="text-lg">Your cart is empty</p>
      </div>
    </div>
    <div v-else>
      <div v-for="(product, index) in products" :key="index" class="product-item">
        <div class="product-details">
          <h3 class="product-name">{{ product.name }}</h3>
          <p class="product-price">${{ product.price }}</p>
        </div>
        <div class="product-controls">
          <div class="quantity-controls">
            <button @click="decreaseQuantity(index)" 
                    :disabled="product.quantity <= 1" 
                    class="quantity-btn">
              <i class="fas fa-minus"></i>
            </button>
            <span class="quantity-display">{{ product.quantity }}</span>
            <button @click="increaseQuantity(index)" 
                    class="quantity-btn">
              <i class="fas fa-plus"></i>
            </button>
          </div>
          <button @click="removeProduct(product.id)" class="delete-btn">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>

      <div class="order-summary">
        <div class="total-section">
          <span class="total-label">Total:</span>
          <span class="total-amount">${{ calculateTotal() }}</span>
        </div>
        <div class="comment-section">
          <label for="comment">Special Instructions:</label>
          <textarea 
            id="comment" 
            v-model="order.comment" 
            placeholder="Add any special instructions here..."
            rows="3"
          ></textarea>
        </div>
        <div class="actions">
          <button @click="clearCart" class="clear-btn">
            Clear Cart
          </button>
          <button 
            @click="submitOrder" 
            :disabled="products.length === 0"
            class="submit-btn">
            Place Order
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useOrderStore } from '../stores/order.js';
import { getRequest, postRequest } from '../services/httpClient.js';

const orderStore = useOrderStore();
const order = ref({ ...orderStore.getOrder() });
const products = ref([]);
console.log(order.value.products);

const calculateTotal = () => {
  return products.value.reduce((total, product) => {
    return total + (product.price * product.quantity);
  }, 0).toFixed(2);
};

const fetchProducts = async () => {
  try {
    const productDetails = [];
    for (const product of orderStore.getOrder().products) {
      const response = await getRequest(`products/${product["product-id"]}`);
      productDetails.push({
        id: product["product-id"],
        name: response.data.name,
        price: response.data.price,
        quantity: product.quantity,
      });
    }
    products.value = productDetails;
  } catch (error) {
    console.error("Failed to fetch product details:", error.message);
  }
};

// Watch for changes in the order store
watch(
  () => orderStore.getOrder().products,
  () => {
    fetchProducts();
  },
  { deep: true }
);

onMounted(fetchProducts);

const increaseQuantity = (index) => {
  products.value[index].quantity++;
  orderStore.increaseQuantity(products.value[index].id);
};

const decreaseQuantity = (index) => {
  if (products.value[index].quantity > 1) {
    products.value[index].quantity--;
    orderStore.decreaseQuantity(products.value[index].id);
  }
};

const removeProduct = (productId) => {
  orderStore.removeOrderItem(productId);
  products.value = products.value.filter(product => product.id !== productId);
};

const clearCart = () => {
  orderStore.deleteOrderProducts();
  products.value = [];
};

const submitOrder = async () => {
  try {
    const orderData = {
      user_id: 1,
      comment: order.value.comment,
      products: products.value.map((product) => ({
        id: product.id,
        quantity: product.quantity,
      })),
    };
    await postRequest("orders", orderData);
    clearCart();
    alert("Order submitted successfully!");
  } catch (error) {
    alert("Failed to submit order: " + error.message);
  }
};
</script>

<style scoped>
.order-form {
  max-width: 100%;
  margin: 0 auto;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.empty-cart {
  padding: 2rem;
  text-align: center;
  color: #666;
  background: #f8f9fa;
  border-radius: 8px;
}

.product-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  margin-bottom: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  transition: transform 0.2s ease;
}

.product-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.product-details {
  flex-grow: 1;
}

.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.25rem;
}

.product-price {
  color: #666;
  font-weight: 500;
}

.product-controls {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #fff;
  padding: 0.25rem;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.quantity-btn {
  padding: 0.5rem;
  background: #4a90e2;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.quantity-btn:hover {
  background: #357abd;
}

.quantity-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.quantity-display {
  min-width: 2rem;
  text-align: center;
  font-weight: 600;
}

.delete-btn {
  padding: 0.5rem;
  background: #dc3545;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background 0.2s;
}

.delete-btn:hover {
  background: #c82333;
}

.order-summary {
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 2px solid #eee;
}

.total-section {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
  margin-bottom: 1rem;
}

.total-label {
  font-size: 1.2rem;
  font-weight: 600;
  color: #2c3e50;
}

.total-amount {
  font-size: 1.4rem;
  font-weight: 700;
  color: #4a90e2;
}

.comment-section {
  margin-bottom: 1.5rem;
}

.comment-section label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2c3e50;
}

.comment-section textarea {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ddd;
  border-radius: 6px;
  resize: vertical;
  min-height: 80px;
}

.actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
}

.clear-btn, .submit-btn {
  flex: 1;
  padding: 0.75rem;
  border: none;
  border-radius: 6px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.clear-btn {
  background: #6c757d;
  color: white;
}

.clear-btn:hover {
  background: #5a6268;
}

.submit-btn {
  background: #28a745;
  color: white;
}

.submit-btn:hover {
  background: #218838;
}

.submit-btn:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>