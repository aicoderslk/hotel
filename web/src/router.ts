import { createRouter, createWebHistory } from 'vue-router';
import Home from './pages/Home.vue';
import Login from './pages/Login.vue';
import Register from './pages/Register.vue';
import Book from './pages/Book.vue';
import Dashboard from './pages/Dashboard.vue';

export const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/', component: Home },
        { path: '/book', component: Book },
        { path: '/login', component: Login },
        { path: '/register', component: Register },
        { path: '/dashboard', component: Dashboard },
        {
            path: '/confirmation/:id',
            name: 'confirmation',
            component: () => import('./pages/ConfirmationPage.vue')
          }
          
    ],
});
