import { createRouter, createWebHistory } from 'vue-router';
import AuthView from '../views/AuthView.vue';
import ProfileView from '../views/ProfileView.vue';
import ContentView from '../views/ContentView.vue';
//import UsuariosView from '../views/UsuariosView.vue';
import { userStore } from '../stores/authStore';

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
    const isLoggedIn = userStore.value != null;
    console.log("el pepe")
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
