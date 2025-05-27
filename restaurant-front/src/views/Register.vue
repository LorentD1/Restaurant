<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white shadow-md rounded-xl p-8 w-full max-w-md">
      <h2 class="text-2xl font-bold mb-6 text-center">Regjistrohu</h2>
      <form @submit.prevent="register">
        <div class="mb-4">
          <label class="block mb-1 text-gray-700">Emri</label>
          <input v-model="name" type="text" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-4">
          <label class="block mb-1 text-gray-700">Email</label>
          <input v-model="email" type="email" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-4">
          <label class="block mb-1 text-gray-700">Fjalëkalimi</label>
          <input v-model="password" type="password" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required />
        </div>
        <div class="mb-6">
          <label class="block mb-1 text-gray-700">Roli</label>
          <select v-model="role" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="user">Përdorues</option>
            <option value="admin">Administrator</option>
          </select>
        </div>
        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700 transition">
          Regjistrohu
        </button>
      </form>
      <p v-if="error" class="text-red-600 mt-4 text-center">{{ error }}</p>
      <p v-if="message" class="text-green-600 mt-4 text-center">{{ message }}</p>
    </div>
  </div>
</template>

<script>
import api from '../axios'

export default {
  name: "RegisterPage",
  data() {
    return {
      name: '',
      email: '',
      password: '',
      role: 'user',
      error: '',
      message: ''
    }
  },
  methods: {
    async register() {
      this.error = ''
      this.message = ''
      try {
        const res = await api.post('/register', {
          name: this.name,
          email: this.email,
          password: this.password,
          role: this.role
        })

        this.message = res.data.message
        this.$router.push('/login') // ose fute drejt në dashboard nëse do

      } catch (err) {
        this.error = err.response?.data?.message || 'Gabim gjatë regjistrimit'
        console.error(err)
      }
    }
  }
}
</script>
