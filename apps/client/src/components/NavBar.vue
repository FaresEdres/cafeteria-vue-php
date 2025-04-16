<template>
  <div class="header_content d-flex flex-row align-items-center justify-content-start">
    <div class="logo">
      <a href="#">
        <div class="armane">The Armané</div>
        <div class="cafe">Café</div>
      </a>
    </div>
    <nav class="main_nav">
      <ul class="d-flex flex-row align-items-center justify-content-start">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/orders">My Orders</router-link></li>
        <li><router-link to="/menu">Menu</router-link></li>
        <li><router-link to="/contact">Contact</router-link></li>
        <li><router-link to="/about">About Us</router-link></li>
        <li v-if="!isLoggedIn">
       <router-link to="/login">Login</router-link>
       </li>
       <li v-else>
          <button @click="logout" class="logout-btn">Logout</button>
       </li>

      </ul>
    </nav>
    <div class="coffee_icon_wrapper ml-auto position-relative">
      <button @click="toggleSidebar" class="coffee_link">
        <i class="fas fa-mug-hot coffee_icon"></i>
        <span class="coffee_count_badge">{{ orderCount }}</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, inject, computed } from 'vue';
import { useOrderStore } from '../stores/order.js';
import { postRequest } from '@/services/httpClient';
import { useAuthStore } from '../stores/auth'; // auth store


export default {
  name: 'Navbar',
  setup() {
    const toggleSidebar = inject('toggleSidebar');
    const orderStore = useOrderStore();
    const authStore = useAuthStore(); // ✅ use auth store


    const orderCount = computed(() => orderStore.getOrder().products.length);

    const logout = async () => {
      await authStore.logout();
    };

    onMounted(() => {
      authStore.fetchUser(); // ✅ check auth on mount
    });


    return {
      toggleSidebar,
      orderCount,
      isLoggedIn: computed(() => authStore.isLoggedIn), // ✅ reactive!
      logout,
    };
  },
};


// export default {
//   name: 'Navbar',
//   setup() {
//     const toggleSidebar = inject('toggleSidebar');
//     const orderStore = useOrderStore();
//     const authStore = useAuthStore(); // ✅ use auth store


//     const isLoggedIn = ref(false);

//     const checkAuth = async () => {
//       try {
//         const response = await postRequest('authenticated');
//         console.log("authoeization request",response);
//         if (response && response.role) {
//           isLoggedIn.value = true;          
//         } else {
//           isLoggedIn.value = false;
//         }
//       } catch (e) {
//         isLoggedIn.value = false;
//       }
//     };

//     const logout = async () => {
//       try {
//         await postRequest('logout');
//         isLoggedIn.value = false;
//         window.location.reload(); // reload to reset state
//       } catch (err) {
//         alert('Logout failed');
//       }
//     };

//     const orderCount = computed(() => {
//       const order = orderStore.getOrder();
//       return order?.products?.length || 0;
//     });

//     onMounted(() => {
//       checkAuth();
//     });

//     return {
//       toggleSidebar,
//       orderCount,
//       isLoggedIn,
//       logout,
//     };
//   },
// };
</script>

<style scoped>
.coffee_link {
  background: transparent;
  border: none;
  box-shadow: none;
  padding: 0;
  position: relative;
  display: flex;
  align-items: center;
}

.coffee_icon {
  color: #fff;
  transition: color 0.3s ease;
}

.coffee_link:hover .coffee_icon {
  color: #b49383;
}

.coffee_count_badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: red;
  color: white;
  border-radius: 50%;
  font-size: 12px;
  padding: 2px 6px;
  line-height: 1;
}
.logout-btn {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  padding: 8px 12px;
  font-family: inherit;
}

.logout-btn:hover {
  color: #b49383;
}
</style>
