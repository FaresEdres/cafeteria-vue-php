<template>
  <section id="product-management" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 800px; margin: auto;">

      <!-- Add Product Form -->
      <div style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">Add Product</h2>
        
        <form @submit.prevent="handleAddProduct">
          <div style="margin-bottom: 20px;">
            <label for="name" style="display:block; margin-bottom: 5px; color: #232323;">Product Name</label>
            <input 
              v-model="productData.name" 
              type="text" 
              id="name" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="image" style="display:block; margin-bottom: 5px; color: #232323;">Image File</label>
            <input 
              type="file" 
              id="image" 
              @change="onFileChange" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="description" style="display:block; margin-bottom: 5px; color: #232323;">Description</label>
            <textarea 
              v-model="productData.description" 
              id="description" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              rows="5"
            ></textarea>
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="price" style="display:block; margin-bottom: 5px; color: #232323;">Price</label>
            <input 
              v-model="productData.price" 
              type="number" 
              id="price" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="category_id" style="display:block; margin-bottom: 5px; color: #232323;">Category ID</label>
            <input 
              v-model="productData.category_id" 
              type="number" 
              id="category_id" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
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

      <!-- Update Product Form -->
      <div style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">Update Product</h2>
        
        <form @submit.prevent="handleUpdateProduct">
          <div style="margin-bottom: 20px;">
            <label for="updateId" style="display:block; margin-bottom: 5px; color: #232323;">Product ID</label>
            <input 
              v-model="updateProductData.id" 
              type="number" 
              id="updateId" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="updateName" style="display:block; margin-bottom: 5px; color: #232323;">Product Name</label>
            <input 
              v-model="updateProductData.name" 
              type="text" 
              id="updateName" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="updateImage" style="display:block; margin-bottom: 5px; color: #232323;">Image File</label>
            <input 
              type="file" 
              id="updateImage" 
              @change="onUpdateFileChange" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="updateDescription" style="display:block; margin-bottom: 5px; color: #232323;">Description</label>
            <textarea 
              v-model="updateProductData.description" 
              id="updateDescription" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              rows="5"
            ></textarea>
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="updatePrice" style="display:block; margin-bottom: 5px; color: #232323;">Price</label>
            <input 
              v-model="updateProductData.price" 
              type="number" 
              id="updatePrice" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-bottom: 20px;">
            <label for="updateCategory" style="display:block; margin-bottom: 5px; color: #232323;">Category ID</label>
            <input 
              v-model="updateProductData.category_id" 
              type="number" 
              id="updateCategory" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-top: 30px; text-align: center;">
            <button 
              type="submit" 
              style="background-color: #c4ab9f; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
              :disabled="isLoading"
            >
              {{ isLoading ? "Updating..." : "Update Product" }}
            </button>
          </div>
        </form>
      </div>

      <!-- Delete Product Form -->
      <div style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">Delete Product</h2>
        
        <form @submit.prevent="handleDeleteProduct">
          <div style="margin-bottom: 20px;">
            <label for="deleteId" style="display:block; margin-bottom: 5px; color: #232323;">Product ID</label>
            <input 
              v-model="deleteProductData.id" 
              type="number" 
              id="deleteId" 
              class="form-control" 
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
              required 
            />
          </div>
          
          <div style="margin-top: 30px; text-align: center;">
            <button 
              type="submit" 
              style="background-color: #e74c3c; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
              :disabled="isLoading"
            >
              {{ isLoading ? "Deleting..." : "Delete Product" }}
            </button>
          </div>
        </form>
      </div>

      <!-- Products List Section -->
      <div v-if="isLoading" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #666;">Loading products...</p>
      </div>

      <div v-else-if="products && products.length > 0" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">All Products</h2>
        
        <div class="product-list" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
          <div v-for="product in products" :key="product.id" style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; background: #fff;">
            <div style="height: 200px; overflow: hidden; margin-bottom: 15px;">
              <img 
                v-if="product.image_url" 
                :src="product.image_url" 
                :alt="product.name" 
                style="width: 100%; height: 100%; object-fit: cover;"
              />
              <div v-else style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                <span>No Image</span>
              </div>
            </div>
            <h3 style="font-size: 18px; margin-bottom: 5px; color: #232323;">{{ product.name }}</h3>
            <p style="font-size: 14px; color: #666; margin-bottom: 5px;">ID: {{ product.id }}</p>
            <p style="font-size: 16px; color: #c4ab9f; font-weight: bold; margin-bottom: 10px;">${{ product.price }}</p>
            <p style="font-size: 14px; color: #666; margin-bottom: 5px;">Category ID: {{ product.category_id }}</p>
            <p style="font-size: 14px; color: #666; margin-bottom: 15px;">{{ product.description }}</p>
          </div>
        </div>
      </div>

      <div v-else style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #666;">No products found</p>
      </div>

    </div>
  </section>
</template>

<script>
import { getRequest, postRequest, deleteRequest } from "../services/httpClient.js";

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
      products: [], // Initialize as empty array
      isLoading: false,
      error: null
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
        this.isLoading = true;
        this.error = null;
        
        const formData = new FormData();
        formData.append('name', this.productData.name);
        formData.append('description', this.productData.description);
        formData.append('price', this.productData.price);
        formData.append('category_id', this.productData.category_id);
        formData.append('image', this.productData.imageFile);

        await postRequest('products', formData);
        
       
     

      } catch (error) {
        this.error = error.message || 'Failed to add product';
        console.error('Error adding product:', error);
      } finally {
        this.isLoading = false;
      }
    },
    async handleUpdateProduct() {
      try {
        this.isLoading = true;
        this.error = null;
        
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

        // Reset form after successful update
        this.updateProductData = {
          id: '',
          name: '',
          description: '',
          price: '',
          category_id: '',
          imageFile: null
        };
        return response;
      } catch (error) {
        this.error = error.message || 'Failed to update product';
        console.error('Error updating product:', error);
      } finally {
        this.isLoading = false;
      }
    },
    async handleDeleteProduct() {
      try {
        this.isLoading = true;
        this.error = null;
        
         await deleteRequest('products',this.deleteProductData.id);
        
        // Reset form after successful deletion
        this.deleteProductData = { id: '' };
      } catch (error) {
        this.error = error.message || 'Failed to delete product';
        console.error('Error deleting product:', error);
      } finally {
        this.isLoading = false;
      }
    },
   
    
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
  text-align: center;
  margin-top: 10px;
}
</style>