<script setup>
import { onMounted, ref } from 'vue'
import api, { storageUrl } from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'

const search = ref('')
const users = ref([])
const loading = ref(false)
const error = ref('')

const searchUsers = async () => {
    loading.value = true
    error.value = ''

    try {
        const response = await api.get('/users', {
            params: search.value.trim()
                ? { search: search.value.trim() }
                : {},
        })

        users.value = response.data.users ?? []
    } catch {
        error.value = 'Não foi possível realizar a busca.'
    } finally {
        loading.value = false
    }
}

onMounted(searchUsers)
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNavbar />

        <main class="mx-auto max-w-xl px-4 pb-10 pt-24">
            <h1 class="mb-6 text-2xl font-bold">
                Pesquisar
            </h1>

            <form
                class="mb-6 flex gap-2"
                @submit.prevent="searchUsers"
            >
                <input
                    v-model="search"
                    type="search"
                    placeholder="Pesquisar usuários..."
                    class="min-w-0 flex-1 rounded-lg border border-gray-300 bg-white px-4 py-3 outline-none focus:border-gray-500"
                />

                <button
                    type="submit"
                    class="rounded-lg bg-black px-5 py-3 font-semibold text-white"
                >
                    Buscar
                </button>
            </form>

            <p
                v-if="loading"
                class="py-6 text-center text-gray-500"
            >
                Buscando...
            </p>

            <p
                v-else-if="error"
                class="py-6 text-center text-red-500"
            >
                {{ error }}
            </p>

            <div
                v-else
                class="space-y-2"
            >
                <p
                    v-if="!search.trim()"
                    class="pb-2 text-sm font-semibold text-gray-500"
                >
                    Sugestões de usuários
                </p>

                <router-link
                    v-for="user in users"
                    :key="user.id"
                    :to="`/profile/${user.id}`"
                    class="flex items-center gap-4 rounded-lg bg-white p-4 transition hover:bg-gray-100"
                >
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gray-200 font-bold"
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

                    <div>
                        <p class="font-semibold">
                            {{ user.username }}
                        </p>

                        <p class="text-sm text-gray-500">
                            {{ user.name }}
                        </p>
                    </div>
                </router-link>

                <p
                    v-if="users.length === 0"
                    class="py-6 text-center text-gray-500"
                >
                    Nenhum usuário encontrado.
                </p>
            </div>
        </main>
    </div>
</template>
