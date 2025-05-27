<template>
    <div class="bg-white p-4 rounded shadow mt-6">
      <Pie :data="chartData" :options="chartOptions" />
    </div>
  </template>
  
  <script>
  import {
    Chart as ChartJS,
    Tooltip,
    Legend,
    ArcElement
  } from 'chart.js'
  import { Pie } from 'vue-chartjs'
  
  ChartJS.register(ArcElement, Tooltip, Legend)
  
  export default {
    name: 'ChangeRequestPieChart',
    components: { Pie },
    props: {
      pending: Number,
      approved: Number
    },
    computed: {
      chartData() {
        return {
          labels: ['Pending', 'Approved'],
          datasets: [
            {
              data: [this.pending, this.approved],
              backgroundColor: ['#f59e0b', '#10b981']
            }
          ]
        }
      },
      chartOptions() {
        return {
          responsive: true,
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      }
    }
  }
  </script>
  
  <style scoped>
  div {
    height: 250px;
  }
  </style>
  