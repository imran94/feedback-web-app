<script setup>

defineProps(['feedbackData'])
</script>

<template>
  <ul class="pagination">
    <template v-for="link in feedbackData.links" :key="link.label">
      <li class="page-item" :class="{ disabled: !link.url }">
        <a class="page-link" :class="{ active: link.active }" href="javascript:void(0)"
          @click="$emit('page-no-clicked', link)" v-html="link.label" />
      </li>
    </template>
  </ul>

  <router-link v-for="post in feedbackData.data" :to="{ name: 'feedbackThread', params: { id: post.id } }" :key="post.id"
    class="m-card">
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
  </router-link>

  <ul class="pagination">
    <template v-for="link in feedbackData.links" :key="link.label">
      <li class="page-item" :class="{ disabled: !link.url }">
        <a class="page-link" :class="{ active: link.active }" href="javascript:void(0)"
          @click="$emit('page-no-clicked', link)" v-html="link.label" />
      </li>
    </template>
  </ul>
</template>

<style scoped>
a {
  text-decoration: none;
}

.section {
  width: 100%;
  height: 100%;

  margin-top: 1em;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;
}

.m-card {
  width: 95%;
  margin-bottom: 1em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, .4);
  border-radius: 0.5em;

  color: black;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

a.m-card:hover {
  background-color: #ebebeb;
  box-shadow: 5px 5px 10px 5px rgba(0, 0, 0, .8);
}

.m-card-subtitle {
  display: inline-flex;
  justify-content: space-between;
}

.m-card-subtitle * {
  padding-right: 5px;
}
</style>
