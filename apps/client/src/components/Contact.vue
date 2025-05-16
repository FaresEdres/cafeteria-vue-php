<template>
  <div class="super_container">
    <div class="contact">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <div class="contact_title">Contact info</div>
            <div class="contact_text">
              <p>Welcome to Armané Café — your cozy corner for handcrafted coffee, artisan pastries, and warm
                conversations. Whether you're here for a quick espresso or to unwind with friends, our doors are always
                open. Stop by and taste the difference passion makes.</p>
            </div>
            <div class="contact_logo_container">
              <div class="contact_logo_title">The Armané</div>
              <div class="contact_logo_subtitle">Café</div>
            </div>
          </div>
          <div class="col-xl-5 col-lg-6">
            <div class="contact_list_container d-flex flex-column align-items-lg-center justify-content-start">
              <div class="contact_list_content">
                <ul class="contact_list">
                  <li class="d-flex flex-row align-items-start justify-content-start">
                    <div>
                      <div>Address</div>
                    </div>
                    <div>481 Main Street CA 93424</div>
                  </li>
                  <li class="d-flex flex-row align-items-start justify-content-start">
                    <div>
                      <div>Phone</div>
                    </div>
                    <div>+53 345 7953 324</div>
                  </li>
                  <li class="d-flex flex-row align-items-start justify-content-start">
                    <div>
                      <div>E-mail</div>
                    </div>
                    <div>ArmanéCafé@gmail</div>
                  </li>
                </ul>
                <div class="contact_social">
                  <ul class="d-flex flex-row align-items-center justify-content-start">
                    <li><a href="https://www.pinterest.com/"><i class="fab fa-pinterest" aria-hidden="true"></i></a>
                    </li>
                    <li><a href="https://www.facebook.com/"><i class="fab fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="https://www.twitter.com/"><i class="fab fa-twitter" aria-hidden="true"></i></a></li>
                    <li><a href="https://www.dribbble.com/"><i class="fab fa-dribbble" aria-hidden="true"></i></a></li>
                    <li><a href="https://www.behance.net/"><i class="fab fa-behance" aria-hidden="true"></i></a></li>
                    <li><a href="https://www.linkedin.com/"><i class="fab fa-linkedin" aria-hidden="true"></i></a></li>

                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row google_map_row">
          <div class="col">

            <!-- Contact Map -->

            <div class="contact_map">

              <!-- Google Map -->

              <div class="map">
                <div id="google_map" class="google_map">
                  <div class="map_container">
                    <div id="map"></div>
                  </div>
                </div>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    // Attach initMap to the window object so it's globally accessible
    window.initMap = this.initMap;

    // Dynamically load Google Maps API with async and defer
    const script = document.createElement("script");
    script.src = `https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyCIwF204lFZg1y4kPSIhKaHEXMLYxxuMhA&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  },
  methods: {
    initMap() {
      const map = new google.maps.Map(document.getElementById("google_map"), {
        center: { lat: 36.7783, lng: -119.4179 }, // Use your actual coordinates
        zoom: 10,
      });

      new google.maps.Marker({
        position: { lat: 36.7783, lng: -119.4179 }, // Use your actual coordinates
        map,
        title: "Armané Café",
      });
    },
  },
};


</script>


<style scoped></style>
