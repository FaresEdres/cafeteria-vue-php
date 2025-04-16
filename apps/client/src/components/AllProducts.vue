<template>
  <section id="product-management" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 1200px; margin: auto;">
      

      <!-- Loading Section -->
      <div v-if="isLoading" style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
        <p>Loading products...</p>
      </div>

      <!-- Products List Section -->
      <div v-else-if="products && products.length > 0" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 30px; color: #3E2723;">All Products</h2>

        <table style="width: 100%; border-collapse: collapse;">
          <thead>
            <tr style="background-color: #f5f5f5;">
              <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">ID</th>
              <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Image</th>
              <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Product Name</th>
              <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Price</th>
              <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in paginatedProducts" :key="product.id" style="border-bottom: 1px solid #ddd;">
              <td style="padding: 12px;">{{ product.id }}</td>
              <td style="padding: 12px;">
                <img 
                  v-if="product.image"
                  width="50px"
                  :src="`http://localhost:8000/public/uploads/` + product.image" 
                  :alt="product.name"
                  style="border-radius: 4px;"
                >
                <div v-else style="width: 50px; height: 50px; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                  <span>No Image</span>
                </div>
              </td>
              <td style="padding: 12px;">{{ product.name }}</td>
              <td style="padding: 12px;">${{ product.price }}</td>
              <td style="padding: 12px;">
                <button 
                  @click="openEditModal(product)" 
                  style="background-color: #c4ab9f; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;"
                >
                  Edit
                </button>
                <button 
                  @click="confirmDelete(product.id)" 
                  style="background-color: #e74c3c; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;"
                >
                  Delete
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Pagination Controls -->
        <div style="text-align: center; margin-top: 30px;">
          <button
            style="background-color: #5D4037; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;"
            :disabled="currentPage === 1"
            @click="goToPage(currentPage - 1)"
          >
            Previous
          </button>
          <span style="margin: 0 15px; color: #5D4037; font-weight: 500;">Page {{ currentPage }} of {{ totalPages }}</span>
          <button
            style="background-color: #5D4037; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;"
            :disabled="currentPage === totalPages"
            @click="goToPage(currentPage + 1)"
          >
            Next
          </button>
        </div>
      </div>

      <!-- No Products Message -->
      <div v-else style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center;">
        <p style="color: #5D4037;">No products found</p>
      </div>

      <!-- Edit Product Modal -->
      <div v-if="showEditModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 600px; max-width: 90%; max-height: 90vh; overflow-y: auto;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; color: #3E2723;">Edit Product</h2>
            <button @click="closeEditModal" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #5D4037;">&times;</button>
          </div>
          <form @submit.prevent="handleUpdateProduct">
            <div style="margin-bottom: 20px;">
              <label style="display:block; margin-bottom: 8px; color: #232323; font-weight: 500;">Product Name</label>
              <input 
                v-model="editProductData.name" 
                type="text" 
                style="width:100%; padding:12px; border: 1px solid #D7CCC8; border-radius: 4px;"
              />
            </div>
            <div style="margin-bottom: 20px;">
              <label style="display:block; margin-bottom: 8px; color: #232323; font-weight: 500;">Image</label>
              <input 
                type="file" 
                @change="onEditFileChange" 
                style="width:100%; padding:12px; border: 1px solid #D7CCC8; border-radius: 4px;"
              />
              <small style="color: #8D6E63;">Leave empty to keep current image</small>
            </div>
            <div style="margin-bottom: 20px;">
              <label style="display:block; margin-bottom: 8px; color: #232323; font-weight: 500;">Description</label>
              <textarea 
                v-model="editProductData.description" 
                rows="3"
                style="width:100%; padding:12px; border: 1px solid #D7CCC8; border-radius: 4px;"
              ></textarea>
            </div>
            <div style="margin-bottom: 20px;">
              <label style="display:block; margin-bottom: 8px; color: #232323; font-weight: 500;">Price</label>
              <input 
                v-model="editProductData.price" 
                type="number" 
                style="width:100%; padding:12px; border: 1px solid #D7CCC8; border-radius: 4px;"
              />
            </div>
            <div style="margin-bottom: 20px;">
              <label style="display:block; margin-bottom: 8px; color: #232323; font-weight: 500;">Category</label>
              <div style="display: flex; align-items: center; gap: 10px;">
                <select 
                  v-model="editProductData.category_id" 
                  style="flex: 1; padding:12px; border: 1px solid #D7CCC8; border-radius: 4px;"
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
                  style="padding: 12px 14px; background-color: #5D4037; border: none; border-radius: 4px; color: white; font-size: 16px; cursor: pointer;"
                >
                  +
                </button>
              </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 30px;">
              <button 
                type="button" 
                @click="closeEditModal" 
                style="background-color: #8D6E63; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer;"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                :disabled="isLoading"
                style="background-color: #5D4037; color: white; padding: 12px 20px; border: none; border-radius: 4px; cursor: pointer;"
              >
                {{ isLoading ? 'Updating...' : 'Update Product' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
        <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px; max-width: 90%;">
          <h2 style="font-size: 24px; margin-bottom: 20px; color: #3E2723;">Confirm Delete</h2>
          <p style="margin-bottom: 30px; color: #5D4037;">Are you sure you want to delete this product?</p>
          <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button 
              @click="closeDeleteModal"
              style="background-color: #8D6E63; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;"
            >
              Cancel
            </button>
            <button 
              @click="handleDeleteProduct" 
              :disabled="isLoading"
              style="background-color: #e74c3c; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;"
            >
              {{ isLoading ? 'Deleting...' : 'Delete' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { getRequest, postRequest, deleteRequest, patchRequest } from "../services/httpClient.js";

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
        formData.append('id', this.editProductData.id);
        formData.append('name', this.editProductData.name);
        formData.append('description', this.editProductData.description);
        formData.append('price', this.editProductData.price);
        formData.append('category_id', this.editProductData.category_id);
        if (this.editProductData.imageFile) {
          formData.append('image', this.editProductData.imageFile);
        }
        
        const response = await patchRequest(
          `products/${this.editProductData.id}`, 
          formData
        );
        console.log(response);
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
/* Form control styling to match user management */
input, textarea, select {
  transition: border-color 0.3s ease;
  border: 1px solid #D7CCC8;
  border-radius: 4px;
  padding: 10px;
  width: 100%;
}

input:focus, textarea:focus, select:focus {
  outline: none;
  border-color: #8D6E63;
  box-shadow: 0 0 0 2px rgba(141, 110, 99, 0.2);
}

button {
  transition: all 0.3s ease;
}

button:hover:not(:disabled) {
  opacity: 0.9;
}

button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Table styling to match user management */
table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #ddd;
}

th {
  background-color: #f5f5f5;
  font-weight: 600;
}

tr:hover {
  background-color: #f9f9f9;
}

/* Modal styling to match user management */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  padding: 30px;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  width: 600px;
  max-width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

/* Button colors to match user management */
.btn-primary {
  background-color: #5D4037;
  color: white;
}

.btn-secondary {
  background-color: #8D6E63;
  color: white;
}

.btn-danger {
  background-color: #e74c3c;
  color: white;
}
</style>     