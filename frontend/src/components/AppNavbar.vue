<script setup>
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const isActive = (...names) => names.includes(route.name)

const logout = async () => {
    try {
        await api.post('/logout')
    } catch {
        // Mesmo que a API esteja indisponível, encerramos a sessão local.
    } finally {
        localStorage.removeItem('token')
        router.replace({ name: 'login' })
    }
}
</script>

<template>
    <nav class="fixed inset-x-0 top-0 z-50 border-b border-gray-200 bg-white">
        <div class="mx-auto flex h-16 max-w-5xl items-center justify-between px-4">
            <router-link
                to="/"
                class="text-2xl font-bold tracking-tight"
            >
                CarHub
            </router-link>

            <div class="flex items-center gap-5">
                <router-link
                    to="/"
                    class="text-xl transition hover:scale-110 hover:text-black"
                    :class="isActive('home') ? 'scale-110 font-bold text-black' : 'text-gray-700'"
                    title="Início"
                >
                    🏠
                </router-link>

                <router-link
                    to="/search"
                    class="text-xl transition hover:scale-110 hover:text-black"
                    :class="isActive('search') ? 'scale-110 font-bold text-black' : 'text-gray-700'"
                    title="Pesquisar"
                >
                    🔎
                </router-link>

                <router-link
                    to="/create"
                    class="text-2xl transition hover:scale-110 hover:text-black"
                    :class="isActive('create') ? 'scale-110 font-bold text-black' : 'text-gray-700'"
                    title="Criar publicação"
                >
                    ＋
                </router-link>

                <router-link
                    to="/profile"
                    class="text-xl transition hover:scale-110 hover:text-black"
                    :class="isActive('profile', 'edit-profile', 'user-profile') ? 'scale-110 font-bold text-black' : 'text-gray-700'"
                    title="Perfil"
                >
                    👤
                </router-link>

                <button
                    type="button"
                    class="text-xl text-gray-700 transition hover:scale-110 hover:text-black"
                    title="Sair"
                    aria-label="Sair da conta"
                    @click="logout"
                >
                    🚪
                </button>
            </div>
        </div>
    </nav>
</template>
