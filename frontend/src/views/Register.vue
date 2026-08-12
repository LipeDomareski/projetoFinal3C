<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const name = ref('')
const username = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')

const error = ref('')
const fieldErrors = ref({})
const loading = ref(false)

const register = async () => {
    error.value = ''
    fieldErrors.value = {}
    loading.value = true

    try {
        await api.post('/register', {
            name: name.value,
            username: username.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        })

        // registro não retorna token, então mandamos o usuário logar em seguida
        router.push({ name: 'login' })
    } catch (err) {
        if (err.response?.status === 422) {
            fieldErrors.value = err.response.data.errors || {}
            error.value = err.response.data.message || 'Verifique os campos abaixo.'
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

            <p>Crie sua conta</p>

            <form @submit.prevent="register">
                <input
                    v-model="name"
                    type="text"
                    placeholder="Nome completo"
                    required
                />
                <p v-if="fieldErrors.name" class="error">
                    {{ fieldErrors.name[0] }}
                </p>

                <input
                    v-model="username"
                    type="text"
                    placeholder="Nome de usuário"
                    required
                />
                <p v-if="fieldErrors.username" class="error">
                    {{ fieldErrors.username[0] }}
                </p>

                <input
                    v-model="email"
                    type="email"
                    placeholder="E-mail"
                    required
                />
                <p v-if="fieldErrors.email" class="error">
                    {{ fieldErrors.email[0] }}
                </p>

                <input
                    v-model="password"
                    type="password"
                    placeholder="Senha"
                    minlength="8"
                    required
                />
                <p v-if="fieldErrors.password" class="error">
                    {{ fieldErrors.password[0] }}
                </p>

                <input
                    v-model="passwordConfirmation"
                    type="password"
                    placeholder="Confirme a senha"
                    minlength="8"
                    required
                />

                <p v-if="error" class="error">
                    {{ error }}
                </p>

                <button type="submit" :disabled="loading">
                    {{ loading ? 'Criando conta...' : 'Criar conta' }}
                </button>
            </form>

            <p>
                Já tem uma conta?
                <router-link to="/login">
                    Entrar
                </router-link>
            </p>
        </div>
    </div>
</template>
