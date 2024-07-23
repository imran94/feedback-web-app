<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted, computed } from 'vue'
import Swal from 'sweetalert2'
import router from '@/router'
import { marked } from 'marked'
import Editor from 'primevue/editor'
import { useAuthStore } from '@/stores/auth'
import FeedbackCard from '@/components/FeedbackCard.vue'

const baseUrl = import.meta.env.VITE_BACKEND_BASE_URL
const defaultAvatarUrl = `${import.meta.env.VITE_BACKEND_BASE_URL}/storage/blank-avatar.jpg`
const auth = useAuthStore()
const post = ref({
  id: -1,
  title: '',
  description: '',
  category: '',
  vote_count: 0,
  user_id: -1,
  created_at: '',
  updated_at: '',
  user: { name: '' },
  comments: []
})
const userVoted = ref(false)
const isVoting = ref(false)
const isLoading = ref(true)
const isSubmittingComment = ref(false)
const ownComment = ref('')

const isOwnPost = computed(() => post.value?.user.id === auth.userId)

async function fetchPostDetails() {
  isLoading.value = true
  const { response, data } = await customFetch(location.pathname)
  if (response.ok) {
    post.value = data
    post.value.comments = post.value.comments.map((comment) => {
      comment.content = marked.parse(comment.content)
      comment.isEditing = false
      comment.editedComment = comment.content
      return comment
    })
    post.value.description = marked.parse(post.value.description)
    try {
      const voteRes = await customFetch(`/feedback/${post.value.id}/vote`)
      userVoted.value = voteRes.data.is_upvote
    } catch (err) {
      const mute = err
    }
  }

  isLoading.value = false
}

async function vote() {
  if (isVoting.value) {
    return
  }

  if (!auth.isAuth) {
    router.push({ name: 'login' })
    return
  }

  isVoting.value = true
  if (userVoted.value) {
    await cancelVote()
    isVoting.value = false
    return
  }

  userVoted.value = true
  try {
    const { response, data } = await customFetch(`/feedback/${post.value.id}/vote`, 'PUT', {
      isUpvote: true
    })
    const mUserVote = data
    userVoted.value = mUserVote.is_upvote
    post.value.vote_count = mUserVote.vote_count
  } catch (err) {
    console.error(err)
  }
  isVoting.value = false
}

async function cancelVote() {
  userVoted.value = false

  const { response, data } = await customFetch(`/feedback/${post.value.id}/vote`, 'DELETE')

  post.value.vote_count = data.vote_count
}

