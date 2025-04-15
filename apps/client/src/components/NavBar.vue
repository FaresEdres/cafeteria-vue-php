<template>
  <div class="header_content d-flex flex-row align-items-center justify-content-start">
    <div class="logo">
      <a href="#">
        <div class="armane">The Armané</div>
        <div class="cafe">Café</div>
      </a>
    </div>
    <nav class="main_nav">
      <ul class="d-flex flex-row align-items-center justify-content-start">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/orders">My Orders</router-link></li>
        <li><router-link to="/menu">Menu</router-link></li>
        <li><router-link to="/contact">Contact</router-link></li>
        <li><router-link to="/about">About Us</router-link></li>
        <li><router-link to="/login">LogIN</router-link></li>
      </ul>
    </nav>
    <div class="coffee_icon_wrapper ml-auto position-relative">
      <router-link to="/orderform" class="coffee_link">
        <i class="fas fa-mug-hot coffee_icon"></i>
        <span class="coffee_count_badge">3</span>
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "Navbar",
};
</script>

<style scoped>
</style>
