<template>
  <div class="max-w-6xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-blue-700">Paneli i Adminit</h2>

    <div class="mb-4 flex items-center justify-center space-x-2">
      <input type="checkbox" v-model="showArchived" id="archiveToggle" />
      <label for="archiveToggle" class="text-gray-700 font-medium">Shfaq të arkivuara (approved/rejected)</label>
    </div>

    <div class="mb-6 flex justify-center space-x-4">
      <button
        v-for="s in statuses"
        :key="s"
        @click="activeFilter = s"
        :class="[ 'px-4 py-2 rounded font-semibold border',
          activeFilter === s
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
        ]"
      >
        {{ s === 'all' ? 'Të gjitha' : s.charAt(0).toUpperCase() + s.slice(1) }}
      </button>
    </div>

    <div v-if="filteredRequests.length === 0" class="text-center text-gray-500">
      Nuk ka kërkesa për këtë kategori.
    </div>

    <div v-for="req in filteredRequests" :key="req.id" class="bg-white rounded-xl shadow-md p-6 mb-6 border-l-4">
      <!-- Content -->
    </div>
  </div>
</template>

<script>
import api from '@/axios'

export default {
  name: "AdminDashboard",
  data() {
    return {
      requests: [],
      showArchived: false,
      activeFilter: 'all',
      statuses: ['all', 'pending', 'approved', 'rejected']
    }
  },
  computed: {
    filteredRequests() {
      if (this.activeFilter === 'all') {
        return this.showArchived ? this.requests : this.requests.filter(req => req.status === 'pending')
      }
      return this.requests.filter(req => req.status === this.activeFilter)
    }
  },
  mounted() {
    this.fetchRequests()
  },
  methods: {
    async fetchRequests() {
      try {
        const res = await api.get('/change-requests')
        this.requests = res.data
      } catch (err) {
        console.error(err)
        alert("Gabim gjatë ngarkimit të kërkesave. Ju lutem kyçuni sërish.")
      }
    }
  }
}
</script>
