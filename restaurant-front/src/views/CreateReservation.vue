<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-xl">
      <h2 class="text-2xl font-bold mb-6 text-center">Krijo Rezervim</h2>

      <div v-if="!selectedTable">
        <!-- Tavolinat si karta -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-8">
          <div
            v-for="table in tables"
            :key="table.id"
            class="cursor-pointer bg-white rounded-lg shadow-md p-4 hover:scale-105 transition-transform duration-300"
            @click="selectTable(table)"
          >
            <img :src="'/storage/tables/' + table.image" alt="Table image" class="w-full h-32 object-cover rounded mb-4" />
            <h3 class="text-sm font-semibold">{{ table.name }}</h3>
            <p class="text-sm text-gray-600">Persona: {{ table.seats }}</p>
          </div>
        </div>
      </div>

      <!-- Form për të plotësuar rezervimin -->
      <form v-if="selectedTable" @submit.prevent="submitReservation">
        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Emri i klientit</label>
          <input v-model="form.customer_name" type="text" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300" required />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Telefoni</label>
          <input v-model="form.customer_phone" type="text" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300" required />
        </div>

        <div class="mb-4">
          <label class="block text-gray-700 mb-1">Koha e rezervimit</label>
          <input v-model="form.reservation_time" type="datetime-local" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300" required />
        </div>

        <div class="mb-6">
          <label class="block text-gray-700 mb-1">Numri i personave</label>
          <input v-model="form.guest_count" type="number" min="1" class="w-full px-4 py-2 border rounded focus:outline-none focus:ring focus:ring-blue-300" required />
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
          Rezervo
        </button>

        <p v-if="error" class="text-red-600 text-center mt-4">{{ error }}</p>
        <p v-if="success" class="text-green-600 text-center mt-4">{{ success }}</p>
      </form>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import axios from 'axios'

export default defineComponent({
  name: "CreateReservation",
  data() {
    return {
      form: {
        customer_name: '',
        customer_phone: '',
        table_id: 0, // Changed this to standard number initialization
        reservation_time: '',
        guest_count: 1, // Changed this to standard number initialization
      },
      tables: [], // Array to hold the table data
      selectedTable: null, // This will hold the selected table data
      success: '',
      error: ''
    }
  },
  mounted() {
    this.fetchTables()
  },
  methods: {
    fetchTables() {
      axios.get('/tables')
        .then(res => {
          this.tables = res.data
        })
        .catch(() => {
          this.error = "Nuk u mund të ngarkohen tavolinat."
        })
    },
    selectTable(table) {
      this.selectedTable = table
      this.form.table_id = table.id
    },
    submitReservation() {
      this.error = ''
      this.success = ''

      axios.post('/reservations', this.form)
        .then(() => {
          this.success = "Rezervimi u krijua me sukses!"
          this.form = {
            customer_name: '',
            customer_phone: '',
            table_id: 0,
            reservation_time: '',
            guest_count: 1
          }
          this.selectedTable = null // Reset the selected table
        })
        .catch(err => {
          this.error = err.response?.data?.message || "Gabim gjatë rezervimit."
        })
    }
  }
})
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
</style>
