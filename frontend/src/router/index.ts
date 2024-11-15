import { createRouter, createWebHistory } from 'vue-router';
import Home from '../views/Home.vue';
import AnnouncePage from '../views/AnnouncePage.vue';
import RentPage from '../views/RentPage.vue';
import ReturnPage from '@/views/ReturnPage.vue';
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/announce',
      name: 'announce',
      component: AnnouncePage,
    },
    {
      path: '/rent',
      name: 'rent',
      component: RentPage,
    },
    {
      path: '/return',
      name: 'return',
      component: ReturnPage,
    },
  ],
});

export default router;
