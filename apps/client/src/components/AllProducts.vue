use my http client to make delete work

<template>
  <section id="product-management" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 800px; margin: auto;">
      
      <!-- Add Product Button (opens modal) -->
<div style="text-align: right; margin-bottom: 20px;">
  <router-link 
    to="/add-product"
    class="btn btn-primary"
    style="background-color: #c4ab9f; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none;"
  >
    Add Product
  </router-link>
</div>


      <!-- Loading Section -->
      <div v-if="isLoading" class="loading-section">
        <p>Loading products...</p>
      </div>

      <!-- Products List Section -->
      <div v-else-if="products && products.length > 0" class="product-table-section">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">All Products</h2>

        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Product Name</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in paginatedProducts" :key="product.id">
              <td>{{ product.id }}</td>
              <td>
               
                <img 
                 v-if="product.image"
                 width="50px"
                :src="`http://localhost:8000/public/uploads/` + product.image" :alt="product.name">

                <div v-else style="width: 50px; height: 50px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                  <span>No Image</span>
                </div>
              </td>
              <td>{{ product.name }}</td>
              <td>{{ product.price }}</td>
              <td>
                <button @click="openEditModal(product)" class="btn btn-warning" style="margin-right: 5px;">Edit</button>
                <button @click="confirmDelete(product.id)" class="btn btn-danger">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="pagination-controls" style="text-align: center; margin-top: 20px;">
          <button
            class="btn btn-secondary"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Previous
          </button>
          <span style="margin: 0 15px;">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            class="btn btn-secondary"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>

      <!-- No Products Message -->
      <div v-else style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #666;">No products found</p>
      </div>

    

      <!-- Edit Product Modal -->
      <div v-if="showEditModal" class="modal-overlay">
        <div class="modal-content">
          <h2>Edit Product</h2>
          <form @submit.prevent="handleUpdateProduct">
            <div class="form-group">
              <label>Product Name</label>
              <input v-model="editProductData.name" type="text"  />
            </div>
            <div class="form-group">
              <label>Image</label>
              <input type="file" @change="onEditFileChange" />
              <small>Leave empty to keep current image</small>
            </div>
            <div class="form-group">
              <label>Description</label>
              <textarea v-model="editProductData.description" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label>Price</label>
              <input v-model="editProductData.price" type="number"  />
            </div>
            <div class="form-group">
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
            <div class="modal-actions">
              <button type="button" @click="closeEditModal" class="btn btn-secondary">Cancel</button>
              <button type="submit" :disabled="isLoading" class="btn btn-primary">
                {{ isLoading ? 'Updating...' : 'Update Product' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="modal-overlay">
        <div class="modal-content">
          <h2>Confirm Delete</h2>
          <p>Are you sure you want to delete this product?</p>
          <div class="modal-actions">
            <button @click="closeDeleteModal" class="btn btn-secondary">Cancel</button>
            <button @click="handleDeleteProduct" :disabled="isLoading" class="btn btn-danger">
              {{ isLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { getRequest, postRequest, deleteRequest,patchRequest } from "../services/httpClient.js";

export default {
  data() {
    return {
      isLoading: false,
      products: [],          
      currentPage: 1,
      itemsPerPage: 5,
      totalPages: 1,
      
      // Modal states
      showEditModal: false,
      showDeleteModal: false,
      
      // Form data
      productData: {
        name: '',
        description: '',
        price: '',
        category_id: '',
        imageFile: null
      },
      editProductData: {
        id: '',
        name: '',
        description: '',
        price: '',
        category_id: '',
        imageFile: null
      },
      deleteProductId: null,
      categories: [],
    };
  },
  computed: {
    paginatedProducts() {
        
      return this.products;
    }
  },
  methods: {
    async fetchProducts() {
      try {
        this.isLoading = true;
        const response = await getRequest(`/products?page=${this.currentPage}`);
          const categories = await getRequest('categories');
        this.categories = categories;
        if (response.success) {
          this.products = response.data;
          this.totalPages = response.pagination.totalPages;
          this.currentPage = response.pagination.currentPage;
        }
      } catch (error) {
        alert(error.message || 'Failed to fetch products');
      } finally {
        this.isLoading = false;
      }
    },
    
    // Modal handlers
    
    openEditModal(product) {
      this.showEditModal = true;
      this.editProductData = {
        id: product.id,
        name: product.name,
        description: product.description,
        price: product.price,
        category_id: product.category_id,
        imageFile: null
      };
    },
    
    closeEditModal() {
      this.showEditModal = false;
    },
    
    confirmDelete(id) {
      this.showDeleteModal = true;
      this.deleteProductId = id;
    },
    
    closeDeleteModal() {
      this.showDeleteModal = false;
      this.deleteProductId = null;
    },
    
    // File handlers
    onFileChange(event) {
      this.productData.imageFile = event.target.files[0];
    },
    
    onEditFileChange(event) {
      this.editProductData.imageFile = event.target.files[0];
    },
    
    // CRUD operations
   
    
    async handleUpdateProduct() {
      try {
        this.isLoading = true;
        
        const formData = new FormData();
        formData.append('id',this.editProductData.id)
        formData.append('name', this.editProductData.name);
        formData.append('description', this.editProductData.description);
        formData.append('price', this.editProductData.price);
        formData.append('category_id', this.editProductData.category_id);
        if (this.editProductData.imageFile) {
          formData.append('image', this.editProductData.imageFile);
        }
        
         const response = await patchRequest(
      `products/${this.editProductData.id}`, 
formData);
console.log(response)
          this.closeEditModal();
          this.fetchProducts(); // Refresh the list
             
        return response;
      } catch (error) {
        alert(error.message || 'Failed to update product');
      } finally {
        this.isLoading = false;
      }
    },
    
    async handleDeleteProduct() {
      try {
        this.isLoading = true;
       await deleteRequest('products', this.deleteProductId);

       
        this.products = this.products.filter(p => p.id !== this.deleteProductId); // update UI
    
    this.closeDeleteModal();
      } catch (error) {
        alert(error.message || 'Failed to delete product');
      } finally {
        this.isLoading = false;
      }
    },
    
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
        this.fetchProducts();
      }
    }
  },
  
 
  
  mounted() {
    this.fetchProducts();
  }
};
</script>

<style scoped>
.loading-section {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  text-align: center;
}

.product-table-section {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

.table th {
  background-color: #f8f9fa;
}

.btn {
  padding: 8px 12px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-primary {
  background-color: #c4ab9f;
  color: white;
}

.btn-warning {
  background-color: #ffc107;
  color: #212529;
}

.btn-danger {
  background-color: #dc3545;
  color: white;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}

.pagination-controls {
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Modal styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 30px;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.modal-content h2 {
  margin-top: 0;
  margin-bottom: 20px;
  text-align: center;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: 500;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
  gap: 10px;
}
</style>