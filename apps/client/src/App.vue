<template>
  <div class="app">
  <AddProduct/>
  </div>
</template>

<script setup>
import { ref } from "vue";
import AddProductPage from "./components/AddProductPage.vue"
import AddProduct from "./components/AddProduct.vue"
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

</style>
