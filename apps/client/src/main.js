import 'bootstrap/dist/css/bootstrap.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp } from "vue";
import { createPinia } from 'pinia';
import '../public/style.css';
import Wrapper from "./Wrapper.vue";
import router from "../routes/index.js";


const app = createApp(Wrapper);
const pinia = createPinia();

app.use(pinia);
app.use(router);
app.mount("#app");

import 'bootstrap/dist/js/bootstrap.bundle.js';
