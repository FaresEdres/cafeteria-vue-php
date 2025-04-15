<template>
  <div class="super_container">
    <header class="header">
      <div class="container">
        <div class="row">
          <div class="col">
            <Navbar />
          </div>
        </div>
      </div>

      <!-- Apply dynamic height here -->
      <div class="home_h" :style="{ height: backgroundHeight }">
        <!-- Dynamic background image -->
        <div
          class="parallax_background"
          :style="{ backgroundImage: `url(${backgroundImage})` }"
        ></div>

        <div class="home_container">
          <div class="container">
            <div class="row">
              <div class="col">
                <div class="home_content text-center">
                  <div class="home_subtitle page_subtitle">{{ subtitle }}</div>
                  <div class="home_title">
                    <h1>{{ title }}</h1>
                  </div>
                  <div class="home_text ml-auto mr-auto">
                    <p>{{ description }}</p>
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
import Navbar from './NavBar.vue';
import { useRoute } from 'vue-router';
import { computed } from 'vue';

export default {
  name: "HeaderComponent",
  components: {
    Navbar
  },
  setup() {
    const route = useRoute();

    const pageData = computed(() => {
      switch (route.path) {
        case '/':
            return {
            background: 'images/home.jpg',
            title: 'An Extraordinary Experience',
            subtitle: 'The Armané Café is',
            description: 'Welcome to The Armané Café, where rich aromas, handcrafted flavors, and a cozy atmosphere come together. Indulge in our artisanal coffee, freshly baked pastries, and gourmet delights, all served with a touch of elegance.',
            height: '100vh'
          };
        default:
          return {
            height: '0vh'
          };
      }
    });

    return {
      backgroundImage: computed(() => pageData.value.background),
      title: computed(() => pageData.value.title),
      subtitle: computed(() => pageData.value.subtitle),
      description: computed(() => pageData.value.description),
      backgroundHeight: computed(() => pageData.value.height)
    };
  }
};
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 100;
  transition: all 400ms ease;
  background-color: rgba(0, 0, 0, 0.5);
}

.home_h {
  position: relative;
  overflow: hidden;
  width: 100%;
  transition: height 0.4s ease;
}

.parallax_background {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

.page_subtitle {
  font-family: 'Dancing Script', cursive;
  color: #b49383;
  line-height: 0.75;
}

.home_title h1 {
  font-size: 48px;
  color: white;
}

.home_text p {
  font-size: 18px;
  color: #f2f2f2;
  max-width: 600px;
  margin: 0 auto;
}
</style>
