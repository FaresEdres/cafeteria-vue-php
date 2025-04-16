<template>
    <div class="layout">
      <!-- Sidebar -->
      <div class="sidebar-container">
        <!-- Sidebar Header -->
        <div class="sidebar-header">
          <h2 class="text-xl font-bold">Admin Panel</h2>
        </div>
  
        <!-- Menu Items -->
        <nav class="sidebar-menu">
          <button
            v-for="item in menuItems"
            :key="item.label"
            @click="selectComponent(item.component)"
            class="menu-item"
          >
            <span class="material-icons">{{ item.icon }}</span>
            <span class="menu-label">{{ item.label }}</span>
          </button>
        </nav>
  
        <!-- Footer -->
        <div class="sidebar-footer">
          <button
            @click="selectComponent('SettingsComponent')"
            class="menu-item"
          >
            <span class="material-icons">settings</span>
            <span class="menu-label">Settings</span>
          </button>
        </div>
      </div>
  
      <!-- Main Content -->
      <div class="main-content">
        <component :is="currentComponent" />
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import About from './About.vue';
  
  // Simulate user role (replace this with actual logic to fetch user role)
  const userRole = ref(localStorage.getItem('userRole') || 'user'); // Example: 'admin' or 'user'
  
  // Menu items for navigation
  const menuItems = [
    { label: 'Home', icon: 'home', component: About },
    { label: 'About', icon: 'description', component: About },
    { label: 'Team', icon: 'group', component: About },
    { label: 'Contact', icon: 'email', component: 'About' },
  ];
  
  // Current component to display in the main content area
  const currentComponent = ref('HomeComponent');
  
  // Function to update the displayed component
  const selectComponent = (componentName) => {
    currentComponent.value = componentName;
  };
  </script>
  
  <style scoped>
  /* Layout */
  .layout {
    display: flex;
    height: 100vh;
  }
  
  /* Sidebar Container */
  .sidebar-container {
    display: flex;
    flex-direction: column;
    width: 300px;
    background-color: #ffffff;
    border-right: 5px solid #b49383;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  /* Sidebar Header */
  .sidebar-header {
    background-color: #b49383;
    color: white;
    padding: 1rem;
    text-align: center;
    font-family: 'Dancing Script', cursive;
    border-bottom: 2px solid #a17d6f;
  }
  
  /* Sidebar Menu */
  .sidebar-menu {
    flex-grow: 1;
    padding: 1rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .menu-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-decoration: none;
    cursor: pointer;
  }
  
  .menu-item:hover {
    background-color: #b49383;
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .menu-label {
    font-size: 1rem;
  }
  
  /* Sidebar Footer */
  .sidebar-footer {
    padding: 1rem;
    border-top: 2px solid #e5e5e5;
  }
  
  /* Main Content */
  .main-content {
    flex-grow: 1;
    padding: 2rem;
    background-color: #fdfcfb;
    overflow-y: auto;
  }
  </style>