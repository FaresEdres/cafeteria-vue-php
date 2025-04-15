<template>
  <section id="add-category" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 600px; margin: auto;">
      <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 style="text-align: center; font-size: 32px; margin-bottom: 20px;">Add Category</h2>

        <form @submit.prevent="handleAddCategory">
          <div style="margin-bottom: 20px;">
            <label for="name" style="display: block; margin-bottom: 5px; color: #232323;">Category Name</label>
            <input
              v-model="categoryData.name"
              type="text"
              id="name"
              class="form-control"
              placeholder="Enter category name"
              style="width: 100%; padding: 10px; border: 1px solid #c4ab9f;"
            />
          </div>

          <div style="text-align: center;">
            <button
              type="submit"
              style="background-color: #c4ab9f; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
              :disabled="isLoading"
            >
              {{ isLoading ? "Adding..." : "Add Category" }}
            </button>
          </div>

          <p v-if="successMessage" style="color: green; text-align: center; margin-top: 15px;">{{ successMessage }}</p>
          <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
        </form>
      </div>
    </div>
  </section>
</template>

<script>
import { postRequest } from "../services/httpClient.js";
export default {
  data() {
    return {
      categoryData: {
        name: ''
      },
      isLoading: false,
      errorMessage: '',
      successMessage: ''
    };
  },
  methods: {
    async handleAddCategory() {
      this.isLoading = true;
      this.errorMessage = '';
      this.successMessage = '';

      try {
        if (!this.categoryData.name.trim()) {
          this.errorMessage = 'Category name is required.';
          this.isLoading = false;
          return;
        }

        const formData = new FormData();
        formData.append('name', this.categoryData.name);
        
         await postRequest('categories', formData);

        this.successMessage = 'Category added successfully!';
        this.categoryData.name = '';
      } catch (err) {
        this.errorMessage = err.response?.data?.message || 'Failed to add category';
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style scoped>
.form-control:focus {
  outline: none;
  border-color: #b08d7a;
  box-shadow: 0 0 0 2px rgba(192, 152, 127, 0.2);
}

button:hover {
  opacity: 0.9;
}

button:disabled {
  background-color: #cccccc !important;
  cursor: not-allowed;
}

.error-message {
  color: #e74c3c;
  text-align: center;
  margin-top: 10px;
}
</style>
