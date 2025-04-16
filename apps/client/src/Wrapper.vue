<template>
  <div>
    <Header v-if="!isAdminRoute" />

    <div>
      <router-view />
    </div>

    <Footer v-if="!isAdminRoute" />
  </div>
</template>

<script setup>
import Footer from './components/Footer.vue'
import Header from './components/Header.vue'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()

const isAdminRoute = computed(() => route.path.startsWith('/admin'))
</script>

<style>
</style>
