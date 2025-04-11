<template>
  <div class="app">
  <AddProductPage/>
  </div>
</template>

<script setup>
import { ref } from "vue";
import AddProductPage from "./components/AddProductPage.vue"
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
