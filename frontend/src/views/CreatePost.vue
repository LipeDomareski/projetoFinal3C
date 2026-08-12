<script setup>
import { onBeforeUnmount, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import AppNavbar from '../components/AppNavbar.vue'

const router = useRouter()

const caption = ref('')
const image = ref(null)
const preview = ref('')

const loading = ref(false)
const error = ref('')

const selectImage = (event) => {
    const file = event.target.files?.[0]

    if (!file) return

    if (file.size > 5 * 1024 * 1024) {
        if (preview.value) URL.revokeObjectURL(preview.value)
        image.value = null
        preview.value = ''
        error.value = 'A imagem deve ter no máximo 5 MB.'
        event.target.value = ''
        return
    }

    if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
        if (preview.value) URL.revokeObjectURL(preview.value)
        image.value = null
        preview.value = ''
        error.value = 'Use uma imagem JPG, PNG ou WEBP.'
        event.target.value = ''
        return
    }

    if (preview.value) {
        URL.revokeObjectURL(preview.value)
    }

    error.value = ''
    image.value = file
    preview.value = URL.createObjectURL(file)
}

const createPost = async () => {
    if (!image.value) {
        error.value = 'Selecione uma imagem.'
        return
    }

    const formData = new FormData()

    formData.append('image', image.value)

    if (caption.value.trim()) {
        formData.append('caption', caption.value.trim())
    }

    loading.value = true
    error.value = ''

    try {
        await api.post('/posts', formData)
        router.push('/')
    } catch (err) {
        if (err.response?.status === 422) {
            const errors = err.response.data.errors ?? {}
            error.value =
                errors.image?.[0] ||
                errors.caption?.[0] ||
                'Verifique os dados da publicação.'
        } else {
            error.value =
                err.response?.data?.message ||
                'Não foi possível criar a publicação.'
        }
    } finally {
        loading.value = false
    }
}

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
                    Criar publicação
                </h1>

                <form
                    class="space-y-5"
                    @submit.prevent="createPost"
                >
                    <div>
                        <label
                            for="image"
                            class="mb-2 block text-sm font-semibold"
                        >
                            Imagem
                        </label>

                        <input
                            id="image"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full text-sm"
                            @change="selectImage"
                        />
                    </div>

                    <div v-if="preview">
                        <img
                            :src="preview"
                            alt="Preview da publicação"
                            class="aspect-square w-full rounded-lg object-cover"
                        />
                    </div>

                    <div>
                        <label
                            for="caption"
                            class="mb-2 block text-sm font-semibold"
                        >
                            Legenda
                        </label>

                        <textarea
                            id="caption"
                            v-model="caption"
                            rows="3"
                            maxlength="2200"
                            placeholder="Escreva uma legenda..."
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 outline-none focus:border-gray-500"
                        ></textarea>
                    </div>

                    <p
                        v-if="error"
                        class="text-sm text-red-500"
                    >
                        {{ error }}
                    </p>

                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full rounded-lg bg-black px-4 py-3 font-semibold text-white transition hover:bg-gray-800 disabled:opacity-50"
                    >
                        {{ loading ? 'Publicando...' : 'Publicar' }}
                    </button>
                </form>
            </div>
        </main>
    </div>
</template>
