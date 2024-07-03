<script setup>
defineProps(['feedbackData'])
defineEmits(['page-no-clicked'])

function formattedDate(date) {
  return new Date(date).toLocaleString()
}
</script>

<template>
  <div class="section">
    <!-- <ul class="pagination">
    <template v-for="link in feedbackData.links" :key="link.label">
      <li class="page-item" :class="{ disabled: !link.url }">
        <a class="page-link" :class="{ active: link.active }" href="javascript:void(0)"
          @click="$emit('page-no-clicked', link)" v-html="link.label" />
      </li>
    </template>
  </ul> -->

    <router-link
      v-for="post in feedbackData.data"
      :to="{ name: 'feedbackThread', params: { id: post.id } }"
      :key="post.id"
      class="m-card"
    >
      <h4 class="m-card-title">{{ post.title }}</h4>

      <div class="m-card-subtitle">
        <i class="bi bi-tag-fill"></i>
        <span>{{ post.category }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-person-fill"></i>
        <span>{{ post.user.name }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-hand-thumbs-up-fill"></i>
        <span>{{ post['vote_count'] }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-chat-left-fill"></i>
        <span>{{ post['comments_count'] }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-calendar"></i>
        <span>{{ formattedDate(post['created_at']) }}</span>
      </div>
    </router-link>

    <ul class="pagination">
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
a {
  text-decoration: none;
}

.section {
  width: 100%;
  /* height: 100%; */
  /* background: light-dark(var(--light-bg), var(--dark-bg)); */

  margin-top: 1em;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;
}

.m-card {
  width: 100%;
  margin-bottom: 1em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px light-dark(var(--light-box-shadow), var(--dark-box-shadow));
  border-radius: 0.5em;

  /* color: black; */
  color: light-dark(var(--light-text), var(--dark-text));
  background: light-dark(var(--light-card-bg), var(--dark-card-bg));
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

a.m-card:hover {
  background-color: light-dark(var(--light-card-bg-hover), var(--dark-card-bg-hover));
  box-shadow: 5px 5px 10px 5px
    light-dark(var(--light-box-shadow-hover), var(--dark-box-shadow-hover));
}

.m-card-subtitle {
  display: inline-flex;
  justify-content: space-between;
}

.m-card-subtitle * {
  padding-right: 5px;
}

.page-link {
  background: light-dark(var(--light-bg), var(--dark-bg));
  color: light-dark(var(--light-link-text), var(--dark-link-text));
}
</style>
