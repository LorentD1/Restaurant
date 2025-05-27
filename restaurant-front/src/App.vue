<template>
  <div>
    <nav class="bg-white shadow p-4 flex justify-between items-center">
      <div class="space-x-4">
        <router-link v-if="isLoggedIn" to="/create" class="nav-link">Rezervo</router-link>
        <router-link v-if="isLoggedIn" to="/list" class="nav-link">Rezervimet</router-link>
        <router-link v-if="isLoggedIn" to="/my-requests" class="nav-link">Kërkesat e mia</router-link>
        <router-link v-if="isAdmin" to="/admin" class="nav-link">Admin</router-link>
        <router-link v-if="isAdmin" to="/admin/statistics" class="nav-link">Statistika</router-link>
      </div>

      <div class="space-x-4">
        <router-link v-if="!isLoggedIn" to="/login" class="text-blue-600 font-semibold">Kyçu</router-link>
        <router-link v-if="!isLoggedIn" to="/register" class="text-green-600 font-semibold">Regjistrohu</router-link>
        <button v-if="isLoggedIn" @click="logout" class="logout-btn">Shkyçu</button>
      </div>
    </nav>

    <main class="p-4 bg-gray-50 min-h-screen">
      <router-view />
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import { toast } from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'

export default {
  name: 'App',
  computed: {
    isLoggedIn() {
      return !!localStorage.getItem('token')
    },
    isAdmin() {
      const user = JSON.parse(localStorage.getItem('user') || '{}')
      return user.role === 'admin'
    }
  },
  methods: {
    async logout() {
      try {
        await axios.post('/logout')
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        this.$router.push('/login')
        toast.success('Ju jeni shkyçur me sukses!')
      } catch (error) {
        toast.error('Gabim gjatë shkyçjes')
      }
    }
  }
}
</script>

<style scoped>
.nav-link {
  @apply text-gray-700 hover:text-blue-600 font-medium;
}
.logout-btn {
  @apply bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600;
}
</style>
