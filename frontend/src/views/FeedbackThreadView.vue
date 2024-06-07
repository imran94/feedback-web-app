<script setup>
import { customFetch } from '@/utils'
import { ref, onMounted, computed } from 'vue'
import Swal from 'sweetalert2'
import router from '@/router'
import { marked } from 'marked'
import Editor from 'primevue/editor'
import { useAuthStore } from '@/stores/auth'

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
const userVote = ref({ isUpvote: null })
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
      userVote.value = { isUpvote: voteRes.data.is_upvote }
    } catch (err) {
      const mute = err
    }

    console.log('post: ', post.value.user.id)
    console.log('userId: ', auth.userId)
  }

  isLoading.value = false
}

async function vote(isUpvote) {
  if (isVoting.value) {
    return
  }

  if (!auth.isAuth) {
    router.push({ name: 'login' })
    return
  }

  isVoting.value = true
  userVote.value.isUpvote = isUpvote
  try {
    const { response, data } = await customFetch(`/feedback/${post.value.id}/vote`, 'PUT', {
      isUpvote: isUpvote
    })
    const mUserVote = data
    userVote.value = { isUpvote: mUserVote.is_upvote }
    post.value.vote_count = mUserVote.vote_count
  } catch (err) {
    console.error(err)
  }
  isVoting.value = false
}

async function cancelVote() {
  userVote.value = {}

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
  const isSuccess = response.status === 200
  if (isSuccess) {
    comment.content = data.content
    comment.isEditing = false
    comment.editedComment = comment.content

    const comments = post.value.comments
    post.value.comments[comments.findIndex((c) => c.id === updatedComment.id)] = comment
    post.value.comments = { ...post.value.comments }
  }
  Swal.fire({
    title: isSuccess ? 'Comment successfully edited' : 'Failed to edit comment',
    toast: true,
    timer: 2000,
    position: 'top-end',
    showConfirmButton: false,
    icon: isSuccess ? 'success' : 'error'
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

    <div v-show="post.id !== -1" class="m-card">
      <div class="m-card-header">
        <h4 class="m-card-title">{{ post.title }}</h4>

        <div v-if="auth.isAdmin || isOwnPost" class="dropdown">
          <button
            class="btn btn-secondary dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          ></button>
          <ul class="dropdown-menu">
            <li>
              <router-link
                :to="{ name: 'editFeedbackForm', params: { id: post.id } }"
                class="dropdown-item"
              >
                Edit
              </router-link>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0)" @click="deletePost">Delete</a>
            </li>
          </ul>
        </div>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-tag-fill"></i>
        <span>{{ post.category }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-person-fill"></i>
        <span>{{ post.user.name }}</span>
      </div>

      <div class="m-card-subtitle">
        <i class="bi bi-calendar"></i>
        <span>{{ formattedDate(post['created_at']) }}</span>
      </div>

      <div class="m-card-main" v-html="convertedDescription"></div>

      <div class="m-card-subtitle">
        <a href="javascript:void(0)" v-if="userVote.isUpvote === true" @click="cancelVote">
          <i class="bi bi-hand-thumbs-up-fill"></i>
        </a>
        <a href="javascript:void(0)" v-else @click="vote(true)">
          <i class="bi bi-hand-thumbs-up"></i>
        </a>
        <span>{{ post.vote_count }}</span>
        <a href="javascript:void(0)" v-if="userVote.isUpvote === false" @click="cancelVote">
          <i class="bi bi-hand-thumbs-down-fill"></i>
        </a>
        <a href="javascript:void(0)" v-else @click="vote(false)">
          <i class="bi bi-hand-thumbs-down"></i>
        </a>
      </div>
    </div>

    <form @submit.prevent="submitNewComment" v-if="auth.isAuth" class="comment-form">
      <div class="m-form-group">
        <label>Write a comment</label>
        <Editor v-model="ownComment" editorStyle="height: 320px" />
      </div>
      <button type="submit" class="btn btn-secondary" :disabled="isSubmittingComment">
        Submit
      </button>
    </form>

    <div class="heading">
      <h3>Comments</h3>
    </div>

    <template v-for="comment in post.comments" :key="comment.id">
      <div class="m-card" v-show="!comment.isEditing">
        <div class="m-card-header">
          <div class="m-card-title">
            <i class="bi bi-person-fill"></i>
            <h5>{{ comment.user.name }}</h5>
          </div>

          <div v-if="auth.isAdmin || comment.user.id === auth.userId" class="dropdown">
            <button
              class="btn btn-secondary dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            ></button>
            <ul class="dropdown-menu">
              <li v-show="!comment.isEditing">
                <a
                  class="dropdown-item"
                  href="javascript:void(0)"
                  @click="startEditingComment(comment, true)"
                >
                  Edit
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="javascript:void(0)" @click="deleteComment(comment)"
                  >Delete</a
                >
              </li>
            </ul>
          </div>
        </div>
        <div class="m-card-main" v-html="comment.content" />
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

    <div v-if="post?.comments.length === 0 && !isLoading">No comments added yet.</div>
  </div>
</template>

<style scoped>
a {
  text-decoration: none;
  color: black;
}

.section {
  width: 100%;
  height: 100%;

  display: flex;
  flex-flow: column wrap;
  justify-content: space-around;
  align-items: center;

  padding-left: 1.5em;
  padding-right: 1.5em;
}

.m-card {
  margin: 1em 0.5em;
  padding: 1em;
  box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, 0.4);
  border-radius: 0.5em;

  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
}

.m-card-header {
  width: 100%;
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  justify-content: space-between;
}

.m-card-title,
.m-card-subtitle {
  display: inline-flex;
  width: 100%;
  justify-content: flex-start;
  align-items: baseline;
  column-gap: 0.75em;
}

.m-card-subtitle {
  padding-top: 0.5em;
}

.m-card-title,
.m-card-subtitle * {
  /* padding-right: 0.5em; */
}

.m-card-main {
  padding: 1em 0em 0.5em 0em;
  padding-bottom: 0.5em;
  width: 100%;

  border-top: 1px solid black;
}

.center {
  margin: 10em;
}

.heading {
  align-self: flex-start;
}

.comment-form {
  width: 100%;
  padding-top: 1em;
  padding-bottom: 1em;

  display: flex;
  flex-direction: column;
}

.m-form-group {
  width: 100%;
  margin-bottom: 1em;
}
</style>
