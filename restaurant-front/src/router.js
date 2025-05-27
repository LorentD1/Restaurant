import { createRouter, createWebHistory } from 'vue-router'
import Login from './views/Login.vue'
import Register from './views/Register.vue'
import CreateReservation from './views/CreateReservation.vue'
import ReservationList from './views/ReservationList.vue'  // ✅ importo ReservationList.vue nga views
import AdminDashboard from './views/AdminDashboard.vue'  
import MyRequests from './views/MyRequests.vue'
import AdminStatistics from './views/AdminStatistics.vue' // ✅ import statistikat

const routes = [
  { path: '/', redirect: '/create' },
  { path: '/login', component: Login },
  { path: '/register', component: Register },
  { path: '/create', component: CreateReservation },
  { path: '/list', component: ReservationList },  // ✅ Shto rrugën për `ReservationList.vue`
  { path: '/admin', component: AdminDashboard },
  { path: '/my-requests', component: MyRequests },
  { path: '/admin/statistics', component: AdminStatistics } // ✅ Shto rrugën për AdminStatistics
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// mbrojtje e rrugëve
router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/register']
  const authRequired = !publicPages.includes(to.path)
  const token = localStorage.getItem('token')

  if (authRequired && !token) {
    return next('/login')
  }

  if (to.path.startsWith('/admin')) {
    const user = JSON.parse(localStorage.getItem('user'))
    if (!user || user.role !== 'admin') {
      return next('/create')
    }
  }

  next()
})

export default router
