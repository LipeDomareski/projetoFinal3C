<script setup>
import { onMounted, ref } from 'vue'
import api, { storageUrl } from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'
import PostCard from '../components/PostCard.vue'

const posts = ref([])
const suggestions = ref([])
const loading = ref(true)
const loadingMore = ref(false)
const error = ref('')
const currentPage = ref(1)
const lastPage = ref(1)

const getPosts = async (page = 1, append = false) => {
    const response = await api.get('/posts', {
        params: { page },
    })

    const paginator = response.data.posts

    posts.value = append
        ? [...posts.value, ...paginator.data]
        : paginator.data

    currentPage.value = paginator.current_page
    lastPage.value = paginator.last_page
}

const getSuggestions = async () => {
    try {
        const response = await api.get('/users/suggestions', {
            params: { limit: 5 },
        })

        suggestions.value = response.data.users ?? []
    } catch {
        suggestions.value = []
    }
}

const loadMore = async () => {
    if (loadingMore.value || currentPage.value >= lastPage.value) return

    loadingMore.value = true

    try {
        await getPosts(currentPage.value + 1, true)
    } catch {
        error.value = 'Não foi possível carregar mais publicações.'
    } finally {
        loadingMore.value = false
    }
}

onMounted(async () => {
    try {
        await Promise.all([
            getPosts(),
            getSuggestions(),
        ])
    } catch {
        error.value = 'Não foi possível carregar as publicações.'
    } finally {
        loading.value = false
    }
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNavbar />

        <main class="mx-auto max-w-xl px-4 pb-10 pt-24">
            <section
                v-if="suggestions.length"
                class="mb-6 rounded-xl border border-gray-200 bg-white p-4"
            >
                <h2 class="mb-3 text-sm font-semibold text-gray-600">
                    Sugestões para você
                </h2>

                <div class="flex gap-4 overflow-x-auto pb-1">
                    <router-link
                        v-for="user in suggestions"
                        :key="user.id"
                        :to="`/profile/${user.id}`"
                        class="min-w-20 text-center"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold"
                        >
                            <img
                                v-if="user.profile_photo"
                                :src="storageUrl(user.profile_photo)"
                                alt="Foto de perfil"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>
                                {{ user.username?.charAt(0).toUpperCase() }}
                            </span>
                        </div>
                        <p class="mt-1 truncate text-xs font-semibold">
                            {{ user.username }}
                        </p>
                    </router-link>
                </div>
            </section>

            <div
                v-if="loading"
                class="py-10 text-center text-gray-500"
            >
                Carregando publicações...
            </div>

            <div
                v-else-if="error && posts.length === 0"
                class="py-10 text-center text-red-500"
            >
                {{ error }}
            </div>

            <div
                v-else-if="posts.length === 0"
                class="py-10 text-center text-gray-500"
            >
                Nenhuma publicação encontrada.
            </div>

            <div v-else class="space-y-6">
                <PostCard
                    v-for="post in posts"
                    :key="post.id"
                    :post="post"
                />

                <button
                    v-if="currentPage < lastPage"
                    type="button"
                    :disabled="loadingMore"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold disabled:opacity-50"
                    @click="loadMore"
                >
                    {{ loadingMore ? 'Carregando...' : 'Carregar mais' }}
                </button>

                <p
                    v-if="error"
                    class="text-center text-sm text-red-500"
                >
                    {{ error }}
                </p>
            </div>
        </main>
    </div>
</template>
