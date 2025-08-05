import { handleError } from '@/helpers/errorHelper'
import { axiosInstance } from '@/plugins/axios'
import router from '@/router'
import Cookies from 'js-cookie'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
    error: null,
    success: null,
  }),

  getters: {
    token: () => Cookies.get('token'),
    isAuthenticated: (state) => !!state.user,
  },

  actions: {
    async login(credentials) {
      this.loading = true

      try {
        const response = await axiosInstance.post('/login', credentials)

        const token = response.data.data.token
        Cookies.set('token', token)

        this.success = response.data.message

        if (response.data.data.user.role === 'admin') {
          router.push({ name: 'admin.dashboard' })
        } else {
          router.push({ name: 'app.dashboard' })
        }
      } catch (error) {
        this.error = handleError(error)
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true

      try {
        const response = await axiosInstance.post('/logout')

        Cookies.remove('token')

        this.user = null
        this.error = null
        this.success = response.data.message

        router.push({ name: 'login' })
      } catch (error) {
        this.error = handleError(error)
      } finally {
        this.loading = false
      }
    }
  },

  async register(credentials) {
    this.loading = true

    try {
      const response = await axiosInstance.post('/register', credentials)
      this.success = response.data.message
      const token = response.data.data.token
      Cookies.set('token', token)
      router.push({ name: 'app.dashboard' })
    }catch (error) {
      this.error = handleError(error)
    } finally {
      this.loading = false
    }
  }
})





