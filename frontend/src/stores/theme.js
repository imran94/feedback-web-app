import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', () => {
  const isDarkMode = ref(
    window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
  )
  const init = () => {}
  const toggle = () => {
    isDarkMode.value = !isDarkMode.value
  }

  return { isDarkMode, init, toggle }
})
