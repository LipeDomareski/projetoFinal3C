<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { storageUrl } from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'

const router = useRouter()

const name = ref('')
const username = ref('')
const bio = ref('')
const profilePhoto = ref(null)
const currentPhoto = ref('')
const preview = ref('')

const loading = ref(true)
const saving = ref(false)
const error = ref('')
const fieldErrors = ref({})

const getProfile = async () => {
    try {
        const response = await api.get('/profile')
        const user = response.data.user

        name.value = user.name ?? ''
        username.value = user.username ?? ''
        bio.value = user.bio ?? ''
        currentPhoto.value = user.profile_photo ?? ''
    } catch {
        error.value = 'Não foi possível carregar o perfil.'
    } finally {
        loading.value = false
    }
}

const selectPhoto = (event) => {
    const file = event.target.files?.[0]

    if (!file) return

    if (file.size > 5 * 1024 * 1024) {
        if (preview.value) URL.revokeObjectURL(preview.value)
        profilePhoto.value = null
        preview.value = ''
        error.value = 'A foto deve ter no máximo 5 MB.'
        event.target.value = ''
        return
    }

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        if (preview.value) URL.revokeObjectURL(preview.value)
        profilePhoto.value = null
        preview.value = ''
        error.value = 'Use uma foto JPG, PNG ou WEBP.'
        event.target.value = ''
        return
    }

    error.value = ''
    profilePhoto.value = file

    if (preview.value) {
        URL.revokeObjectURL(preview.value)
    }

    preview.value = URL.createObjectURL(file)
}

const saveProfile = async () => {
    saving.value = true
    error.value = ''
    fieldErrors.value = {}

    const formData = new FormData()

    formData.append('_method', 'PUT')
    formData.append('name', name.value)
    formData.append('username', username.value)
    formData.append('bio', bio.value)

    if (profilePhoto.value) {
        formData.append('profile_photo', profilePhoto.value)
    }

    try {
        await api.post('/profile', formData)
        router.push('/profile')
    } catch (err) {
        if (err.response?.status === 422) {
            fieldErrors.value = err.response.data.errors ?? {}
            error.value = 'Verifique os campos do formulário.'
        } else {
            error.value = 'Não foi possível salvar o perfil.'
        }
    } finally {
        saving.value = false
    }
}

onMounted(getProfile)

onBeforeUnmount(() => {
    if (preview.value) {
        URL.revokeObjectURL(preview.value)
    }
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <AppNavbar />

        <main class="mx-auto max-w-xl px-4 pb-10 pt-24">
            <div class="rounded-xl border border-gray-200 bg-white p-6">
                <h1 class="mb-6 text-2xl font-bold">
                    Editar perfil
                </h1>

                <p
                    v-if="loading"
                    class="py-8 text-center text-gray-500"
                >
                    Carregando...
                </p>

                <form
                    v-else
                    class="space-y-5"
                    @submit.prevent="saveProfile"
                >
                    <div class="flex items-center gap-4">
                        <div
                            class="flex h-20 w-20 items-center justify-center overflow-hidden rounded-full bg-gray-200 text-2xl font-bold"
                        >
                            <img
                                v-if="preview || currentPhoto"
                                :src="preview || storageUrl(currentPhoto)"
                                alt="Foto de perfil"
                                class="h-full w-full object-cover"
                            />
                            <span v-else>
                                {{ username?.charAt(0).toUpperCase() }}
                            </span>
                        </div>

                        <div>
                            <label
                                for="profile-photo"
                                class="mb-1 block text-sm font-semibold"
                            >
                                Foto de perfil
                            </label>
                            <input
                                id="profile-photo"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                @change="selectPhoto"
                            />
                            <p
                                v-if="fieldErrors.profile_photo"
                                class="mt-1 text-sm text-red-500"
                            >
                                {{ fieldErrors.profile_photo[0] }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <label
                            for="name"
                            class="mb-1 block text-sm font-semibold"
                        >
                            Nome
                        </label>
                        <input
                            id="name"
                            v-model="name"
                            type="text"
                            maxlength="255"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-gray-500"
                        />
                        <p
                            v-if="fieldErrors.name"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ fieldErrors.name[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="username"
                            class="mb-1 block text-sm font-semibold"
                        >
                            Usuário
                        </label>
                        <input
                            id="username"
                            v-model="username"
                            type="text"
                            maxlength="50"
                            required
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-gray-500"
                        />
                        <p
                            v-if="fieldErrors.username"
                            class="mt-1 text-sm text-red-500"
                        >
                            {{ fieldErrors.username[0] }}
                        </p>
                    </div>

                    <div>
                        <label
                            for="bio"
                            class="mb-1 block text-sm font-semibold"
                        >
                            Bio
                        </label>
                        <textarea
                            id="bio"
                            v-model="bio"
                            rows="4"
                            maxlength="500"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-gray-500"
                        ></textarea>
                    </div>

                    <p
                        v-if="error"
                        class="text-sm text-red-500"
                    >
                        {{ error }}
                    </p>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            class="flex-1 rounded-lg border border-gray-300 px-4 py-3 font-semibold"
                            @click="router.back()"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            :disabled="saving"
                            class="flex-1 rounded-lg bg-black px-4 py-3 font-semibold text-white disabled:opacity-50"
                        >
                            {{ saving ? 'Salvando...' : 'Salvar' }}
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</template>
