<template>
  <div class="super_container">
    <header class="header">
      <div class="container">
        <div class="row">
          <div class="col">
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
                <router-link to="/orderform"  class="coffee_link">
                  <i class="fas fa-mug-hot coffee_icon"></i>
                  <span class="coffee_count_badge">3</span>
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="home">
        <div class="parallax_background parallax-window" data-parallax="scroll" data-image-src="images/home.jpg"
          data-speed="0.8"></div>
        <div class="home_container">
          <div class="container">
            <div class="row">
              <div class="col">
                <div class="home_content text-center">
                  <div class="home_subtitle page_subtitle">The Armané Café is</div>
                  <div class="home_title">
                    <h1>An Extraordinary Experience</h1>
                  </div>
                  <div class="home_text ml-auto mr-auto">
                    <p> Welcome to The Armané Café, where rich aromas, handcrafted flavors, and a cozy atmosphere come
                      together.
                      Indulge in our artisanal coffee, freshly baked pastries, and gourmet delights, all served with a
                      touch of elegance.
                      Whether you're here for a morning boost or an evening unwind, every sip and bite is crafted for
                      perfection.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="scroll_icon"></div>
      </div>
    </header>
  </div>
</template>



<script>
export default {
  name: "HeaderComponent",
};
</script>


<style scoped>

</style>
