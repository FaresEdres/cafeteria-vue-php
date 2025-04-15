import { ref, onMounted } from 'vue';
import { useOrderStore } from '../stores/order.js';
import { getRequest } from '../services/httpClient.js';

export function useCategoryProducts() {
  const orderStore = useOrderStore();

  const drinks = ref([]);
  const food = ref([]);
  const desserts = ref([]);
  const main = ref([]);
  const isLoading = ref(false);

  const fetchCategoryProducts = async (categoryId, targetRef) => {
    try {
      isLoading.value = true;
      let url = 'products';
      if (categoryId !== null) {
        url += `?category=${categoryId}`;
      }
      console.log(url);

      const response = await getRequest(url);
      targetRef.value = response.data;
      console.log(response.data);
    } catch (error) {
      alert(error.message || 'Failed to fetch products');
    } finally {
      isLoading.value = false;
    }
  };

  const addToOrderStore = (productId) => {
    orderStore.addOrderItem(productId, 1);
  };

  const ifExisting = (productId) => {
    const existingProduct = orderStore.getOrder().products.find(
      (product) => product['product-id'] === productId
    );
    return !!existingProduct;
  };

  onMounted(() => {
    fetchCategoryProducts(1, drinks);
    fetchCategoryProducts(11, food);
    fetchCategoryProducts(12, desserts);
    fetchCategoryProducts(10, main);
  });

  return {
    drinks,
    food,
    main,
    desserts,
    isLoading,
    addToOrderStore,
    ifExisting
  };
}
