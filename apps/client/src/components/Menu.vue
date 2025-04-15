<template>
  <div class="super_container">
    <div class="themenu">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="section_title_container text-center">
              <div class="section_subtitle page_subtitle">Something new</div>
              <div class="section_title">
                <h1>Discover Our Menu</h1>
              </div>
            </div>
          </div>
        </div>
        <div class="row themenu_text_row">
          <div class="col-lg-6">
            <div class="themenu_text">
              <p>At The Armané Café, we pride ourselves on offering a range of high-quality dishes made from the finest
                ingredients. Whether you're in the mood for a hearty starter, a delicious main course, or a sweet
                dessert, we have something to satisfy every palate. Our menu is carefully crafted to provide you with an
                unforgettable dining experience, with a mix of traditional and modern flavors to explore.</p>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="themenu_text">
              <p>From the rich aromas of our hand-picked coffee beans to the fresh and vibrant ingredients in our main
                courses, every dish at The Armané Café is designed to provide you with a sensory experience. Savor the
                perfect balance of flavors and textures, and indulge in our expertly curated menu, all served in a cozy
                and welcoming atmosphere.</p>
            </div>
          </div>
        </div>
        <div class="row themenu_row">

          <!-- Drinks -->
          <div class="col-lg-4 themenu_column">
            <div class="themenu_image"><img src="/Cappuccino.jpg" height="330px" alt=""></div>
            <div class="themenu_col trans_400">
              <div class="themenu_col_title">Drinks</div>
              <div class="dish_list">

                <!-- Drinks -->
                <div v-for="product in drinks" :key="product.id" class="dish">
                  <div
                    class="dish_title_container d-flex flex-xl-row flex-column align-items-start justify-content-start">
                    <div class="dish_title">{{ product.name }}</div>
                    <div class="dish_price">${{ product.price }}</div>
                  </div>
                  <div class="dish_contents">
                    <div class="d-flex flex-row align-items-start justify-content-start flex-wrap">
                      {{ product.description }}
                    </div>
                  </div>
                  <div class="dish_image mt-2 mb-2">
                    <img :src="'http://localhost:8000/public/uploads/' + product.image" :alt="product.name" class="img-fluid"
                      style="max-width: 50%; border-radius: 8px;" />
                  </div>
                  <div class="dish_order"><a href="#">Order Now</a></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main -->
          <div class="col-lg-4 themenu_column">
            <div class="themenu_image"><img src="/main.jpg" alt=""></div>
            <div class="themenu_col trans_400">
              <div class="themenu_col_title">Main</div>
              <div class="dish_list">

                <!-- Dish -->
                <div v-for="product in food" :key="product.id" class="dish">
                  <div
                    class="dish_title_container d-flex flex-xl-row flex-column align-items-start justify-content-start">
                    <div class="dish_title">{{ product.name }}</div>
                    <div class="dish_price">${{ product.price }}</div>
                  </div>
                  <div class="dish_contents">
                    <div class="d-flex flex-row align-items-start justify-content-start flex-wrap">
                      {{ product.description }}
                    </div>
                  </div>
                  <div class="dish_image mt-2 mb-2">
                    <img :src="'http://localhost:8000/public/uploads/' + product.image" :alt="product.name" class="img-fluid"
                      style="max-width: 50%; border-radius: 8px;" />
                  </div>
                  <div class="dish_order"><a href="#">Order Now</a></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Deserts -->
          <div class="col-lg-4 themenu_column">
            <div class="themenu_image"><img src="/deserts.jpg" alt=""></div>
            <div class="themenu_col trans_400">
              <div class="themenu_col_title">Deserts</div>
              <div class="dish_list">

                <!-- Dish -->
                <div v-for="product in desserts" :key="product.id" class="dish">
                  <div
                    class="dish_title_container d-flex flex-xl-row flex-column align-items-start justify-content-start">
                    <div class="dish_title">{{ product.name }}</div>
                    <div class="dish_price">${{ product.price }}</div>
                  </div>
                  <div class="dish_contents">
                    <div class="d-flex flex-row align-items-start justify-content-start flex-wrap">
                      {{ product.description }}
                    </div>
                  </div>
                  <div class="dish_image mt-2 mb-2">
                    <img :src="'http://localhost:8000/public/uploads/' + product.image" :alt="product.name" class="img-fluid"
                      style="max-width: 50%; border-radius: 8px;" />
                  </div>
                  <div class="dish_order"><a href="#">Order Now</a></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useCategoryProducts } from '../composables/useCategoryProducts.js';

const {
  drinks,
  food,
  desserts,
  isLoading,
  addToOrderStore,
  ifExisting
} = useCategoryProducts();

</script>

<style scoped>
.small-heart {
  font-size: 18px;
  color: rgba(255, 0, 0, 0.3);
  margin-left: 5px;
}
</style>
