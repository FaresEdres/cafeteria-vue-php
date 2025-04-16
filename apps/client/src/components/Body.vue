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
                <p>Welcome to <span class="heart">Armané Café</span> <span class="small-heart">&#10084;</span> , where
                  every moment is an experience of warmth and flavor. Our café is a sanctuary for coffee lovers, food
                  enthusiasts, and anyone seeking a cozy escape. From expertly brewed coffee to handcrafted pastries,
                  every dish is made with passion and care. Whether you're enjoying a morning cup or relaxing in the
                  evening, you'll find that every detail at Armene Café is designed for your comfort and enjoyment. Join
                  us for a truly memorable experience!</p>
              </div>
            </div>
            <div class="row">
              <div class="col-xl-4 col-md-6 intro_col">
                <div class="intro_image"><img src="/intro_1.jpg" alt="https://unsplash.com/@quanle2819"></div>
              </div>
              <div class="col-xl-4 col-md-6 intro_col">
                <div class="intro_image"><img src="/intro_2.jpg" alt="https://unsplash.com/@fabmag"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="themenu">
      <div class="container">
        <div class="row">
          <div class="col">
            <div class="themenu_title_bar_container">
              <div class="themenu_stars text-center page_subtitle">The Armané Café</div>
              <div class="themenu_rating text-center">
                <div class="rating_r rating_r_5"><i></i><i></i><i></i><i></i><i></i></div>
              </div>
              <div class="themenu_title_bar d-flex flex-column align-items-center justify-content-center">
                <div class="themenu_title">The Leasted Order</div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="isLoading" class="text-center my-4">
          <p>Loading leatest product...</p>
        </div>
        <!-- call the leatst order her  -->
        <div class="sig" >
          <div class="sig_content_container">
            <div class="container">
              <div class="row">
                <div class="col-lg-7">
                  <div class="sig_content">
                    <div class="sig_subtitle page_subtitle">The Leatest Order</div>
                    <div class="sig_title">
                      <h1>Your Last Order</h1>
                    </div>
                    <div class="rating_r sig_rating rating_r_5"><i></i><i></i><i></i><i></i><i></i></div>
                    <div class="sig_name_container d-flex flex-column align-items-start">
                      <!-- <div class="sig_name">{{ product.name }}</div>
                      <div class="sig_name">{{ product.description }}</div>
                      <div class="sig_price ml-auto">${{ product.price }}</div> -->
                    </div>
                    <div class="button sig_button trans_200"><a href="#">Order Again</a></div>
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
                    <!-- <div class="background_image" :style="{ backgroundImage: 'url(' + product.image + ')' }"></div>
                     <img :src="'http://localhost:8000/public/uploads/' + product.image" :alt="product.name"> -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
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
import { useOrderStore } from '../stores/order.js';
import { getRequest } from "../services/httpClient.js";
const orderStore = useOrderStore();

const main = ref([]);
  const isLoading = ref(false);

  const fetchCategoryProducts = async (categoryId, targetRef) => {
    try {
      isLoading.value = true;
      let url = 'products';

      const response = await getRequest(url);
      targetRef.value = response.data;
    } catch (error) {
      alert(error.message || 'Failed to fetch products');
    } finally {
      isLoading.value = false;
    }
  };
  onMounted(() => {
    fetchCategoryProducts(10, main);
  });


const addToOrderStore = (productId) => {
  orderStore.addOrderItem(productId, 1);
};

const ifExisting = (productId) => {
  const existingProduct = orderStore.getOrder().products.find(
    (product) => product["product-id"] === productId
  );
  return !!existingProduct;
};

// Watch for changes in the order store
watch(() => orderStore.getOrder().products, () => {
  // This will automatically update the UI when products are removed
  // or when the cart is cleared
}, { deep: true });
</script>

<style scoped>
.small-heart {
  font-size: 18px;
  color: rgba(255, 0, 0, 0.3);
  margin-left: 5px;
}
</style>
