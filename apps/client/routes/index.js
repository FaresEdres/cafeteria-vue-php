import { createRouter, createWebHistory } from 'vue-router';
import Body from '../src/components/Body.vue';
import Header from '../src/components/Header.vue';
import Menu from '../src/components/Menu.vue';
import Orders from '../src/components/Orders.vue';
import OrderForm from '../src/components/OrderForm.vue';
import About from '../src/components/About.vue';
import Contact from '../src/components/Contact.vue';
import Footer from '../src/components/Footer.vue';
import NotFound from '../src/components/NotFound.vue';
import ChecksPage from '../src/components/ChecksPage.vue';

const routes=[
    {path:'/',component:Body},
    {path:'/menu',component:Menu},
    { path: '/orders', component: Orders },
    { path: '/orderform', component: OrderForm },
    { path: '/about', component: About },
    { path: '/contact', component: Contact },
    {path:'/header',component:Header},
    {path:'/footer',component:Footer},
    {path:'/cheks',component:ChecksPage},
    {path:'/:catchAll(.*)',component:NotFound}

];

const router=createRouter({
    routes,
    history:createWebHistory()
})
export default router;


