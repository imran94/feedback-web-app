<script setup>
import utils from '@/utils';
import { ref, onMounted, computed } from 'vue'
import Swal from 'sweetalert2'
import router from '@/router';
import { marked } from 'marked'


import { useAuthStore } from '@/stores/auth';
const auth = useAuthStore()
const post = ref({
    id: -1,
    title: "",
    description: "",
    category: "",
    vote_count: 0,
    user_id: -1,
    created_at: "",
    updated_at: "",
    user: { name: "" },
    comments: []
})
const userVote = ref({ isUpvote: null })
const isLoading = ref(true)
const ownComment = ref('')

async function fetchPostDetails() {
    isLoading.value = true
    const res = await utils.networkRequest(location.pathname)
    if (res.status === 200) {
        post.value = await res.json()
        post.value.comments = post.value.comments.map(comment => {
            comment.content = marked.parse(comment.content)
            comment.isEditing = false
            comment.editedComment = ''
            return comment
        })
        post.value.description = marked.parse(post.value.description)
        try {
            const voteRes = await utils.networkRequest(`/feedback/${post.value.id}/vote`)
            const voteData = await voteRes.json()
            userVote.value = { isUpvote: voteData.is_upvote }
        } catch (err) { }
    }


    isLoading.value = false
}

async function vote(isUpvote) {
    if (!auth.isAuth) {
        router.push({ name: 'login' })
        return
    }

    userVote.value.isUpvote = isUpvote
    const res = await utils.networkRequest(`/feedback/${post.value.id}/vote`, 'PUT', { isUpvote: isUpvote })

    const mUserVote = await res.json()
    userVote.value = { isUpvote: mUserVote.is_upvote }
    post.value.vote_count = mUserVote.vote_count
}

async function cancelVote() {
    userVote.value = {}

    const res = await utils.networkRequest(`/feedback/${post.value.id}/vote`, "DELETE")

    const mUserVote = await res.json()
    post.value.vote_count = mUserVote.vote_count
}

async function deletePost() {
    const res = await Swal.fire({
        title: "Delete Feedback Post",
        text: "Are you sure you want to delete this post?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
    })
    if (!res.isConfirmed) {
        return
    }

    await utils.networkRequest(`/feedback/${post.value.id}`, 'DELETE')

    Swal.fire({
        title: "Deleted!",
        text: "Post has been deleted.",
        icon: "success"
    });
    router.push({ name: 'home' })

}

async function submitNewComment() {
    const res = await utils.networkRequest(
        `/feedback/${post.value.id}/comments`,
        'POST',
        { content: ownComment.value }
    )

    ownComment.value = ''
    const isSuccess = res.status === 200
    if (isSuccess) {
        const newComment = await res.json()
        newComment.content = marked.parse(newComment.content)
        post.value.comments = { ...post.value.comments, newComment }
    }
    Swal.fire({
        title: isSuccess ? 'Comment successfully created' : 'Failed to create comment',
        toast: true,
        timer: 2000,
        position: 'top-end',
        showConfirmButton: false,
        icon: isSuccess ? 'success' : 'error',
    });
}

async function updateComment(comment) {

    let updatedComment = { ...comment }
    updatedComment.content = comment.editedComment

    // comment.content = comment.editedComment

    const res = await utils.networkRequest(`/comments/${comment.id}`, 'PUT', updatedComment)
    const isSuccess = res.status === 200
    if (isSuccess) {
        updatedComment = await res.json()
        comment.content = updatedComment.content
        comment.isEditing = false
        comment.editedComment = ''

        post.value.comments[post.value.comments.findIndex(c => c.id === updatedComment.id)] = comment
        post.value.comments = { ...post.value.comments }
    }
    Swal.fire({
        title: isSuccess ? 'Comment successfully edited' : 'Failed to edit comment',
        toast: true,
        timer: 2000,
        position: 'top-end',
        showConfirmButton: false,
        icon: isSuccess ? 'success' : 'error',
    });
}

async function deleteComment(comment) {
    const res = await Swal.fire({
        title: "Delete Comment",
        text: "Are you sure you want to delete this comment?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes"
    })
    if (!res.isConfirmed) {
        return
    }

    await utils.networkRequest(`/comments/${comment.id}`, 'DELETE')

    Swal.fire({
        title: "Deleted!",
        text: "Comment has been deleted.",
        icon: "success"
    });
    fetchPostDetails()
}

function startEditingComment(comment, isEditing) {
    comment.editedComment = ''
    comment.isEditing = isEditing
}

onMounted(() => {
    fetchPostDetails()
});

const convertedDescription = computed(() => {
    return marked.parse(post.value.description)
})

const convertedComment = computed(() => {
    return marked.parse(ownComment.value)
})
</script>

<template>
    <div class="section">
        <div v-show="isLoading" class="spinner-border center"></div>

        <div v-show="post.id !== -1" class="m-card">
            <div class="m-card-header">
                <h4 class="m-card-title">{{ post.title }}</h4>

                <div v-if="auth.isAdmin || post.user.id === auth.userId" class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                    </button>
                    <ul class="dropdown-menu">
                        <li>
                            <router-link :to="{ name: 'editFeedbackForm', params: { id: post.id } }" class="dropdown-item">
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

            <div class="m-card-main" v-html="convertedDescription">
            </div>

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
                <textarea v-model="ownComment" class="form-control" id="description" rows="8" required :disabled="isLoading"
                    placeholder="Write a comment"></textarea>
            </div>
            <div class="m-form-group">
                Preview:
                <div v-html="convertedComment"></div>

            </div>
            <button type="submit" class="btn btn-secondary">Submit</button>
        </form>

        <div class="heading">
            <h3>Comments</h3>
        </div>

        <template v-for="comment in post.comments" :key="comment.id">
            <div class="m-card">
                <div class="m-card-header">

                    <div class="m-card-title">
                        <i class="bi bi-person-fill"></i>
                        <h5>{{ comment.user.name }}</h5>
                    </div>

                    <div v-if="auth.isAdmin || comment.user.id === auth.userId" class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                        </button>
                        <ul class="dropdown-menu">
                            <li v-show="!comment.isEditing">
                                <a class="dropdown-item" href="javascript:void(0)"
                                    @click="startEditingComment(comment, true)">
                                    Edit
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    @click="deleteComment(comment)">Delete</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="m-card-main" v-html="comment.content">
                </div>
            </div>

            <template v-if="comment.isEditing">
                <div class="m-form-group">
                    <textarea v-model="comment.editedComment" class="form-control" id="description" rows="8" required
                        :disabled="isLoading" placeholder="Write a comment"></textarea>
                </div>
                <div>
                    <button class="btn btn-warning" @click="startEditingComment(comment, false)">Cancel</button>
                    <button class="btn btn-secondary" style="margin-left: 1em" @click="updateComment(comment)">Edit</button>
                </div>

                <div class="m-form-group">
                    Preview:
                    <div v-html="marked.parse(comment.editedComment)"></div>

                </div>
            </template>
        </template>

        <div v-if="post?.comments.length === 0 && !isLoading">No comments added yet.</div>
    </div>
</template>

<style scoped>
a {
    text-decoration: none;
    color: black
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
    box-shadow: 2px 2px 5px 2px rgba(0, 0, 0, .4);
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
    justify-content: space-evenly;
    align-items: space-evenly;
}

.m-card-main {
    padding-top: 1em;
    padding-bottom: 1em;
}

.m-card-subtitle {
    padding-top: 0.5em;
}

.m-card-subtitle * {
    padding-right: 5px;
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