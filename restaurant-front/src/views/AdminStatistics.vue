<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Statistika</h2>

    <div class="mb-6">
      <StatisticsChart
        :totalReservations="stats.totalReservations"
        :totalUsers="stats.totalUsers"
        :totalChangeRequests="stats.totalChangeRequests"
      />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
      <div class="p-4 bg-green-100 rounded shadow">
        <p class="font-semibold">Numri total i rezervimeve:</p>
        <p class="text-2xl">{{ stats.totalReservations }}</p>
      </div>
      <div class="p-4 bg-blue-100 rounded shadow">
        <p class="font-semibold">Numri total i përdoruesve:</p>
        <p class="text-2xl">{{ stats.totalUsers }}</p>
      </div>
      <div class="p-4 bg-yellow-100 rounded shadow">
        <p class="font-semibold">Numri total i kërkesave për ndryshim:</p>
        <p class="text-2xl">{{ stats.totalChangeRequests }}</p>
      </div>
    </div>

    <ChangeRequestPieChart
      :pending="stats.pending_requests"
      :approved="stats.approved_requests"
    />
  </div>
</template>

<script>
import axios from 'axios'
import StatisticsChart from '../components/StatisticsChart.vue'
import ChangeRequestPieChart from '../components/ChangeRequestPieChart.vue'

export default {
components: {
  StatisticsChart,
  ChangeRequestPieChart
},
data() {
  return {
    stats: {
      totalReservations: 0,
      totalUsers: 0,
      totalChangeRequests: 0,
      pending_requests: 0,
      approved_requests: 0
    }
  }
},
async mounted() {
  try {
    const response = await axios.get('/admin/statistics', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    })
    this.stats = {
      totalReservations: response.data.reservations,
      totalUsers: response.data.users,
      totalChangeRequests: response.data.pending_requests + response.data.approved_requests,
      pending_requests: response.data.pending_requests,
      approved_requests: response.data.approved_requests
    }
  } catch (error) {
    console.error('Gabim gjatë marrjes së statistikave:', error)
  }
}
}
</script>
