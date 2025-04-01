<template>
  <div>
      <Header />
      <div>
        <div>
          <div>
            <Body />
          </div>
        </div>
      </div>
      <Footer />


  </div>
</template>

<script>
import Footer from './components/Footer.vue';
import Header from './components/Header.vue';
import Body from './components/Body.vue'

export default {
  name: 'Wrapper',
  components: {
    Header,
    Body,
    Footer
  }
};
</script>

<style>

</style>
