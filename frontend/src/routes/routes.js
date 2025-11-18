// frontend/src/routes/index.js (ou routes.js)
import DashboardLayout from '../layout/DashboardLayout.vue'
// GeneralViews
import NotFound from '../pages/NotFoundPage.vue'

// Admin pages
import Overview from '@/pages/Overview.vue' // <-- Use o alias '@'
import UserProfile from '@/pages/UserProfile.vue' // <-- Use o alias '@'

// NOVOS IMPORTS para Login/Register
import Login from '@/views/Login.vue' // Ajuste o caminho conforme onde você criou Login.vue
import Index from '@/views/Index.vue' // Opcional, se você criar um Index.vue
import Register from '@/views/Register.vue' // Opcional, se você criar um Register.vue
import { name } from 'v-tooltip'

// 

const routes = [
  {
    path: '/',
    name: 'Index',
    component: Index,
  },
  {
    path: '/admin', // Rotas protegidas pelo login
    component: DashboardLayout,
    redirect: '/admin/overview',
    children: [
      {
        path: 'overview',
        name: 'Overview',
        component: Overview,
        meta: { requiresAuth: true } // Marque as rotas que precisam de autenticação
      },
      {
        path: 'user',
        name: 'User',
        component: UserProfile,
        meta: { requiresAuth: true }
      },
      // ... outras rotas do dashboard com meta: { requiresAuth: true }
    ]
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { guest: true } // Permite acesso a quem NÃO está logado
  },
  { path: '*', component: NotFound }
]

export default routes