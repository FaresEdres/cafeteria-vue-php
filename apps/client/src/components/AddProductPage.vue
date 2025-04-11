<template>
  <div>
    <!-- Add Product Form -->
    <h2>Add Product</h2>
    <form @submit.prevent="handleAddProduct">
      <div>
        <label for="name">Product Name</label>
        <input v-model="productData.name" type="text" id="name" required />
      </div>
      <div>
        <label for="image">Image File</label>
        <input type="file" id="image" @change="onFileChange" required />
      </div>
      <div>
        <label for="description">Description</label>
        <textarea v-model="productData.description" id="description"></textarea>
      </div>
      <div>
        <label for="price">Price</label>
        <input v-model="productData.price" type="number" id="price" required />
      </div>
      <div>
        <label for="category_id">Category ID</label>
        <input v-model="productData.category_id" type="number" id="category_id" required />
      </div>
      <button type="submit">Add Product</button>
    </form>

    <!-- Update Product Form -->
    <h2>Update Product</h2>
    <form @submit.prevent="handleUpdateProduct">
      <div>
        <label for="updateId">Product ID</label>
        <input v-model="updateProductData.id" type="number" id="updateId" required />
      </div>
      <div>
        <label for="updateName">Product Name</label>
        <input v-model="updateProductData.name" type="text" id="updateName" required />
      </div>
      <div>
        <label for="updateImage">Image File</label>
        <input type="file" id="updateImage" @change="onUpdateFileChange" />
      </div>
      <div>
        <label for="updateDescription">Description</label>
        <textarea v-model="updateProductData.description" id="updateDescription"></textarea>
      </div>
      <div>
        <label for="updatePrice">Price</label>
        <input v-model="updateProductData.price" type="number" id="updatePrice" required />
      </div>
      <div>
        <label for="updateCategory">Category ID</label>
        <input v-model="updateProductData.category_id" type="number" id="updateCategory" required />
      </div>
      <button type="submit">Update Product</button>
    </form>

    <!-- Delete Product -->
    <h2>Delete Product</h2>
    <form @submit.prevent="handleDeleteProduct">
      <div>
        <label for="deleteId">Product ID</label>
        <input v-model="deleteProductData.id" type="number" id="deleteId" required />
      </div>
      <button type="submit">Delete Product</button>
    </form>
<!-- Display All Products -->
<h2>All Products</h2>
<div v-if="products.length">
  <ul>
    <li v-for="product in products" :key="product.id">
      <strong>{{ product.name }}</strong> (ID: {{ product.id }})<br />
      Price: ${{ product.price }}<br />
      Category ID: {{ product.category_id }}<br />
      Description: {{ product.description }}<br />
      <img v-if="product.image_url" :src="product.image_url" alt="Product Image" style="max-width: 150px; margin-top: 5px;" />
      <hr />
    </li>
  </ul>
</div>
<div v-else>
  No products found.
</div>

    </div>
</template>
<script>
import { ref } from 'vue';
import { getRequest, postRequest, deleteRequest, patchRequest } from "../services/httpClient.js";

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
      updateProductData: {
        id: '',
        name: '',
        description: '',
        price: '',
        category_id: '',
        imageFile: null
      },
      deleteProductData: {
        id: ''
      },
      products: [],
      isLoading: false
    };
  },
  methods: {
    onFileChange(event) {
      this.productData.imageFile = event.target.files[0];

    },
    onUpdateFileChange(event) {
      this.updateProductData.imageFile = event.target.files[0];
    },
    async handleAddProduct() {
      try {
        const formData = new FormData();
        formData.append('name', this.productData.name);
        formData.append('description', this.productData.description);
        formData.append('price', this.productData.price);
        formData.append('category_id', this.productData.category_id);
        formData.append('image', this.productData.imageFile);

        await postRequest('products', formData);
    
      } catch (error) {
        // alert(error.message || 'Failed to add product');
      }
    },// Frontend: handleUpdateProduct method

// In your component
async handleUpdateProduct() {
  try {
    const formData = new FormData();
       formData.append('id', this.updateProductData.id);

   formData.append('name', this.updateProductData.name);
        formData.append('description', this.updateProductData.description);
        formData.append('price', this.updateProductData.price);
        formData.append('category_id', this.updateProductData.category_id);
        formData.append('image', this.updateProductData.imageFile);
    // Add other fields as needed

    if (this.updateProductData.imageFile) {
      formData.append('image', this.updateProductData.imageFile);
    }
    

    const response = await postRequest(
      `products/${this.updateProductData.id}`, 
formData);

    return response;
  } catch (error) {
    alert(error.message || 'Failed to update product');
    throw error;
  }
}

,
    async handleDeleteProduct() {
      try {
        await deleteRequest('products',this.deleteProductData.id);
        alert('Product deleted successfully');
        this.fetchProducts();
      } catch (error) {
        alert(error.message || 'Failed to delete product');
      }
    },
    async fetchProducts() {
      try {
        this.isLoading = true;
       const response = await getRequest('products');
        this.products=response.data

      } catch (error) {
        alert(error.message || 'Failed to fetch products');
      } finally {
        this.isLoading = false;
      }
    }
  },
  mounted() {
    this.fetchProducts();
  }
};
</script>



<style scoped>
form {
  margin-bottom: 20px;
}

input, textarea {
  margin: 5px;
  padding: 10px;
  width: 100%;
}

button {
  padding: 10px 15px;
  background-color: #007BFF;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #0056b3;
}
</style>