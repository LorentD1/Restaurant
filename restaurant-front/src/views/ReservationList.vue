<template>
  <div class="max-w-5xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700">Rezervimet e Mia</h2>

    <div v-if="reservations.length === 0" class="text-center text-gray-500">
      Nuk ka rezervime aktive.
    </div>

    <div
      v-for="reservation in reservations"
      :key="reservation.id"
      class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200"
    >
      <p><strong>Klienti:</strong> {{ reservation.customer_name }}</p>
      <p><strong>Telefoni:</strong> {{ reservation.customer_phone }}</p>
      <p><strong>Tavolina:</strong> {{ reservation.table?.name }}</p>
      <p><strong>Koha:</strong> {{ reservation.reservation_time }}</p>
      <p><strong>Persona:</strong> {{ reservation.guest_count }}</p>

      <div v-if="editingId === reservation.id" class="mt-4 space-y-2">
        <input v-model="editData.customer_name" type="text" class="w-full border px-3 py-2 rounded" />
        <input v-model="editData.customer_phone" type="text" class="w-full border px-3 py-2 rounded" />
        <input v-model="editData.reservation_time" type="datetime-local" class="w-full border px-3 py-2 rounded" />
        <input v-model="editData.guest_count" type="number" min="1" class="w-full border px-3 py-2 rounded" />

        <div class="flex space-x-2 mt-3">
          <button @click="submitEditRequest(reservation.id)" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Dërgo Ndryshim</button>
          <button @click="cancelEdit" class="bg-gray-500 text-white px-4 py-1 rounded hover:bg-gray-600">Anulo</button>
        </div>
      </div>

      <div v-else class="mt-4 flex space-x-3">
        <button @click="startEdit(reservation)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Kërko Ndryshim</button>
        <button @click="requestDelete(reservation.id)" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Kërko Fshirje</button>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/axios' // instanca me baseURL
import { useToast } from 'vue-toastification'

export default {
  name: "ReservationList",
  data() {
    return {
      reservations: [], // Lista e rezervimeve
      editingId: null, // ID për redaktim
      editData: {
        customer_name: '',
        customer_phone: '',
        reservation_time: '',
        guest_count: 1
      },
      toast: null // Për përdorimin e toast notifications
    }
  },
  mounted() {
    this.toast = useToast()
    this.fetchReservations() // Merr rezervimet
  },
  methods: {
    // Funksioni për të marrë rezervimet nga backend
    fetchReservations() {
      api.get('/reservations')
        .then(res => this.reservations = res.data)
        .catch(() => this.toast.error("Gabim gjatë ngarkimit të rezervimeve"))
    },

    // Funksioni për të filluar redaktimin e një rezervimi
    startEdit(reservation) {
      this.editingId = reservation.id
      this.editData = {
        customer_name: reservation.customer_name,
        customer_phone: reservation.customer_phone,
        reservation_time: reservation.reservation_time,
        guest_count: reservation.guest_count
      }
    },

    // Funksioni për të anuluar redaktimin
    cancelEdit() {
      this.editingId = null
      this.editData = {}
    },

    // Funksioni për të dërguar kërkesën për ndryshim të rezervimit
    submitEditRequest(reservationId) {
      api.post('/change-requests', {
        reservation_id: reservationId,
        type: 'edit',
        new_data: JSON.stringify(this.editData) // ✅ Ndryshimi i bërë këtu
      })
        .then(() => {
          this.editingId = null
          this.toast.success("Kërkesa për ndryshim u dërgua me sukses!")
        })
        .catch(() => this.toast.error("Gabim gjatë dërgimit të kërkesës"))
    },

    // Funksioni për të dërguar kërkesën për fshirje të një rezervimi
    requestDelete(reservationId) {
      api.post('/change-requests', {
        reservation_id: reservationId,
        type: 'delete'
      })
        .then(() => this.toast.success("Kërkesa për fshirje u dërgua!"))
        .catch(() => this.toast.error("Gabim gjatë dërgimit të kërkesës për fshirje"))
    }
  }
}
</script>
