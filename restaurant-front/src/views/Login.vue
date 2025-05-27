<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-md rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Kyçu në llogari</h2>
      <form @submit.prevent="login">
        <div class="mb-4">
          <label class="block mb-1 text-gray-700">Email</label>
          <input v-model="email" type="email" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-6">
          <label class="block mb-1 text-gray-700">Fjalëkalimi</label>
          <input v-model="password" type="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
          Kyçu
        </button>
      </form>
      <p v-if="error" class="text-red-600 mt-4 text-center">{{ error }}</p>
    </div>
  </div>
</template>

<script>
import api from '../axios' // ose '../../axios' varësisht ku e ke vendosur

export default {
  name: "LoginPage",
  data() {
    return {
      email: '',
      password: '',
      error: ''
    }
  },
  methods: {
    async login() {
      this.error = ''
      try {
        // Nëse backend-i yt përdor Sanctum, çel këtë rresht
        // await api.get('/sanctum/csrf-cookie')

        const res = await api.post('/login', {
          email: this.email,
          password: this.password
        })

        localStorage.setItem('token', res.data.token)
        localStorage.setItem('user', JSON.stringify(res.data.user))
        api.defaults.headers.common['Authorization'] = `Bearer ${res.data.token}`

        if (res.data.user.role === 'admin') {
          this.$router.push('/admin')
        } else {
          this.$router.push('/create')
        }

      } catch (err) {
        this.error = err.response?.data?.message || 'Gabim gjatë kyçjes'
        console.error(err)
      }
    }
  }
}
</script>
