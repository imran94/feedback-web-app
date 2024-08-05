<script setup>
import FeedbackCard from '@/components/FeedbackCard.vue'

defineProps(['feedbackData', 'isLoading', 'emptyMessage'])
defineEmits(['page-no-clicked'])

function formattedDate(date) {
  return new Date(date).toLocaleString()
}
</script>

<template>
  <div class="feedback-list">
    <div class="spinner-border" v-show="isLoading"></div>

    <h3 v-show="!isLoading && feedbackData.data.length === 0" class="empty-message">
      No feedback posts to show.
    </h3>

    <router-link
      v-for="post in feedbackData.data"
      :to="{ name: 'feedbackThread', params: { id: post.id } }"
      :key="post.id"
      class="feedback-card-link"
    >
      <feedback-card :feedback="post" :isLink="true" />
    </router-link>

    <ul class="pagination" v-if="feedbackData.data.length !== 0">
      <template v-for="link in feedbackData.links" :key="link.label">
        <li class="page-item" :class="{ disabled: !link.url }">
          <a
            class="page-link"
            :class="{ active: link.active }"
            href="javascript:void(0)"
            @click="$emit('page-no-clicked', link)"
            v-html="link.label"
          />
        </li>
      </template>
    </ul>
  </div>
</template>

<style scoped>
.feedback-list {
  width: 100%;
  /* max-width: var(--max-screen-width); */

  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  row-gap: 1em;
}

.feedback-card-link {
  width: 100%;
}

.empty-message,
.spinner-border {
  align-self: center;
}

.page-link {
  background: light-dark(var(--light-bg), var(--dark-bg));
  color: light-dark(var(--light-link-text), var(--dark-link-text));
}

a {
  text-decoration: none;
}
</style>
