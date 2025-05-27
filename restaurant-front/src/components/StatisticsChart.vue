<template>
    <div class="bg-white p-4 rounded shadow">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script>
  import {
    Chart as ChartJS,
    BarElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
  } from 'chart.js'
  import { Bar } from 'vue-chartjs'
  
  ChartJS.register(BarElement, CategoryScale, LinearScale, Tooltip, Legend)
  
  export default {
    name: 'StatisticsChart',
    components: { Bar },
    props: {
      totalReservations: Number,
      totalUsers: Number,
      totalChangeRequests: Number
    },
    computed: {
      chartData() {
        return {
          labels: ['Rezervime', 'Përdorues', 'Kërkesa për Ndryshim'],
          datasets: [
            {
              label: 'Statistika Totale',
              backgroundColor: ['#4ade80', '#60a5fa', '#facc15'],
              data: [
                this.totalReservations,
                this.totalUsers,
                this.totalChangeRequests
              ]
            }
          ]
        }
      },
      chartOptions() {
        return {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1
              }
            }
          }
        }
      }
    }
  }
  </script>
  
  <style scoped>
  div {
    height: 300px;
  }
  </style>
  