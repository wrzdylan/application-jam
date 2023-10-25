import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/views/HomeView.vue'
import Register from '@/views/RegisterView.vue'
import Login from '@/views/LoginView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/register',
    name: 'register',
    component: Register
  },
  {
    path: '/login',
    name: 'login',
    component: Login
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

export default router;