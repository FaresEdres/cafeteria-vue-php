<template>
  <section id="product-management" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 800px; margin: auto;">

      <!-- Add Product Form -->
      <div style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">Add Product</h2>
        
        <form @submit.prevent="handleAddProduct">
          <!-- Product Name -->
          <div style="margin-bottom: 20px;">
            <label for="name" style="display:block; margin-bottom: 5px; color: #232323;">Product Name</label>
            <input 
              v-model="productData.name" 
              type="text" 
              id="name" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
            />
            <p v-if="errors.name" class="error-message">{{ errors.name }}</p>
          </div>
          
          <!-- Image File -->
          <div style="margin-bottom: 20px;">
            <label for="image" style="display:block; margin-bottom: 5px; color: #232323;">Image File</label>
            <input 
              type="file" 
              id="image" 
              @change="onFileChange" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
            />
            <p v-if="errors.imageFile" class="error-message">{{ errors.imageFile }}</p>
          </div>
          
          <!-- Description -->
          <div style="margin-bottom: 20px;">
            <label for="description" style="display:block; margin-bottom: 5px; color: #232323;">Description</label>
            <textarea 
              v-model="productData.description" 
              id="description" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              rows="5"
            ></textarea>
            <p v-if="errors.description" class="error-message">{{ errors.description }}</p>
          </div>

          <!-- Price -->
          <div style="margin-bottom: 20px;">
            <label for="price" style="display:block; margin-bottom: 5px; color: #232323;">Price</label>
            <input 
              v-model="productData.price" 
              type="number" 
              id="price" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
            />
            <p v-if="errors.price" class="error-message">{{ errors.price }}</p>
          </div>

          <!-- Category -->
          <!-- Category -->
<div style="margin-bottom: 20px;">
  <label for="category_id" style="display:block; margin-bottom: 5px; color: #232323;">Category</label>
  <div style="display: flex; align-items: center; gap: 10px;">
    <select 
      v-model="productData.category_id" 
      id="category_id" 
      class="form-control" 
      style="flex: 1; padding:10px; border: 1px solid #c4ab9f;"
    >
      <option disabled value="">Please select a category</option>
      <option v-for="category in categories" :key="category.id" :value="category.id">
        {{ category.name }}
      </option>
    </select>
    <button 
      @click="$router.push('/add-category')" 
      type="button"
      title="Add Category"
      style="padding: 10px 14px; background-color: #c4ab9f; border: none; border-radius: 4px; color: white; font-size: 18px; cursor: pointer;"
    >
      +
    </button>
  </div>
  <p v-if="errors.category_id" class="error-message">{{ errors.category_id }}</p>
</div>


          <!-- Submit Button -->
          <div style="margin-top: 30px; text-align: center;">
            <button 
              type="submit" 
              style="background-color: #c4ab9f; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
              :disabled="isLoading"
            >
              {{ isLoading ? "Adding..." : "Add Product" }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>

<script>
import { postRequest, getRequest } from "../services/httpClient.js";

export default {
  data() {
    return {
      productData: {
        name: '',
        description: '',
        price: '',
        category_id: '',
        imageFile: null
      },
      categories: [],
      isLoading: false,
      error: null,
      errors: {}
    };
  },
  methods: {
    onFileChange(event) {
      this.productData.imageFile = event.target.files[0];
    },
    validateForm() {
      const errors = {};
      if (!this.productData.name) errors.name = 'Product name is required';
      if (!this.productData.imageFile) errors.imageFile = 'Image is required';
      if (!this.productData.description) errors.description = 'Description is required';
      if (!this.productData.price) errors.price = 'Price is required';
      if (!this.productData.category_id) errors.category_id = 'Category is required';
      this.errors = errors;
      return Object.keys(errors).length === 0;
    },
    async handleAddProduct() {
      if (!this.validateForm()) return;

      try {
        this.isLoading = true;
        this.error = null;

        const formData = new FormData();
        formData.append('name', this.productData.name);
        formData.append('description', this.productData.description);
        formData.append('price', this.productData.price);
        formData.append('category_id', this.productData.category_id);
        formData.append('image', this.productData.imageFile);

        await postRequest('products', formData);

        this.productData = {
          name: '',
          description: '',
          price: '',
          category_id: '',
          imageFile: null
        };
        this.errors = {};
      } catch (error) {
        this.error = error.message || 'Failed to add product';
        console.error('Error adding product:', error);
      } finally {
        this.isLoading = false;
      }
    },
    async fetchCategories() {
      try {
        this.isLoading = true;
        const response = await getRequest('categories');
        this.categories = response;
      } catch (error) {
        alert(error.message || 'Failed to fetch categories');
      } finally {
        this.isLoading = false;
      }
    }
  },
  mounted() {
    this.fetchCategories();
  }
};
</script>

<style scoped>
.form-control {
  transition: border-color 0.3s ease;
}

.form-control:focus {
  outline: none;
  border-color: #b08d7a;
  box-shadow: 0 0 0 2px rgba(192, 152, 127, 0.2);
}

button {
  transition: opacity 0.3s ease, background-color 0.3s ease;
}

button:hover {
  opacity: 0.9;
}

button:disabled {
  background-color: #cccccc !important;
  cursor: not-allowed;
  opacity: 1;
}

.product-list > div {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.product-list > div:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.error-message {
  color: #e74c3c;
  font-size: 14px;
  margin-top: 5px;
}
</style>
