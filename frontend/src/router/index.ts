import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import ProfileView from '../views/ProfileView.vue';
import ContentView from '../views/ContentView.vue';
//import UsuariosView from '../views/UsuariosView.vue';
import { userStore } from '../stores/authStore';
import RegisterView from '../views/RegisterView.vue';
import UsersView from '../views/UsersView.vue';

const routes = [
  { path: '/', name: 'Login', component: LoginView },
  {path: '/registro', name: 'Register', component: RegisterView},
  { path: '/perfil', name: 'perfil', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/perfil/:id', name: 'perfil-usuario', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/contenido/:tipo', name: 'contenido', component: ContentView },
  { path: '/usuarios', name: 'usuarios', component: UsersView },
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
