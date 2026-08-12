<script setup>
import { ref } from 'vue'
import { storageUrl } from '../services/api'
import { usePostInteractions } from '../composables/usePostInteractions'

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
})

const showComments = ref(false)

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
} = usePostInteractions(() => props.post.id)

syncPostState(props.post)

const toggleComments = async () => {
    showComments.value = !showComments.value

    if (showComments.value && comments.value.length === 0) {
        await loadComments()
    }
}
</script>

<template>
    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white">
        <div class="flex items-center gap-3 p-4">
            <router-link
                :to="`/profile/${post.user?.id}`"
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
            </router-link>

            <router-link :to="`/profile/${post.user?.id}`">
                <p class="font-semibold">
                    {{ post.user?.username }}
                </p>
                <p class="text-xs text-gray-500">
                    {{ post.user?.name }}
                </p>
            </router-link>
        </div>

        <router-link :to="`/posts/${post.id}`">
            <img
                :src="storageUrl(post.image)"
                :alt="post.caption || 'Post do CarHub'"
                class="aspect-square w-full object-cover transition hover:opacity-95"
            />
        </router-link>

        <div class="px-4 pt-3">
            <div class="flex items-center gap-4 text-2xl">
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
                    aria-label="Comentários"
                    @click="toggleComments"
                >
                    💬
                </button>
            </div>

            <p class="mt-2 text-sm font-semibold">
                {{ likesCount }}
                {{ likesCount === 1 ? 'curtida' : 'curtidas' }}
            </p>

            <button
                v-if="commentsCount > 0 && !showComments"
                type="button"
                class="mt-1 text-sm text-gray-500"
                @click="toggleComments"
            >
                Ver {{ commentsCount }}
                {{ commentsCount === 1 ? 'comentário' : 'comentários' }}
            </button>
        </div>

        <div class="p-4">
            <p v-if="post.caption" class="text-sm">
                <span class="font-semibold">
                    {{ post.user?.username }}
                </span>
                {{ post.caption }}
            </p>

            <p v-else class="text-sm text-gray-500">
                Nenhuma legenda.
            </p>
        </div>

        <div
            v-if="showComments"
            class="border-t border-gray-200 px-4 py-4"
        >
            <h3 class="mb-4 font-semibold">
                Comentários
            </h3>

            <p
                v-if="loadingComments"
                class="text-sm text-gray-500"
            >
                Carregando comentários...
            </p>

            <p
                v-else-if="commentsError"
                class="text-sm text-red-500"
            >
                {{ commentsError }}
            </p>

            <div v-else class="space-y-3">
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

            <form
                class="mt-4 flex gap-2"
                @submit.prevent="addComment"
            >
                <input
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
                    {{ sendingComment ? 'Enviando...' : 'Enviar' }}
                </button>
            </form>
        </div>
    </article>
</template>
