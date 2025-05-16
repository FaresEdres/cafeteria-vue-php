<template>
  <section id="user-management" style="padding: 60px 0; background-color: #f9f5f2;">
    <div class="super_container" style="max-width: 800px; margin: auto;">

      <div
        style="margin-bottom: 40px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <h2 class="page_subtitle" style="text-align: center; font-size: 36px; margin-bottom: 20px;">Add New User</h2>

        <form @submit.prevent="handleAddUser">
          <div style="margin-bottom: 20px;">
            <label for="firstname" style="display:block; margin-bottom: 5px; color: #232323;">First Name</label>
            <input v-model="userData.firstname" type="text" id="firstname" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" />
            <p v-if="errors.firstname" class="error-message">{{ errors.firstname }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="lastname" style="display:block; margin-bottom: 5px; color: #232323;">Last Name</label>
            <input v-model="userData.lastname" type="text" id="lastname" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" />
            <p v-if="errors.lastname" class="error-message">{{ errors.lastname }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="email" style="display:block; margin-bottom: 5px; color: #232323;">Email</label>
            <input v-model="userData.email" type="email" id="email" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" />
            <p v-if="errors.email" class="error-message">{{ errors.email }}</p>
          </div>


          <div style="margin-bottom: 20px;">
            <label for="password" style="display:block; margin-bottom: 5px; color: #232323;">Password</label>
            <input v-model="userData.password" type="password" id="password" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" @input="validatePassword" />
            <p v-if="errors.password" class="error-message">{{ errors.password }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="confirmPassword" style="display:block; margin-bottom: 5px; color: #232323;">Confirm
              Password</label>
            <input v-model="userData.confirmPassword" type="password" id="confirmPassword" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" @input="validatePasswordMatch" />
            <p v-if="errors.confirmPassword" class="error-message">{{ errors.confirmPassword }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="room" style="display:block; margin-bottom: 5px; color: #232323;">Room</label>
            <input v-model="userData.room" type="text" id="room" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" />
            <p v-if="errors.room" class="error-message">{{ errors.room }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="ext" style="display:block; margin-bottom: 5px; color: #232323;">Extension</label>
            <input v-model="userData.ext" type="text" id="ext" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" />
            <p v-if="errors.ext" class="error-message">{{ errors.ext }}</p>
          </div>

          <div style="margin-bottom: 20px;">
            <label for="image" style="display:block; margin-bottom: 5px; color: #232323;">Profile Image</label>
            <input type="file" id="image" @change="onFileChange" class="form-control"
              style="width:100%; padding:10px; border: 1px solid #c4ab9f;" accept="image/*" />
            <p v-if="errors.image" class="error-message">{{ errors.image }}</p>
          </div>

          <div style="margin-top: 30px; text-align: center;">
            <button type="submit"
              style="background-color: #c4ab9f; color: white; padding: 12px 30px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;"
              :disabled="isLoading">
              {{ isLoading ? "Processing..." : "Add User" }}
            </button>
          </div>
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
      userData: {
        firstname: '',
        lastname: '',
        email: '',
        password: '',
        confirmPassword: '',
        room: '',
        ext: '',
        image: null
      },
      isLoading: false,
      error: null,
      errors: {}
    };
  },
  methods: {
    onFileChange(event) {
      this.userData.image = event.target.files[0];
    },
    validatePassword() {
      this.errors.password = '';
      if (!this.userData.password) {
        this.errors.password = 'Password is required';
        return false;
      }
      if (this.userData.password.length < 8) {
        this.errors.password = 'Password must be at least 8 characters';
        return false;
      }
      if (this.userData.confirmPassword) {
        this.validatePasswordMatch();
      }
      return true;
    },
    validatePasswordMatch() {
      this.errors.confirmPassword = '';
      if (!this.userData.confirmPassword) {
        this.errors.confirmPassword = 'Please confirm your password';
        return false;
      }
      if (this.userData.password !== this.userData.confirmPassword) {
        this.errors.confirmPassword = 'Passwords do not match';
        return false;
      }
      return true;
    },
    validateForm() {
      const errors = {};
      if (!this.userData.firstname) errors.firstname = 'First name is required';
      if (!this.userData.lastname) errors.lastname = 'Last name is required';
      if (!this.userData.email) {
        errors.email = 'Email is required';
      } else if (!/^\S+@\S+\.\S+$/.test(this.userData.email)) {
        errors.email = 'Please enter a valid email';
      }

      if (!this.userData.password) {
        errors.password = 'Password is required';
      } else if (this.userData.password.length < 8) {
        errors.password = 'Password must be at least 8 characters';
      }

      if (!this.userData.confirmPassword) {
        errors.confirmPassword = 'Please confirm your password';
      } else if (this.userData.password !== this.userData.confirmPassword) {
        errors.confirmPassword = 'Passwords do not match';
      }

      if (!this.userData.room) errors.room = 'Room is required';
      if (!this.userData.ext) errors.ext = 'Extension is required';
      if (!this.userData.image) errors.image = 'Profile image is required';

      this.errors = errors;
      return Object.keys(errors).length === 0;
    },
    async handleAddUser() {
      if (!this.validateForm()) return;

      try {
        this.isLoading = true;
        this.error = null;

        const formData = new FormData();
        formData.append('firstname', this.userData.firstname);
        formData.append('lastname', this.userData.lastname);
        formData.append('email', this.userData.email);
        formData.append('password', this.userData.password);
        formData.append('room', this.userData.room);
        formData.append('ext', this.userData.ext);
        formData.append('image', this.userData.image);

        const message = await postRequest('users', formData);



        this.userData = {
          firstname: '',
          lastname: '',
          email: '',
          password: '',
          confirmPassword: '',
          room: '',
          ext: '',
          image: null
        };
        if (message['error']) {
          this.error = message['error'];
        } else {
          // toast
        }

      } catch (error) {
        this.error = error.message || 'Failed to add user';
        if (error.response && error.response.data && error.response.data.error) {
          if (error.response.data.error.includes('email')) {
            this.errors.email = 'This email is already registered';
          }
        }
        console.error('Error adding user:', error);
      } finally {
        this.isLoading = false;
      }
    }
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
</style>
