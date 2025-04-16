import { createRouter, createWebHistory } from 'vue-router';
import Body from '../src/components/Body.vue';
import Header from '../src/components/Header.vue';
import Menu from '../src/components/Menu.vue';
import Orders from '../src/components/Orders.vue';
import OrderForm from '../src/components/OrderForm.vue';
import About from '../src/components/About.vue';
import Contact from '../src/components/Contact.vue';
import Login from '../src/components/Login.vue';
import Footer from '../src/components/Footer.vue';
import NotFound from '../src/components/NotFound.vue';
import AddProduct from '../src/components/AddProduct.vue';
import AddCategory from '../src/components/AddCategory.vue';
import ChecksPage from '../src/components/ChecksPage.vue';
import AllProducts from '../src/components/AllProducts.vue';
import AddUser from '../src/components/AddUser.vue';
import Users from '../src/components/Users.vue';
import AdminLayout from '../src/components/AdminLayout.vue';
import OrdersDashboard from '../src/components/OrdersDashboard.vue'

const routes = [
    { path: '/', component: Body },
    { path: '/menu', component: Menu },
    { path: '/orders', component: Orders },
    { path: '/orderform', component: OrderForm },
    { path: '/about', component: About },
    { path: '/contact', component: Contact },
    { path: '/header', component: Header },
    { path: '/footer', component: Footer },
    { path: '/add-product', component: AddProduct },
    { path: '/add-category', component: AddCategory },


      {
        path: '/admin',
        component: AdminLayout,
        children: [
          { path: 'adduser', component: () => AddUser },
          { path: 'users', component: () => Users },
          { path: 'products', component: () => AllProducts },
          { path: 'addproduct', component: () => AddProduct},
          { path: 'checks', component: () => ChecksPage },
          { path: 'orderdash', component: () => OrdersDashboard },
        ],
      },

    { path: '/checks', component: ChecksPage },
    { path: '/all-products', component: AllProducts },
    { path: '/users', component: Users },
    { path: '/adduser', component: AddUser },
    { path: '/login', component: Login },
    { path: '/orderdashboard', component: OrdersDashboard },
    { path: '/:catchAll(.*)', component: NotFound }

];

const router = createRouter({
    routes,
    history: createWebHistory()
})
export default router;


