<template>
  <div class="super_container">
    <div class="intro">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="intro_content">
              <div class="intro_subtitle page_subtitle">Something new</div>
              <div class="intro_title">
                <h2>An Extraordinery Experience</h2>
              </div>
              <div class="intro_text">
                <p>
                  Welcome to <span class="heart">Armané Café</span>
                  <span class="small-heart">&#10084;</span>, where every moment is an experience of warmth and flavor.
                  Our café is a sanctuary for coffee lovers, food enthusiasts, and anyone seeking a cozy escape. From expertly
                  brewed coffee to handcrafted pastries, every dish is made with passion and care. Whether you're enjoying
                  a morning cup or relaxing in the evening, you'll find that every detail at Armene Café is designed for
                  your comfort and enjoyment. Join us for a truly memorable experience!
                </p>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-4 col-md-6 intro_col">
                <div class="intro_image"><img src="/intro_1.jpg" alt="Intro 1" /></div>
              </div>
              <div class="col-xl-4 col-md-6 intro_col">
                <div class="intro_image"><img src="/intro_2.jpg" alt="Intro 2" /></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- The Lasted Order Section -->
    <div class="themenu" v-if="lastOrder">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="themenu_title_bar_container">
              <div class="themenu_stars text-center page_subtitle">The Armané Café</div>
              <div class="themenu_rating text-center">
                <div class="rating_r rating_r_5"><i></i><i></i><i></i><i></i><i></i></div>
              </div>
              <div class="themenu_title_bar d-flex flex-column align-items-center justify-content-center">
                <div class="themenu_title">The Lasted Order</div>
              </div>
            </div>
          </div>
        </div>

        <div class="sig">
          <div class="sig_content_container">
            <div class="container">
              <div class="row">
                <div class="col-lg-7">
                  <div class="sig_content">
                    <div class="sig_subtitle page_subtitle" style="margin-bottom: 10px;">The Latest Order</div>
                    <div class="sig_name_container d-flex flex-column align-items-start">
                      <div v-for="product in lastOrder.products" :key="product.product_id" class="product-item">
                        <div class="sig_name">Order Name: {{ product.name }}</div>
                        <div class="sig_name">Order ID: {{ lastOrder.order_id }}</div>
                        <div class="sig_name">Quantity: {{ product.quantity }}</div>
                        <div class="sig_price ml-auto">Price: ${{ product.price }}</div>
                        <div class="sig_price ml-auto">Total: ${{ lastOrder.total_price }}</div>
                      </div>
                    </div>
                    <div class="button sig_button trans_200">
                      <a href="#">Order Again</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="sig_image_container">
            <div class="container">
              <div class="row">
                <div class="col-lg-7 offset-lg-5">
                  <div class="sig_image">
                    <div class="background_image"
                      :style="{ backgroundImage: lastOrder.products[0] ? 'url(' + lastOrder.products[0].image + ')' : '' }">
                    </div>
                    <img v-if="lastOrder.products.length > 0"
                      :src="'http://localhost:8000/public/uploads/' + lastOrder.products[0].image"
                      :alt="lastOrder.products[0].name" />
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Main Section -->
    <div class="themenu">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="themenu_title_bar_container">
              <div class="themenu_stars text-center page_subtitle">Armané Café</div>
              <div class="themenu_rating text-center">
                <div class="rating_r rating_r_5"><i></i><i></i><i></i><i></i><i></i></div>
              </div>
              <div class="themenu_title_bar d-flex flex-column align-items-center justify-content-center">
                <div class="themenu_title">The Main Section</div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="isLoading" class="text-center my-4">
          <p>Loading products...</p>
        </div>

        <div class="sig" v-for="product in main" :key="product.id">
          <div class="sig_content_container">
            <div class="container">
              <div class="row">
                <div class="col-lg-7">
                  <div class="sig_content">
                    <div class="sig_subtitle page_subtitle">Something new</div>
                    <div class="sig_title">
                      <h1>Our Signature Drink</h1>
                    </div>
                    <div class="rating_r sig_rating rating_r_5"><i></i><i></i><i></i><i></i><i></i></div>
                    <div class="sig_name_container d-flex flex-column align-items-start">
                      <div class="sig_name">{{ product.name }}</div>
                      <div class="sig_name">{{ product.description }}</div>
                      <div class="sig_price ml-auto">${{ product.price }}</div>
                    </div>
                    <div v-if="!ifExisting(product.id)">
                      <button @click="addToOrderStore(product.id)" class="button sig_button trans_200">Order Now</button>
                    </div>
                    <div v-else>
                      <button disabled class="button sig_button trans_200">Already in Order</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="sig_image_container">
            <div class="container">
              <div class="row">
                <div class="col-lg-7 offset-lg-5">
                  <div class="sig_image">
                    <div class="background_image" :style="{ backgroundImage: 'url(' + product.image + ')' }"></div>
                    <img :src="'http://localhost:8000/public/uploads/' + product.image" :alt="product.name">
                  </div>
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
import { ref, onMounted, watch } from 'vue';
import { postRequest, getRequest } from "../services/httpClient.js";
import { useOrderStore } from '../stores/order.js';
import { useCategoryProducts } from '../composables/useCategoryProducts.js';

// Main section logic
const orderStore = useOrderStore();
const {
  main,
  isLoading,
  addToOrderStore,
  ifExisting
} = useCategoryProducts();

watch(
  () => orderStore.getOrder().products,
  (newProducts) => {
    console.log('Order updated:', newProducts);
  },
  { deep: true }
);

const lastOrder = ref(null);
const userId = ref(null);

const fetchLastOrder = async () => {
  try {
    const user = await postRequest('authenticated');
    userId.value = user.id;

    const response = await getRequest(`LastOrder/${userId.value}`);
    if (response && response.length > 0) {
      lastOrder.value = response[0];
    } else {
      console.error('No last order found');
    }
  } catch (error) {
    console.error('Error fetching last order:', error);
  }
};

onMounted(() => {
  fetchLastOrder();
});
</script>
