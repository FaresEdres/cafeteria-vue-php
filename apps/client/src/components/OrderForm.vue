<template>
    <div class="order-form">
      <h1>Order Form</h1>
      <div v-for="(product, index) in order.products" :key="index" class="product-item">
        <div class="product-details">
          <h3>{{ product.name }}</h3>
          <p>Price: ${{ product.price }}</p>
        </div>
        <div class="quantity-controls">
          <button @click="decreaseQuantity(index)" :disabled="product.quantity <= 1">-</button>
          <span>{{ product.quantity }}</span>
          <button @click="increaseQuantity(index)">+</button>
        </div>
      </div>
      <div class="comment-section">
        <label for="comment">Add a Comment:</label>
        <textarea id="comment" v-model="order.comment" placeholder="Add your comment here..."></textarea>
      </div>
      <button @click="submitOrder" class="submit-button">Submit Order</button>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useOrderStore } from '../stores/order.js';
  import { postRequest } from '../services/httpClient.js';
  
  const orderStore = useOrderStore();
  const order = ref({ ...orderStore.getOrder() });
  
  const increaseQuantity = (index) => {
    order.value.products[index].quantity++;
  };
  
  const decreaseQuantity = (index) => {
    if (order.value.products[index].quantity > 1) {
      order.value.products[index].quantity--;
    }
  };
  
  const submitOrder = async () => {
    try {
      const response = await postRequest('orders', order.value);
      alert('Order submitted successfully!');
      console.log(response);
    } catch (error) {
      alert('Failed to submit order: ' + error.message);
    }
  };
  </script>
  
  <style scoped>
  .order-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
  }
  
  .product-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
  }
  
  .product-details h3 {
    margin: 0;
  }
  
  .quantity-controls {
    display: flex;
    align-items: center;
  }
  
  .quantity-controls button {
    padding: 5px 10px;
    margin: 0 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f0f0f0;
    cursor: pointer;
  }
  
  .quantity-controls button:disabled {
    background-color: #e0e0e0;
    cursor: not-allowed;
  }
  
  .comment-section {
    margin-top: 20px;
  }
  
  .comment-section textarea {
    width: 100%;
    height: 80px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    resize: none;
  }
  
  .submit-button {
    display: block;
    width: 100%;
    padding: 10px;
    margin-top: 20px;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: #fff;
    font-size: 16px;
    cursor: pointer;
  }
  
  .submit-button:hover {
    background-color: #0056b3;
  }
  </style>