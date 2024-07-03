import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useThemeStore = defineStore('theme', () => {
  const isDarkMode = ref(false)

  const Enums = Object.freeze({
    THEME: 'THEME',
    LIGHT: 'LIGHT',
    DARK: 'DARK'
  })

  const init = () => {
    const savedTheme = localStorage.getItem(Enums.THEME)
    if (savedTheme) {
      isDarkMode.value = savedTheme === Enums.DARK
    } else {
      isDarkMode.value =
        window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
    }

    setTheme()
  }
  const toggle = () => {
    isDarkMode.value = !isDarkMode.value
    localStorage.setItem(Enums.THEME, isDarkMode.value ? Enums.DARK : Enums.LIGHT)

    setTheme()
  }
  const setTheme = () => {
    const app = document.querySelector('#app')
    if (isDarkMode.value) {
      document.body.style['color-scheme'] = 'dark'
      // document.body.classList.add('dark-theme')
      // document.body.classList.remove('light-theme')

      app.classList.add('dark-theme')
      app.classList.remove('light-theme')
    } else {
      document.body.style['color-scheme'] = 'light'
      // document.body.classList.add('light-theme')
      // document.body.classList.remove('dark-theme')

      app.classList.add('light-theme')
      app.classList.remove('dark-theme')
    }
  }

  return { isDarkMode, init, toggle }
})
