<script setup>
import { computed, ref } from 'vue'

const showPassword = ref(false)
const password = defineModel()

const passwordToggleClass = computed(() => ({
  'bi-eye': showPassword.value,
  'bi-eye-slash': !showPassword.value
}))

const props = defineProps({
  label: {
    type: [String, null],
    default: 'Password'
  }
})
</script>

<template>
  <label for="passwordInput" class="form-label">{{ label }}</label>
  <div class="input-group">
    <input
      :type="showPassword ? 'text' : 'password'"
      id="passwordInput"
      v-model="password"
      class="form-control"
      required
    />
    <span class="input-group-text password-toggle" @click="showPassword = !showPassword">
      <i class="bi" :class="passwordToggleClass" aria-hidden="true"></i>
    </span>
  </div>
</template>

<style scoped>
.password-toggle {
  cursor: pointer;
}

.password-toggle:hover {
  filter: saturate(0.5);
}

.input-group-text {
  background-color: light-dark(#e9ecef, #232627);
  color: light-dark(var(--light-text), var(--dark-text));
}
</style>
