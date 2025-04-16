<template>
  <div class="login-container">
    <div class="login-header">
      <div class="armane">The Armané</div>
      <div class="cafe">Café</div>
    </div>
    
    <div class="login-form-container">
      <div class="login-form">
        <h3>Welcome Back</h3>
        <p>Sign in to access your account and order history</p>
        
        <form @submit.prevent="handleLogin">
          <div class="form-group">
            <label for="email">Email Address</label>
            <input 
              type="email" 
              id="email" 
              v-model="email" 
              placeholder="your@email.com"
              required
              @blur="validateEmail"
            >
            <span class="error-message" v-if="errors.email">{{ errors.email }}</span>
          </div>
          
          <div class="form-group">
            <label for="password">Password</label>
            <input 
              type="password" 
              id="password" 
              v-model="password" 
              placeholder="••••••••"
              required
              @input="validatePassword"
            >
            <span class="error-message" v-if="errors.password">{{ errors.password }}</span>
          </div>
          
          <button type="submit" class="login-button">Sign In</button>
        </form>
        
        <div class="divider">or</div>
        
        <p class="signup-text">Don't have an account? <router-link to="/contact">Contact Us!</router-link></p>
      </div>
      
      <div class="login-image">
        <img src="/intro_1.jpg" alt="The Armand Cafe Interior">
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/auth';
import { postRequest } from "../services/httpClient.js";

export default {
  data() {
    return {
      email: '',
      password: '',
      errors: {
        email: '',
        password: ''
      },
      // Instantiate the auth store
      authStore: useAuthStore()
    };
  },
  created() {
    // Check if the user is already logged in. If so, redirect them to another page.
    if (this.authStore.isLoggedIn) {
      this.$router.push('/menu');
    }
  },
  methods: {
    validateEmail() {
      this.errors.email = '';
      if (!this.email) {
        this.errors.email = 'Email is required';
        return false;
      }
      if (!/^\S+@\S+\.\S+$/.test(this.email)) {
        this.errors.email = 'Please enter a valid email';
        return false;
      }
      return true;
    },
    validatePassword() {
      this.errors.password = '';
      if (!this.password) {
        this.errors.password = 'Password is required';
        return false;
      }
      if (this.password.length < 8) {
        this.errors.password = 'Password must be at least 8 characters';
        return false;
      }
      return true;
    },
    validateForm() {
      const validEmail = this.validateEmail();
      const validPassword = this.validatePassword();
      return validEmail && validPassword;
    },

    // async handleLogin() {
    //   if (!this.validateForm()) return;
      
    //   try {
    //     const formData = new FormData();
    //     formData.append('email', this.email);
    //     formData.append('password', this.password);
        
    //     // Send the login request
    //     const response = await postRequest('login', formData);
    //     console.log('Login response:', response);
        
    //     // After a successful login attempt, call fetchUser() to update the auth state.
    //     await this.authStore.fetchUser();
        
    //     // If the user is now logged in, redirect them.
    //     if (this.authStore.isLoggedIn) {
    //       this.$router.push('/menu');
    //     } else {
    //       console.error('Login failed: user still not authenticated');
    //     }
    //   } catch (error) {
    //     console.error('Login failed:', error);
    //   }
    // }

    async handleLogin() {
  if (!this.validateForm()) return;
  
  try {
    const formData = new FormData();
    formData.append('email', this.email);
    formData.append('password', this.password);
    
    const response = await postRequest('login', formData);
    console.log('Login response:', response);
    
    // Update auth store with the latest user info
    await this.authStore.fetchUser();
    
    // Check user's role and route accordingly
    if (this.authStore.user && this.authStore.user.role === 'admin') {
      this.$router.push('/admin');
    } else if (this.authStore.isLoggedIn) {
      this.$router.push('/menu');
    } else {
      console.error('Login failed: user still not authenticated');
    }
  } catch (error) {
    console.error('Login failed:', error);}
  }
  }
};
</script>

<style scoped>
.armane{
  font-family: 'Dancing Script', cursive;
  font-size: 50px;
}
.cafe{
  margin-left: 0px !important;
  font-family: 'Dancing Script', cursive;
  font-size: 50px;
}
.login-container {
  font-family: 'Playfair Display', serif;
  color: #3E2723;
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-form-container {
  display: flex;
  background: #FFF9F0;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  height: 600px;
}

.login-form {
  padding: 3rem;
  flex: 1;
}

.login-form h3 {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
}

.login-form p {
  color: #6D4C41;
  margin-bottom: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.form-group input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #D7CCC8;
  border-radius: 4px;
  font-size: 1rem;
  transition: border-color 0.3s;
}

.form-group input:focus {
  outline: none;
  border-color: #8D6E63;
}

.error-message {
  color: #d32f2f;
  font-size: 0.8rem;
  margin-top: 0.25rem;
  display: block;
}

.login-button {
  width: 100%;
  padding: 14px;
  background-color: #5D4037;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.3s;
}

.login-button:hover {
  background-color: #3E2723;
}

.divider {
  display: flex;
  align-items: center;
  margin: 2rem 0;
  color: #A1887F;
}

.divider::before, .divider::after {
  content: "";
  flex: 1;
  border-bottom: 1px solid #D7CCC8;
}

.divider::before {
  margin-right: 1rem;
}

.divider::after {
  margin-left: 1rem;
}

.signup-text {
  text-align: center;
  margin-top: 2rem;
  color: #6D4C41;
}

.signup-text a {
  color: #5D4037;
  font-weight: 500;
  text-decoration: none;
}

.login-image {
  flex: 1;
  display: flex;
}

.login-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

@media (max-width: 768px) {
  .login-form-container {
    flex-direction: column;
  }
  
  .login-image {
    display: none;
  }
}

</style>
