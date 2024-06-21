import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '@/views/HomeView.vue'
import { useAuthStore } from '@/stores/auth'
import { Enums } from '@/utils'

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
      component: () => import('@/views/AboutView.vue')
    },
    // will match /profile and /profile/42
    {
      path: '/profile/:id(\\d+)?',
      name: 'profile',
      component: () => import('@/views/ProfileView.vue')
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('@/views/LoginView.vue')
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('@/views/RegistrationView.vue')
    },
    {
      path: '/registration-success',
      name: 'registrationSuccess',
      component: () => import('@/views/RegistrationSuccessView.vue')
    },
    {
      path: '/feedback/:id',
      name: 'feedbackThread',
      component: () => import('@/views/FeedbackThreadView.vue')
    },
    {
      path: '/feedback-form',
      name: 'createFeedbackForm',
      component: () => import('@/views/FeedbackFormView.vue')
    },
    {
      path: '/feedback-form/:id',
      name: 'editFeedbackForm',
      component: () => import('@/views/FeedbackFormView.vue')
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
    },
    {
      path: '/forgot-password',
      name: 'forgotPassword',
      component: () => import('@/views/ForgotPasswordView.vue')
    },
    {
      path: '/reset-password',
      name: 'resetPassword',
      component: () => import('@/views/ResetPasswordView.vue')
    }
  ]
})

export default router

router.beforeEach((to, from, next) => {
  const isAuth = localStorage.getItem(Enums.ACCESS_TOKEN)

  if (!authStore) {
    authStore = useAuthStore()
  }

  if (to.name === 'login' && isAuth) next({ name: 'home' })
  else if (to.name === 'forgotPassword' && isAuth) next({ name: 'home' })
  else if (to.name === 'profile' && !isAuth) next({ name: 'login' })
  else if (to.name === 'createFeedbackForm' && !isAuth) next({ name: 'login' })
  else if (to.name === 'editFeedbackForm' && !isAuth) next({ name: 'login' })
  else next()
})
