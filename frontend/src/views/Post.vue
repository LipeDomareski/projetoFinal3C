<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api, { storageUrl } from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'
import { usePostInteractions } from '../composables/usePostInteractions'

const route = useRoute()
const router = useRouter()

const post = ref(null)
const loading = ref(true)
const error = ref('')
const deleting = ref(false)
const commentInput = ref(null)

const {
    liked,
    likesCount,
    commentsCount,
    loadingLike,
    comments,
    loadingComments,
    commentsError,
    newComment,
    sendingComment,
    toggleLike,
    loadComments,
    addComment,
    syncPostState,
} = usePostInteractions(() => post.value?.id)

const getPost = async () => {
    try {
        const response = await api.get(`/posts/${route.params.id}`)
        post.value = response.data.post
        syncPostState(post.value)
    } catch (err) {
        if (err.response?.status === 404) {
            error.value = 'Publicação não encontrada.'
        } else {
            error.value = 'Não foi possível carregar a publicação.'
        }
    } finally {
        loading.value = false
    }
}

const focusComment = () => {
    commentInput.value?.focus()
}

const deletePost = async () => {
    if (!post.value?.is_owner || deleting.value) return

    const confirmed = window.confirm('Deseja realmente excluir esta publicação?')

    if (!confirmed) return

    deleting.value = true

    try {
        await api.delete(`/posts/${post.value.id}`)
        router.push('/profile')
    } catch {
        error.value = 'Não foi possível excluir a publicação.'
    } finally {
        deleting.value = false
    }
}

onMounted(async () => {
    await getPost()

    if (post.value) {
        await loadComments()
    }
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNavbar />

        <div
            v-if="loading"
            class="flex min-h-screen items-center justify-center pt-16 text-gray-500"
        >
            Carregando publicação...
        </div>

        <div
            v-else-if="error && !post"
            class="flex min-h-screen flex-col items-center justify-center gap-4 pt-16 text-center"
        >
            <p class="text-red-500">
                {{ error }}
            </p>
            <router-link
                to="/"
                class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white"
            >
                Voltar para o início
            </router-link>
        </div>

        <main
            v-else-if="post"
            class="mx-auto max-w-4xl px-4 pb-10 pt-24"
        >
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm md:flex">
                <div class="md:w-1/2">
                    <img
                        :src="storageUrl(post.image)"
                        :alt="post.caption || 'Post do CarHub'"
                        class="aspect-square h-full w-full object-cover"
                    />
                </div>

                <div class="flex min-h-[500px] flex-col md:w-1/2">
                    <div class="flex items-center justify-between border-b border-gray-200 p-4">
                        <router-link
                            :to="`/profile/${post.user?.id}`"
                            class="flex items-center gap-3"
                        >
                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold"
                            >
                                <img
                                    v-if="post.user?.profile_photo"
                                    :src="storageUrl(post.user.profile_photo)"
                                    alt="Foto de perfil"
                                    class="h-full w-full object-cover"
                                />
                                <span v-else>
                                    {{ post.user?.username?.charAt(0).toUpperCase() }}
                                </span>
                            </div>

                            <div>
                                <p class="font-semibold">
                                    {{ post.user?.username }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ post.user?.name }}
                                </p>
                            </div>
                        </router-link>

                        <button
                            v-if="post.is_owner"
                            type="button"
                            :disabled="deleting"
                            class="rounded-lg px-3 py-2 text-sm font-semibold text-red-600 hover:bg-red-50 disabled:opacity-50"
                            @click="deletePost"
                        >
                            {{ deleting ? 'Excluindo...' : 'Excluir' }}
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto">
                        <div
                            v-if="post.caption"
                            class="border-b border-gray-100 p-4 text-sm"
                        >
                            <span class="font-semibold">
                                {{ post.user?.username }}
                            </span>
                            {{ post.caption }}
                        </div>

                        <div
                            v-if="loadingComments"
                            class="p-4 text-sm text-gray-500"
                        >
                            Carregando comentários...
                        </div>

                        <div
                            v-else-if="commentsError"
                            class="p-4 text-sm text-red-500"
                        >
                            {{ commentsError }}
                        </div>

                        <div
                            v-else
                            class="space-y-4 p-4"
                        >
                            <p
                                v-if="comments.length === 0"
                                class="text-sm text-gray-500"
                            >
                                Nenhum comentário ainda.
                            </p>

                            <div
                                v-for="comment in comments"
                                :key="comment.id"
                                class="text-sm"
                            >
                                <router-link
                                    :to="`/profile/${comment.user?.id}`"
                                    class="font-semibold"
                                >
                                    {{ comment.user?.username }}
                                </router-link>
                                {{ comment.content }}
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-200">
                        <div class="flex items-center gap-4 px-4 pt-3 text-2xl">
                            <button
                                type="button"
                                :disabled="loadingLike"
                                class="transition hover:scale-110 disabled:opacity-50"
                                :aria-label="liked ? 'Remover curtida' : 'Curtir'"
                                @click="toggleLike"
                            >
                                {{ liked ? '❤️' : '♡' }}
                            </button>

                            <button
                                type="button"
                                class="transition hover:scale-110"
                                aria-label="Comentar"
                                @click="focusComment"
                            >
                                💬
                            </button>
                        </div>

                        <p class="px-4 pt-2 text-sm font-semibold">
                            {{ likesCount }}
                            {{ likesCount === 1 ? 'curtida' : 'curtidas' }}
                        </p>

                        <p class="px-4 pt-1 text-xs text-gray-500">
                            {{ commentsCount }}
                            {{ commentsCount === 1 ? 'comentário' : 'comentários' }}
                        </p>

                        <form
                            class="flex gap-2 p-4"
                            @submit.prevent="addComment"
                        >
                            <input
                                ref="commentInput"
                                v-model="newComment"
                                type="text"
                                maxlength="1000"
                                placeholder="Adicione um comentário..."
                                class="min-w-0 flex-1 rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500"
                            />

                            <button
                                type="submit"
                                :disabled="sendingComment || !newComment.trim()"
                                class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
                            >
                                {{ sendingComment ? '...' : 'Enviar' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <p
                v-if="error"
                class="mt-4 text-center text-sm text-red-500"
            >
                {{ error }}
            </p>
        </main>
    </div>
</template>
