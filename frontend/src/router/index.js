import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { useAuthStore } from '../stores/auth'

let authStore = null

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
    },
    {
      path: '/verify-email-address/:code',
      name: 'verifyEmailAddress',
      component: () => import('@/views/VerifyEmailView.vue')
    }
  ]
})

export default router

router.beforeEach((to, from, next) => {
  if (!authStore) {
    authStore = useAuthStore()
  }

  if (to.name === 'login' && authStore.isAuthenticated) next({ name: 'home' })
  else if (to.name === 'profile' && !authStore.isAuthenticated) next({ name: 'login' })
  else if (to.name === 'createFeedbackForm' && !authStore.isAuthenticated) next({ name: 'login' })
  else if (to.name === 'editFeedbackForm' && !authStore.isAuthenticated) next({ name: 'login' })
  else next()
})