async function deletePost() {
  const res = await Swal.fire({
    title: 'Delete Feedback Post',
    text: 'Are you sure you want to delete this post?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  })
  if (!res.isConfirmed) {
    return
  }

  await customFetch(`/feedback/${post.value.id}`, 'DELETE')

  Swal.fire({
    title: 'Deleted!',
    text: 'Post has been deleted.',
    icon: 'success'
  })
  router.push({ name: 'home' })
}

async function submitNewComment() {
  if (isEmptyHtml(ownComment.value)) return

  isSubmittingComment.value = true
  const { response, data } = await customFetch(`/feedback/${post.value.id}/comments`, 'POST', {
    content: ownComment.value
  })

  ownComment.value = ''
  isSubmittingComment.value = false

  if (response.ok) {
    data.editedComment = data.content
    const comments = post.value.comments
    comments.push(data)
    post.value.comments = comments
  }
  Swal.fire({
    title: response.ok ? 'Comment successfully created' : 'Failed to create comment',
    toast: true,
    timer: 2000,
    position: 'top-end',
    showConfirmButton: false,
    icon: response.ok ? 'success' : 'error'
  })
}

async function updateComment(comment) {
  if (isEmptyHtml(comment.editedComment)) return

  let updatedComment = { ...comment }
  updatedComment.content = comment.editedComment

  // comment.content = comment.editedComment

  const { response, data } = await customFetch(`/comments/${comment.id}`, 'PUT', updatedComment)

  if (response.ok) {
    comment.content = data.content
    comment.isEditing = false
    comment.editedComment = comment.content

    const comments = post.value.comments
    post.value.comments[comments.findIndex((c) => c.id === updatedComment.id)] = comment
    post.value.comments = { ...post.value.comments }
  }
  Swal.fire({
    title: response.ok ? 'Comment successfully edited' : 'Failed to edit comment',
    toast: true,
    timer: 2000,
    position: 'top-end',
    showConfirmButton: false,
    icon: response.ok ? 'success' : 'error'
  })
}

async function deleteComment(comment) {
  const res = await Swal.fire({
    title: 'Delete Comment',
    text: 'Are you sure you want to delete this comment?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes'
  })
  if (!res.isConfirmed) {
    return
  }

  await customFetch(`/comments/${comment.id}`, 'DELETE')

  Swal.fire({
    title: 'Deleted!',
    text: 'Comment has been deleted.',
    icon: 'success'
  })
  fetchPostDetails()
}

function startEditingComment(comment, isEditing) {
  comment.editedComment = comment.content
  comment.isEditing = isEditing
}

function formattedDate(date) {
  return new Date(date).toLocaleString()
}

onMounted(() => {
  fetchPostDetails()
})

const convertedDescription = computed(() => {
  return marked.parse(post.value.description)
})

const domParser = new DOMParser()
function isEmptyHtml(str) {
  let parsedString = domParser.parseFromString(str, 'text/html').body.textContent || ''
  return parsedString.trim() === ''
}
</script>

<template>
  <div class="section">
    <div v-show="isLoading" class="spinner-border center"></div>

    <div class="control-buttons" v-if="auth.isAdmin || isOwnPost">
      <router-link
        :to="{ name: 'editFeedbackForm', params: { id: post.id } }"
        class="btn btn-secondary"
      >
        Edit Feedback
      </router-link>

      <div class="btn btn-danger clickable" @click="deletePost">Delete</div>
    </div>

    <feedback-card
      :feedback="post"
      :showFullDescription="true"
      :userVoted="userVoted"
      @on-vote-clicked="vote"
    />

    <div class="m-card">
      <div class="heading">
        <span>{{ post.comments.length }} Comments</span>
      </div>

      <template v-for="comment in post.comments" :key="comment.id">
        <div class="comment" v-show="!comment.isEditing">
          <img
            class="avatar"
            :src="comment.user.avatar_url ? `${baseUrl}/${user.avatar_url}` : defaultAvatarUrl"
            alt="Avatar"
          />
          <div class="comment-content">
            <div class="comment-user bold">
              <div class="comment-username">{{ comment.user.name }}</div>
              <!-- <div class="reply-button">Reply</div> -->
            </div>

            <div class="comment-text" v-html="comment.content"></div>
          </div>
        </div>

        <template v-if="comment.isEditing">
          <div class="m-form-group">
            <Editor v-model="comment.editedComment" editorStyle="height: 250px" />
          </div>
          <div>
            <button class="btn btn-warning" @click="startEditingComment(comment, false)">
              Cancel
            </button>
            <button
              class="btn btn-secondary"
              style="margin-left: 1em"
              @click="updateComment(comment)"
            >
              Edit
            </button>
          </div>
        </template>
      </template>
    </div>

    <form @submit.prevent="submitNewComment" v-if="auth.isAuth" class="comment-form m-card">
      <div class="m-form-group">
        <div class="comment-form-label bold">Write a comment</div>
        <Editor v-model="ownComment" editorStyle="height: 200px;" />
      </div>
      <button type="submit" class="btn btn-secondary" :disabled="isSubmittingComment">
        Submit
      </button>
    </form>
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
  color: black;
}

.section {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
  align-items: center;

  padding-left: 1.5em;
  padding-right: 1.5em;
}

.control-buttons {
  width: 100%;
  margin-bottom: 1em;
  display: flex;
  flex-flow: row wrap;
  justify-content: flex-end;
  align-items: center;
}

.control-buttons > * {
  margin-left: 1em;
}

.m-card {
  margin: 1em 0.5em;
  padding: 2em;
  box-shadow: 2px 2px 5px 2px #00000066;
  border-radius: 0.5em;

  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  row-gap: 1em;
}

.center {
  margin: 10em;
}

.heading {
  font-size: 2em;
  font-weight: bolder;
  align-self: flex-start;
}

.m-form-group {
  width: 100%;
  margin-bottom: 1em;
}
.dropdown {
  padding-bottom: 0.5em;
}

.comment {
  width: 100%;
  padding: 3em 0em;
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: flex-start;
}

.comment + .comment {
  border-top: 0.1px solid light-dark(#d3d3d3, #3c4144);
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
}

.comment-content {
  width: 100%;
  margin-left: 2em;

  display: flex;
  flex-direction: column;
  align-items: flex-start;
  row-gap: 1em;
}

.comment-user {
  width: 100%;

  display: flex;
  flex-direction: row;
  justify-content: space-between;
}

.bold {
  font-weight: bold;
}

.comment-form-label {
  font-size: 2em;
  margin-bottom: 1em;
}

.comment-text {
  color: light-dark(#637196, #999183);
}
</style>
