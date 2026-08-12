<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref('')
const loading = ref(false)

const login = async () => {
    error.value = ''
    loading.value = true

    try {
        const response = await api.post('/login', {
            email: email.value,
            password: password.value,
        })

        const token = response.data.token

        localStorage.setItem('token', token)

        router.push({ name: 'home' })
    } catch (err) {
        if (err.response?.status === 422 || err.response?.status === 401) {
            error.value =
                err.response.data.message || 'E-mail ou senha inválidos.'
        } else {
            error.value = 'Não foi possível conectar com a API.'
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="login-page">
        <div class="login-card">
            <h1 class="text-5xl font-bold text-blue-600">
               CarHub
            </h1>

            <p>Entre na sua conta</p>

            <form @submit.prevent="login">
                <input
                    v-model="email"
                    type="email"
                    placeholder="E-mail"
                    required
                />

                <input
                    v-model="password"
                    type="password"
                    placeholder="Senha"
                    required
                />

                <p v-if="error" class="error">
                    {{ error }}
                </p>

                <button type="submit" :disabled="loading">
                    {{ loading ? 'Entrando...' : 'Entrar' }}
                </button>
            </form>

            <p>
                Não tem uma conta?
                <router-link to="/register">
                    Criar conta
                </router-link>
            </p>
        </div>
    </div>
</template>
