<script setup>
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import api, { storageUrl } from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'

const route = useRoute()

const profile = ref(null)
const loading = ref(true)
const error = ref('')
const loadingFollow = ref(false)

const getProfile = async () => {
    loading.value = true
    error.value = ''

    try {
        const response = route.params.id
            ? await api.get(`/users/${route.params.id}`)
            : await api.get('/profile')

        profile.value = response.data.user
    } catch {
        profile.value = null
        error.value = 'Não foi possível carregar o perfil.'
    } finally {
        loading.value = false
    }
}

const toggleFollow = async () => {
    if (
        !profile.value ||
        profile.value.is_me ||
        loadingFollow.value
    ) {
        return
    }

    loadingFollow.value = true

    try {
        if (profile.value.is_following) {
            await api.delete(`/users/${profile.value.id}/follow`)
            profile.value.is_following = false
            profile.value.followers_count = Math.max(
                0,
                (profile.value.followers_count ?? 0) - 1
            )
        } else {
            await api.post(`/users/${profile.value.id}/follow`)
            profile.value.is_following = true
            profile.value.followers_count =
                (profile.value.followers_count ?? 0) + 1
        }
    } catch {
        error.value = 'Não foi possível atualizar o follow.'
    } finally {
        loadingFollow.value = false
    }
}

watch(
    () => route.params.id,
    () => getProfile(),
    { immediate: true }
)
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNavbar />

        <main class="mx-auto max-w-4xl px-4 pb-10 pt-24">
            <div
                v-if="loading"
                class="py-10 text-center text-gray-500"
            >
                Carregando perfil...
            </div>

            <div
                v-else-if="error && !profile"
                class="py-10 text-center text-red-500"
            >
                {{ error }}
            </div>

            <div v-else-if="profile">
                <section
                    class="flex flex-col gap-6 border-b border-gray-200 pb-8 sm:flex-row sm:items-center"
                >
                    <div
                        class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-3xl font-bold"
                    >
                        <img
                            v-if="profile.profile_photo"
                            :src="storageUrl(profile.profile_photo)"
                            alt="Foto de perfil"
                            class="h-full w-full object-cover"
                        />
                        <span v-else>
                            {{ profile.username?.charAt(0).toUpperCase() }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <h1 class="text-2xl font-semibold">
                                {{ profile.username }}
                            </h1>

                            <router-link
                                v-if="profile.is_me"
                                to="/profile/edit"
                                class="rounded-lg border border-gray-300 px-4 py-2 text-center text-sm font-semibold transition hover:bg-gray-100"
                            >
                                Editar perfil
                            </router-link>

                            <button
                                v-else
                                type="button"
                                :disabled="loadingFollow"
                                class="rounded-lg bg-black px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-800 disabled:opacity-50"
                                @click="toggleFollow"
                            >
                                {{
                                    loadingFollow
                                        ? 'Carregando...'
                                        : profile.is_following
                                            ? 'Deixar de seguir'
                                            : 'Seguir'
                                }}
                            </button>
                        </div>

                        <p class="mt-2 text-gray-600">
                            {{ profile.name }}
                        </p>

                        <p
                            v-if="profile.bio"
                            class="mt-2 whitespace-pre-line text-sm text-gray-600"
                        >
                            {{ profile.bio }}
                        </p>

                        <div class="mt-4 flex gap-6 text-sm">
                            <div>
                                <span class="font-semibold">
                                    {{ profile.posts_count ?? 0 }}
                                </span>
                                posts
                            </div>

                            <div>
                                <span class="font-semibold">
                                    {{ profile.followers_count ?? 0 }}
                                </span>
                                seguidores
                            </div>

                            <div>
                                <span class="font-semibold">
                                    {{ profile.following_count ?? 0 }}
                                </span>
                                seguindo
                            </div>
                        </div>

                        <p
                            v-if="error"
                            class="mt-3 text-sm text-red-500"
                        >
                            {{ error }}
                        </p>
                    </div>
                </section>

                <section class="pt-6">
                    <h2 class="mb-4 text-lg font-semibold">
                        Publicações
                    </h2>

                    <div
                        v-if="!profile.posts || profile.posts.length === 0"
                        class="py-10 text-center text-gray-500"
                    >
                        Nenhuma publicação ainda.
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-2 gap-1 sm:grid-cols-3"
                    >
                        <router-link
                            v-for="post in profile.posts"
                            :key="post.id"
                            :to="`/posts/${post.id}`"
                            class="group relative overflow-hidden bg-gray-200"
                        >
                            <img
                                :src="storageUrl(post.image)"
                                :alt="post.caption || 'Publicação'"
                                class="aspect-square w-full object-cover transition duration-200 group-hover:scale-105"
                            />
                        </router-link>
                    </div>
                </section>
            </div>
        </main>
    </div>
</template>
