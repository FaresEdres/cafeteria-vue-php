<template>
    <section id="user-management" style="padding: 60px 0; background-color: #f9f5f2;">
      <div class="super_container" style="max-width: 1200px; margin: auto;">
  
        <!-- User List Table -->
        <div style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
          <h2 class="page_subtitle" style="font-size: 36px; margin-bottom: 30px;">User Management</h2>
  
          <!-- Error Message -->
          <div v-if="fetchError" class="error-message" style="text-align: center; margin-bottom: 20px;">
            {{ fetchError }}
          </div>
  
          <!-- Loading State -->
          <div v-if="isLoading" style="text-align: center; padding: 40px;">
            Loading users...
          </div>
  
          <!-- Users Table -->
          <div v-else-if="users.length > 0">
            <table style="width: 100%; border-collapse: collapse;">
              <thead>
                <tr style="background-color: #f5f5f5;">
                  <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Name</th>
                  <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Email</th>
                  <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Room</th>
                  <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Extension</th>
                  <th style="padding: 12px; text-align: left; border-bottom: 2px solid #ddd;">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id" style="border-bottom: 1px solid #ddd;">
                  <td style="padding: 12px;">{{ user.firstname }} {{ user.lastname }}</td>
                  <td style="padding: 12px;">{{ user.email }}</td>
                  <td style="padding: 12px;">{{ user.room }}</td>
                  <td style="padding: 12px;">{{ user.ext }}</td>
                  <td style="padding: 12px;">
                    <button 
                      @click="editUser(user)"
                      style="background-color: #c4ab9f; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; margin-right: 5px;"
                    >
                      Edit
                    </button>
                    <button 
                      @click="confirmDelete(user.id)"
                      style="background-color: #e74c3c; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer;"
                    >
                      Delete
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
  
          <!-- Empty State -->
          <div v-else style="text-align: center; padding: 40px;">
            No users found
          </div>
        </div>
  
        <!-- Edit User Form Modal -->
        <div v-if="showEditForm" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
          <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 600px; max-width: 90%; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
              <h2 style="font-size: 24px;">Edit User</h2>
              <button @click="closeForm" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
            </div>
  
            <form @submit.prevent="handleUpdateUser()">
              <!-- First Name -->
              <div style="margin-bottom: 20px;">
                <label for="firstname" style="display:block; margin-bottom: 5px; color: #232323;">First Name</label>
                <input 
                  v-model="userData.firstname" 
                  type="text" 
                  id="firstname" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                />
                <p v-if="errors.firstname" class="error-message">{{ errors.firstname }}</p>
              </div>
              
              <!-- Last Name -->
              <div style="margin-bottom: 20px;">
                <label for="lastname" style="display:block; margin-bottom: 5px; color: #232323;">Last Name</label>
                <input 
                  v-model="userData.lastname" 
                  type="text" 
                  id="lastname" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                />
                <p v-if="errors.lastname" class="error-message">{{ errors.lastname }}</p>
              </div>
              
              <!-- Email -->
              <div style="margin-bottom: 20px;">
                <label for="email" style="display:block; margin-bottom: 5px; color: #232323;">Email</label>
                <input 
                  v-model="userData.email" 
                  type="email" 
                  id="email" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                />
              </div>
              
              <!-- Room -->
              <div style="margin-bottom: 20px;">
                <label for="room" style="display:block; margin-bottom: 5px; color: #232323;">Room</label>
                <input 
                  v-model="userData.room" 
                  type="text" 
                  id="room" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                />
                <p v-if="errors.room" class="error-message">{{ errors.room }}</p>
              </div>
              
              <!-- Extension -->
              <div style="margin-bottom: 20px;">
                <label for="ext" style="display:block; margin-bottom: 5px; color: #232323;">Extension</label>
                <input 
                  v-model="userData.ext" 
                  type="text" 
                  id="ext" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                />
                <p v-if="errors.ext" class="error-message">{{ errors.ext }}</p>
              </div>
              
              <!-- Profile Image -->
              <div style="margin-bottom: 20px;">
                <label for="image" style="display:block; margin-bottom: 5px; color: #232323;">Profile Image</label>
                <input 
                  type="file" 
                  id="image" 
                  @change="onFileChange" 
                  class="form-control" 
                  style="width:100%; padding:10px; border: 1px solid #c4ab9f;"
                  accept="image/*"
                />
                <div v-if="userData.imagePreview" style="margin-top: 10px;">
                  <img :src="userData.imagePreview" style="max-width: 100px; max-height: 100px; border-radius: 4px;" />
                </div>
              </div>
              
              <!-- Submit Button -->
              <div style="margin-top: 30px; text-align: center;">
                <button 
                  type="submit" 
                  style="background-color: #c4ab9f; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
                  :disabled="isLoading"
                >
                  {{ isLoading ? "Updating..." : "Update User" }}
                </button>
              </div>
            </form>
          </div>
        </div>
  
        <!-- Delete Confirmation Modal -->
        <div v-if="showDeleteConfirm" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
          <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 400px; max-width: 90%;">
            <h3 style="font-size: 20px; margin-bottom: 20px;">Confirm Delete</h3>
            <p style="margin-bottom: 30px;">Are you sure you want to delete this user?</p>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
              <button 
                @click="showDeleteConfirm = false"
                style="background-color: #ccc; color: #333; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;"
              >
                Cancel
              </button>
              <button 
                @click="handleDeleteUser"
                style="background-color: #e74c3c; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;"
                :disabled="isLoading"
              >
                {{ isLoading ? "Deleting..." : "Delete" }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script>
  import { getRequest, postRequest, deleteRequest } from "../services/httpClient.js";
  
  export default {
    data() {
      return {
        users: [],
        userData: {
          id: null,
          firstname: '',
          lastname: '',
          email: '',
          room: '',
          ext: '',
          image: null,
          imagePreview: null
        },
        isLoading: false,
        fetchError: null,
        showEditForm: false,
        showDeleteConfirm: false,
        userIdToDelete: null,
        errors: {}
      };
    },
    methods: {
      onFileChange(event) {
        const file = event.target.files[0];
        if (file) {
          this.userData.image = file;
          this.userData.imagePreview = URL.createObjectURL(file);
        }
      },
      validateForm() {
        const errors = {};
        if (!this.userData.firstname) errors.firstname = 'First name is required';
        if (!this.userData.lastname) errors.lastname = 'Last name is required';
        if (!this.userData.room) errors.room = 'Room is required';
        if (!this.userData.ext) errors.ext = 'Extension is required';
        
        this.errors = errors;
        return Object.keys(errors).length === 0;
      },
      async fetchUsers() {
        try {
          this.isLoading = true;
          this.fetchError = null;
          const response = await getRequest('users');
          
          if (response.error) {
            this.fetchError = response.error;
            this.users = [];
          } else {
            this.users = response;
          }
        } catch (error) {
          this.fetchError = error.message || 'Failed to fetch users';
          console.error('Error fetching users:', error);
        } finally {
          this.isLoading = false;
        }
      },
      async handleUpdateUser() {
        if (!this.validateForm()) return;
  
        try {
          this.isLoading = true;
          this.error = null;
  
          const formData = new FormData();
          formData.append('firstname', this.userData.firstname);
          formData.append('lastname', this.userData.lastname);
          formData.append('room', this.userData.room);
          formData.append('ext', this.userData.ext);
          formData.append('email', this.userData.email);
          if (this.userData.image) {
            formData.append('image', this.userData.image);
          }
  
          await postRequest(`users/${this.userData.id}`, formData);
          this.closeForm();
          this.fetchUsers();
        } catch (error) {
          this.error = error.message || 'Failed to update user';
          console.error('Error updating user:', error);
        } finally {
          this.isLoading = false;
        }
      },
      editUser(user) {
        this.userData = {
          id: user.id,
          firstname: user.firstname,
          lastname: user.lastname,
          email: user.email,
          room: user.room,
          ext: user.ext,
          image: null,
          imagePreview: user.image ? `http://localhost:8000/public/uploads/users/${user.image}` : null
        };
        this.showEditForm = true;
      },
      confirmDelete(userId) {
        this.userIdToDelete = userId;
        this.showDeleteConfirm = true;
      },
      async handleDeleteUser() {
        try {
          this.isLoading = true;
          await deleteRequest(`users`, this.userIdToDelete);
          this.showDeleteConfirm = false;
          this.fetchUsers();
        } catch (error) {
          this.error = error.message || 'Failed to delete user';
          console.error('Error deleting user:', error);
        } finally {
          this.isLoading = false;
        }
      },
      closeForm() {
        this.userData = {
          id: null,
          firstname: '',
          lastname: '',
          email: '',
          room: '',
          ext: '',
          image: null,
          imagePreview: null
        };
        this.errors = {};
        this.showEditForm = false;
      }
    },
    mounted() {
      this.fetchUsers();
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
  
  .error-message {
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
  }
  
  table {
    width: 100%;
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
  </style>