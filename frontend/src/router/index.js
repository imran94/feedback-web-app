import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '../stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue')
    },
    {
      path: '/profile',
      name: 'profile',
      component: () => import('../views/ProfileView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../views/RegistrationView.vue')
    },
    {
      path: '/registration-success',
      name: 'registrationSuccess',
      component: () => import('@/views/RegistrationSuccessView.vue')
    },
    {
      path: '/feedback/:id',
      name: 'feedbackThread',
      component: () => import('../views/FeedbackThreadView.vue')
    },
    {
      path: '/feedback-form',
      name: 'createFeedbackForm',
      component: () => import('../views/FeedbackFormView.vue')
    },
    {
      path: '/feedback-form/:id',
      name: 'editFeedbackForm',
      component: () => import('../views/FeedbackFormView.vue')
    },
    {
      path: '/search',
      name: 'search',
      component: () => import('@/views/SearchView.vue')
    }
  ]
})

export default router

router.beforeEach((to, from, next) => {
  if (to.name === 'login' && useAuthStore().isAuth) next({ name: 'home' })
  else if (to.name === 'profile' && !useAuthStore().isAuth) next({ name: 'login' })
  else if (to.name === 'createFeedbackForm' && !useAuthStore().isAuth) next({ name: 'login' })
  else if (to.name === 'editFeedbackForm' && !useAuthStore().isAuth) next({ name: 'login' })
  else next()
})
