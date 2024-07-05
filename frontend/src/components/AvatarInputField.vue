<script setup>
import { computed, ref } from 'vue'

const baseUrl = import.meta.env.VITE_BACKEND_BASE_URL
const avatarFileInput = ref(null)
const props = defineProps(['newAvatarUrl', 'currentAvatarUrl'])
const emit = defineEmits(['on-avatar-selected', 'on-avatar-removed'])

function removeSelectedAvatar() {
  avatarFileInput.value = null
  emit('on-avatar-removed')
}
</script>

<template>
  <div class="input-avi-container">
    <div class="input-avi input-avi-image">
      <img
        v-if="newAvatarUrl || currentAvatarUrl"
        class="input-avi"
        :src="newAvatarUrl ?? `${baseUrl}/${currentAvatarUrl}`"
        alt="Avatar"
      />
      <i v-else class="bi bi-camera-fill"></i>
    </div>

    <input
      class="input-avi input-avi-selector"
      type="file"
      accept="image/*"
      name="avatar"
      id="avatar-select"
      ref="avatarFileInput"
      @change="$emit('on-avatar-selected', $event)"
    />

    <label
      v-if="newAvatarUrl || currentAvatarUrl"
      class="input-avi input-avi-selector-label"
      for="avatar-select"
    >
      <i class="bi bi-pencil"></i>
    </label>
    <i v-if="newAvatarUrl" class="bi bi-x btn-input-avi-remove" @click="removeSelectedAvatar"></i>
  </div>
  <div class="avatar-select-tip">
    <div>Max avatar size: 1 MB</div>
    <div>Max dimensions: 1000x1000</div>
  </div>
</template>

<style scoped>
.input-avi-container,
.avatar-select-tip {
  align-self: center;
}

.input-avi-container {
  width: 100px;
  height: 100px;
  border-radius: 50%;
}

.input-avi-container {
  position: relative;
}
.input-avi {
  width: 100%;
  height: 100%;
  border-radius: 50%;
  position: absolute;
  top: 0;
  left: 0;
}
.input-avi-image {
  background: light-dark(var(--blank-avi-bg-light), var(--blank-avi-bg-dark));
  /* background-color: #b8b8b8; */
  /* color: white; */
  display: flex;
  align-items: center;
  justify-content: center;
}

.input-avi-selector {
  z-index: 2;
  opacity: 0;
  cursor: pointer;
}
.input-avi-selector-label,
.btn-input-avi-remove {
  cursor: pointer;
}
.input-avi-selector-label {
  z-index: 3;
  display: flex;
  align-items: center;
  justify-content: center;
  background: var(--input-avi-bg);
  color: white;
  opacity: 0;
  font-size: 1.5em;
}
.input-avi-selector-label:hover {
  opacity: 75%;
}

.btn-input-avi-remove {
  z-index: 4;
  right: 0;
  border-radius: 50%;
  position: absolute;
  bottom: 0;
  background: light-dark(var(--blank-avi-bg-dark), var(--light-bg));
  color: light-dark(var(--dark-text), var(--light-text));
  padding: 0.3em 0.5em;
}
.bt-input-avi-remove:hover {
  background-color: var(--blank-avi-background-color);
}

.avatar-select-tip {
  text-align: center;
  font-size: 0.7em;
  width: 200px;
  padding: 0.5em;

  border: 1px solid silver;
  border-radius: 3%;
}
</style>
