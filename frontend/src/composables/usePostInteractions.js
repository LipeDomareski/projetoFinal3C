import { ref } from 'vue'
import api from '../services/api'

export const usePostInteractions = (postId) => {
    const liked = ref(false)
    const likesCount = ref(0)
    const commentsCount = ref(0)
    const loadingLike = ref(false)

    const comments = ref([])
    const loadingComments = ref(false)
    const commentsError = ref('')

    const newComment = ref('')
    const sendingComment = ref(false)

    const resolvePostId = () => (
        typeof postId === 'function' ? postId() : postId
    )

    const toggleLike = async () => {
        const id = resolvePostId()

        if (!id || loadingLike.value) return

        loadingLike.value = true

        try {
            if (liked.value) {
                await api.delete(`/posts/${id}/like`)
                liked.value = false
                likesCount.value = Math.max(0, likesCount.value - 1)
            } else {
                await api.post(`/posts/${id}/like`)
                liked.value = true
                likesCount.value++
            }
        } finally {
            loadingLike.value = false
        }
    }

    const loadComments = async () => {
        const id = resolvePostId()

        if (!id) return

        loadingComments.value = true
        commentsError.value = ''

        try {
            const response = await api.get(`/posts/${id}/comments`)
            comments.value = response.data.comments ?? []
            commentsCount.value = comments.value.length
        } catch {
            commentsError.value = 'Não foi possível carregar os comentários.'
        } finally {
            loadingComments.value = false
        }
    }

    const addComment = async () => {
        const id = resolvePostId()
        const content = newComment.value.trim()

        if (!id || !content || sendingComment.value) return

        sendingComment.value = true
        commentsError.value = ''

        try {
            const response = await api.post(
                `/posts/${id}/comments`,
                { content }
            )

            comments.value.unshift(response.data.comment)
            commentsCount.value++
            newComment.value = ''
        } catch {
            commentsError.value = 'Não foi possível adicionar o comentário.'
        } finally {
            sendingComment.value = false
        }
    }

    const syncPostState = (post) => {
        liked.value = !!post?.liked_by_me
        likesCount.value = post?.liked_by_count ?? 0
        commentsCount.value = post?.comments_count ?? 0
    }

    return {
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
    }
}
