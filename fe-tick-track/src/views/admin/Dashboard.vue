<script setup>
import { useDashboardStore } from '@/stores/dashboard'
import { useTicketStore } from '@/stores/ticket'
import { Chart } from 'chart.js/auto'
import feather from 'feather-icons'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { onMounted, watch } from 'vue'

const dashboardStore = useDashboardStore()
const { statistic } = storeToRefs(dashboardStore)
const { fetchStatistics } = dashboardStore

const ticketStore = useTicketStore()
const { tickets } = storeToRefs(ticketStore)
const { fetchTickets } = ticketStore

const toggleTicketMenu = (ticket) => {
  ticket.showMenu = !ticket.showMenu
}

let chart = null

watch(statistic, () => {
  if (statistic.value && chart) {
    chart.data.datasets[0].data = [
      statistic.value.status_distribution?.open,
      statistic.value.status_distribution?.onprogress, 
      statistic.value.status_distribution?.resolved,
      statistic.value.status_distribution?.rejected
    ]
    chart.update()
  }
}, { deep: true })

onMounted(async () => {
  await fetchTickets()
  await fetchStatistics()

  const statusCtx = document.getElementById('statusChart')?.getContext('2d')

  if (statusCtx && statistic.value) {
    chart = new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['open', 'onprogress', 'resolved', 'rejected'],
        datasets: [{
          data: [
            statistic.value.status_distribution?.open,
            statistic.value.status_distribution?.onprogress,
            statistic.value.status_distribution?.resolved,
            statistic.value.status_distribution?.rejected
          ],
          backgroundColor: [
            '#3B82F6',
            '#F59E0B',
            '#10B981',
            '#EF4444'
          ]
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'bottom'
          }
        },
        cutout: '70%'
      }
    })
  }

  feather.replace()
})
</script>

<template>
  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Tiket -->
    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600">Total Tiket</p>
          <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.total_tickets }}</h3>
        </div>
        <div class="p-3 bg-blue-50 rounded-lg">
          <i data-feather="tag" class="w-6 h-6 text-blue-600"></i>
        </div>
      </div>
      <div class="mt-4 flex items-center text-sm">
        <span class="text-green-500 flex items-center">
          <i data-feather="trending-up" class="w-4 h-4 mr-1"></i>
          12%
        </span>
        <span class="text-gray-500 ml-2">vs bulan lalu</span>
      </div>
    </div>

    <!-- Tiket Aktif -->
    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600">Tiket Aktif</p>
          <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.active_tickets }}</h3>
        </div>
        <div class="p-3 bg-yellow-50 rounded-lg">
          <i data-feather="clock" class="w-6 h-6 text-yellow-600"></i>
        </div>
      </div>
    </div>

    <!-- Tiket Selesai -->
    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600">Tiket Selesai</p>
          <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.resolved_tickets }}</h3>
        </div>
        <div class="p-3 bg-green-50 rounded-lg">
          <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
        </div>
      </div>
    </div>

    <!-- Tiket Ditolak -->
    <div class="stat-card bg-white rounded-xl shadow-sm p-6 border border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600">Tiket Ditolak</p>
          <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ statistic?.rejected_tickets }}</h3>
        </div>
        <div class="p-3 bg-red-50 rounded-lg">
          <i data-feather="x-circle" class="w-6 h-6 text-red-600"></i>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart Status Tiket -->
  <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-8">
    <h4 class="text-lg font-semibold mb-4">Distribusi Status Tiket</h4>
    <canvas id="statusChart" height="120"></canvas>
    <div class="flex justify-center mt-4 space-x-6">
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span>
        <span class="text-sm text-gray-600">Open</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-yellow-500"></span>
        <span class="text-sm text-gray-600">On Progress</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>
        <span class="text-sm text-gray-600">Resolved</span>
      </div>
      <div class="flex items-center space-x-2">
        <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
        <span class="text-sm text-gray-600">Rejected</span>
      </div>
    </div>
  </div>

  <!-- Daftar Tiket Terbaru -->
  <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
    <h4 class="text-lg font-semibold mb-4">Tiket Terbaru</h4>
    <table class="min-w-full divide-y divide-gray-200">
      <thead>
        <tr>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
          <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <tr v-for="ticket in tickets.slice(0,5)" :key="ticket.id">
          <td class="px-4 py-2 text-gray-700">{{ ticket.title }}</td>
          <td class="px-4 py-2">
            <span
              :class="{
                'bg-blue-100 text-blue-700': ticket.status === 'open',
                'bg-yellow-100 text-yellow-700': ticket.status === 'onprogress',
                'bg-green-100 text-green-700': ticket.status === 'resolved',
                'bg-red-100 text-red-700': ticket.status === 'rejected'
              }"
              class="px-2 py-1 rounded text-xs font-semibold"
            >
              {{ ticket.status }}
            </span>
          </td>
          <td class="px-4 py-2 text-gray-500">{{ DateTime.fromISO(ticket.created_at).toFormat('dd LLL yyyy') }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>