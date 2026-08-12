import { createRouter, createWebHistory } from 'vue-router'
import api from '../services/api'

import Login from '../views/Login.vue'
import Register from '../views/Register.vue'
import Home from '../views/Home.vue'
import Profile from '../views/Profile.vue'
import EditProfile from '../views/EditProfile.vue'
import Search from '../views/Search.vue'
import Post from '../views/Post.vue'
import CreatePost from '../views/CreatePost.vue'
import NotFound from '../views/NotFound.vue'

const router = createRouter({
    history: createWebHistory(),

    routes: [
        {
            path: '/',
            name: 'home',
            component: Home,
            meta: { requiresAuth: true },
        },
        {
            path: '/login',
            name: 'login',
            component: Login,
        },
        {
            path: '/register',
            name: 'register',
            component: Register,
        },
        {
            path: '/profile',
            name: 'profile',
            component: Profile,
            meta: { requiresAuth: true },
        },
        {
            path: '/profile/edit',
            name: 'edit-profile',
            component: EditProfile,
            meta: { requiresAuth: true },
        },
        {
            path: '/profile/:id',
            name: 'user-profile',
            component: Profile,
            meta: { requiresAuth: true },
        },
        {
            path: '/search',
            name: 'search',
            component: Search,
            meta: { requiresAuth: true },
        },
        {
            path: '/posts/:id',
            name: 'post',
            component: Post,
            meta: { requiresAuth: true },
        },
        {
            path: '/create',
            name: 'create',
            component: CreatePost,
            meta: { requiresAuth: true },
        },
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: NotFound,
        },
    ],
})

let validatedToken = null

router.beforeEach(async (to) => {
    const token = localStorage.getItem('token')

    if (!token) {
        validatedToken = null

        if (to.meta.requiresAuth) {
            return { name: 'login' }
        }

        return
    }

    if (validatedToken !== token) {
        try {
            await api.get('/profile')
            validatedToken = token
        } catch {
            localStorage.removeItem('token')
            validatedToken = null

            if (to.meta.requiresAuth) {
                return { name: 'login' }
            }

            return
        }
    }

    if (to.name === 'login' || to.name === 'register') {
        return { name: 'home' }
    }
})

export default router
