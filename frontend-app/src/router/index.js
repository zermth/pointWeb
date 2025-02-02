import { createRouter, createWebHistory } from 'vue-router'

import Login from '@/components/Login.vue'
import Home from '@/components/Home.vue'
import ProductDetails from '@/components/ProductDetails.vue'
import Profile from '@/components/Profile.vue'
import ProductList from '@/components/ProductList.vue'

const routes = [
  { path: '/', component: Login },
  { path: '/home', component: Home },
  { path: '/profile', name: 'Profile', component: Profile },
  {
    path: '/productlist',
    name: 'ProductList',
    component: ProductList,
  },
  {
    path: '/product/:id',
    name: 'ProductDetail',
    component: ProductDetails,
    props: true,  // Allow passing the route params as props
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.VUE_APP_BASE_URL),
  routes,
})


// Navigation guard to check if the user is authorized
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  
  if (to.path !== '/' && (!token)) {
    // If the token is missing or expired, redirect to login
    next('/')
  } else {
    // Otherwise, allow the navigation to proceed
    next()
  }
})

export default router
