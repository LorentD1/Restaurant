<template>
  <div class="max-w-7xl mx-auto mt-10 px-4">
    <h2 class="text-3xl font-bold mb-6 text-center text-indigo-700">Rezervoni Tavolinën</h2>

    <!-- Tavolinat e disponueshme -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 mb-8">
      <div
        v-for="table in tables"
        :key="table.id"
        class="cursor-pointer bg-white rounded-lg shadow-lg p-4 hover:scale-105 transition-transform duration-300"
        @click="selectTable(table)"
        :class="{'border-2 border-indigo-500': selectedTable?.id === table.id}"
      >
        <img :src="table.image" alt="Table image" class="w-full h-32 object-cover rounded mb-4" />
        <h3 class="text-lg font-semibold">{{ table.name }}</h3>
        <p class="text-sm text-gray-600">Persona: {{ table.seats }}</p>
      </div>
    </div>

    <!-- Form for filling reservation details -->
    <form @submit.prevent="submitReservation" v-if="selectedTable">
      <div class="mb-4">
        <label class="block mb-1">Emri i klientit</label>
        <input v-model="form.customer_name" type="text" class="w-full border p-2 rounded" />
        <p v-if="errors.customer_name" class="text-red-600 text-sm">{{ errors.customer_name }}</p>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Telefoni</label>
        <input v-model="form.customer_phone" type="text" class="w-full border p-2 rounded" />
        <p v-if="errors.customer_phone" class="text-red-600 text-sm">{{ errors.customer_phone }}</p>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Koha e rezervimit</label>
        <input v-model="form.reservation_time" type="datetime-local" class="w-full border p-2 rounded" />
        <p v-if="errors.reservation_time" class="text-red-600 text-sm">{{ errors.reservation_time }}</p>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Numri i personave</label>
        <input v-model="form.guest_count" type="number" class="w-full border p-2 rounded" min="1" />
        <p v-if="errors.guest_count" class="text-red-600 text-sm">{{ errors.guest_count }}</p>
      </div>

      <div class="mb-4">
        <label class="block mb-1">Koment</label>
        <input v-model="form.notes" type="text" class="w-full border p-2 rounded" placeholder="Shto një koment..." />
      </div>

      <div class="text-center">
        <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded">Rezervo</button>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'
import { useToast } from 'vue-toastification'

export default {
  data() {
    return {
      tables: [],
      selectedTable: null, // Tavolina e zgjedhur
      form: {
        customer_name: '',
        customer_phone: '',
        table_id: '',
        reservation_time: '',
        guest_count: 1,
        notes: ''
      },
      errors: {}
    }
  },
  mounted() {
    const toast = useToast()
    axios.get(`${process.env.VUE_APP_API_URL}/tables`)
      .then(res => this.tables = res.data)
      .catch(() => toast.error("Nuk u mund të merret lista e tavolinave"));
  },
  methods: {
    validateForm() {
      this.errors = {}

      if (!this.form.customer_name) {
        this.errors.customer_name = "Emri është i detyrueshëm"
      }

      if (!this.form.customer_phone) {
        this.errors.customer_phone = "Telefoni është i detyrueshëm"
      } else if (!/^\d+$/.test(this.form.customer_phone)) {
        this.errors.customer_phone = "Telefoni duhet të përmbajë vetëm numra"
      }

      if (!this.form.table_id) {
        this.errors.table_id = "Zgjidh një tavolinë"
      }

      if (!this.form.reservation_time) {
        this.errors.reservation_time = "Zgjidh kohën e rezervimit"
      }

      if (!this.form.guest_count || this.form.guest_count < 1) {
        this.errors.guest_count = "Së paku 1 person"
      }

      return Object.keys(this.errors).length === 0
    },

    selectTable(table) {
      this.selectedTable = table
      this.form.table_id = table.id
    },

    submitReservation() {
      const toast = useToast()

      if (!this.validateForm()) {
        toast.error("Plotëso të gjitha fushat saktë.")
        return
      }

      axios.post(`${process.env.VUE_APP_API_URL}/reservations`, this.form)
        .then(() => {
          toast.success("Rezervimi u shtua me sukses!")
          this.form = {
            customer_name: '',
            customer_phone: '',
            table_id: '',
            reservation_time: '',
            guest_count: 1,
            notes: ''
          }
        })
        .catch(error => {
          const msg = error.response?.data?.message || "Gabim gjatë krijimit të rezervimit"
          toast.error(msg)
        });
    }
  }
}
</script>

<style scoped>
/* Shtoni stilizime për tavolinat dhe selektimin e tyre */
.cursor-pointer {
  cursor: pointer;
}
.border-indigo-500 {
  border-color: #6366f1;
}
</style>
