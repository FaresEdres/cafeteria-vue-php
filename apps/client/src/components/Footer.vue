<template>
  <div class="super_container">
    <footer class="footer">
      <div class="container">
        <div class="row">

          <!-- Footer Logo -->
          <div class="col-lg-3 footer_col">
            <div class="footer_logo">
              <div class="footer_logo_title">The Armané</div>
              <div class="footer_logo_subtitle">Café</div>
            </div>
            <div class="copyright">
              <p style="line-height: 1.2;">Copyright &copy;{{ currentYear }} All rights reserved</p>
            </div>
          </div>

          <!-- Footer About -->
          <div class="col-lg-6 footer_col">
            <div class="footer_about">
              <p>At Armene Café, we pride ourselves on providing an exceptional experience. From the first sip of our handcrafted coffee to the last bite of our artisanal pastries, everything is made with care and passion. Welcome to the heart of flavor and warmth, where every moment is a little bit sweeter.</p>
            </div>
          </div>

          <!-- Footer Contact -->
          <div class="col-lg-3 footer_col">
            <div class="footer_contact">
              <ul>
                <li class="d-flex flex-row align-items-start justify-content-start">
                  <div><div class="footer_contact_title">Address:</div></div>
                  <div class="footer_contact_text">481 Main Street CA 93424</div>
                </li>
                <li class="d-flex flex-row align-items-start justify-content-start">
                  <div><div class="footer_contact_title">Phone:</div></div>
                  <div class="footer_contact_text">+53 345 7953 32453</div>
                </li>
                <li class="d-flex flex-row align-items-start justify-content-start">
                  <div><div class="footer_contact_title">Email:</div></div>
                  <div class="footer_contact_text">ArmanéCafé@gmail</div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>




<script>
export default {
  name: "FooterComponent",
  data() {
    return {
      currentYear: new Date().getFullYear(),
    };
  },
};
</script>


<style scoped>

</style>
