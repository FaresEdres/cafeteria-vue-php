<template>
  <div class="admin-layout">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><router-link to="/admin/users">Manage Users</router-link></li>
        <li><router-link to="/admin/adduser">Add User</router-link></li>
        <li><router-link to="/admin/products">Manage Products</router-link></li>
        <li><router-link to="/admin/addproduct">Add Product</router-link></li>
        <li><router-link to="/admin/checks">Checks</router-link></li>
        <li><router-link to="/admin/orderdash">Order Dashboard</router-link></li>
        <li>
          <button @click="logout" class="logout-btn">Logout</button>
        </li>

      </ul>
    </aside>

    <main class="main-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<script>
import { postRequest } from '../services/httpClient';
import { useAuthStore } from '../stores/auth';

export default {
  name: 'AdminLayout',
  async beforeMount() {
    const user = (await postRequest('authenticated'));
    if (!user || user.role !== 'admin') {
      alert('You are not authorized to access this page');
      window.location.href = '/';
    }
  },
  methods: {
    async logout() {
      const authStore = useAuthStore();
      await authStore.logout();
      window.location.href = '/';
    }
  }
};

</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #fdf9f6;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.sidebar {
  width: 220px;
  background-color: #fff7f2;
  padding: 1.5rem;
  border-right: 1px solid #e0c9b7;
  box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
}

.sidebar h2 {
  font-size: 1.4rem;
  margin-bottom: 1rem;
  color: #c5907c;
  font-family: 'Georgia', serif;
  text-align: center;
}

.sidebar ul {
  list-style: none;
  padding: 0;
}

.sidebar li {
  margin-bottom: 0.8rem;
}

.sidebar a {
  text-decoration: none;
  color: #6f4e37;
  font-weight: 500;
  transition: color 0.3s;
}

.sidebar a:hover,
.sidebar a.router-link-exact-active {
  color: #a35b3c;
}

.main-content {
  flex-grow: 1;
  padding: 2rem;
  background-color: #fdf9f6;
}

.logout-btn {
  margin-top: 2rem;
}

.logout-btn form {
  display: flex;
  justify-content: center;
}

.logout-btn input[type="submit"] {
  background-color: #d9534f;
  color: white;
  border: none;
  padding: 0.6rem 1.2rem;
  font-size: 0.95rem;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  font-weight: bold;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.logout-btn input[type="submit"]:hover {
  background-color: #c9302c;
  transform: translateY(-1px);
}

.logout-btn {
  background-color: #d9534f;
  border: none;
  padding: 0.8rem 1.5rem;
  color: #ffffff;
  font-size: 1rem;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  margin-top: 2rem;
  display: inline-block;
}

.logout-btn:hover {
  background-color: #c9302c;
  transform: translateY(-2px);
}

.logout-btn:active {
  transform: translateY(0);
}
</style>
