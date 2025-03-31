<template>
  <div class="app">
    <h1>Cafeteria Management System</h1>
    <div v-if="data">
      <p>Message from Server: {{ data.message }}</p>
      <p>Timestamp: {{ data.timestamp }}</p>
      <p>Environment: {{ data.environment }}</p>
    </div>
    <button @click="fetchData">Fetch Data from Server</button>
  </div>

  <AddUserPage/>
</template>

<script setup>
import { ref } from "vue";
import AddUserPage from "./components/AddUserPage.vue";
const data = ref(null);

const fetchData = async () => {
  try {
    const response = await fetch("http://localhost:8000");
    data.value = await response.json();
  } catch (error) {
    console.error("Error fetching data:", error);
  }
};
</script>

<style>
.app {
  padding: 20px;
  max-width: 800px;
  margin: 0 auto;
}

button {
  padding: 10px 20px;
  margin-top: 20px;
  cursor: pointer;
}
</style>
