import { createRouter, createWebHistory } from 'vue-router';
import AuthView from '../views/Authview.vue';
import ProfileView from '../views/Profileview.vue';
import ContentView from '../views/Contentview.vue';
//import UsuariosView from '../views/UsuariosView.vue';
import api from '../axios';

const routes = [
  { path: '/', name: 'auth', component: AuthView },
  { path: '/perfil', name: 'perfil', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/contenido/:tipo', name: 'contenido', component: ContentView },
  //{ path: '/usuarios', name: 'usuarios', component: UsuariosView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to, from, next) => {
  try {
    const res = await api.get('/api/me');
    const isLoggedIn = !!res.data;

    if (to.path === '/' && isLoggedIn) {
      return next('/perfil'); // Redirige si está logueado
    }

    if (to.path === '/perfil' && !isLoggedIn) {
      return next('/'); // Redirige si NO está logueado
    }

    next(); // Permite la navegación
  } catch {
    if (to.path === '/perfil') return next('/');
    next();
  }
});

export default router;
