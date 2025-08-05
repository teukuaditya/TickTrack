<script setup>
import { useTicketStore } from '@/stores/ticket'
import { capitalize, debounce } from 'lodash'
import { storeToRefs } from 'pinia'
import { onMounted, ref, watch } from 'vue'
import feather from 'feather-icons' // Impor feather
import { DateTime } from 'luxon' // Impor DateTime

const ticketStore = useTicketStore()
const { tickets } = storeToRefs(ticketStore)
const { fetchTickets } = ticketStore

const filters = ref({
  search: '',
  status: '',
  priority: '',
  date: '',
})

// Gunakan debounce dengan benar pada watch
watch(
  filters,
  debounce(() => {
    fetchTickets(filters.value).catch((error) => {
      console.error('Error fetching tickets:', error)
    })
  }, 300),
  { deep: true }
)

onMounted(async () => {
  try {
    await fetchTickets()
    feather.replace()
  } catch (error) {
    console.error('Error on mounted:', error)
  }
})
</script>

<template>
  <!-- Filters and Actions -->
  <div class="p-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Search -->
          <div class="relative">
            <input
              type="text"
              v-model="filters.search"
              placeholder="Cari tiket..."
              class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
            />
            <i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
          </div>

          <!-- Status Filter -->
          <select
            v-model="filters.status"
            class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500"
          >
            <option value="">Semua Status</option>
            <option value="open">Open</option>
            <option value="on_progress">On Progress</option>
            <option value="resolved">Resolved</option>
            <option value="rejected">Rejected</option>
          </select>

          <!-- Priority Filter -->
          <select
            v-model="filters.priority"
            class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500"
          >
            <option value="">Semua Prioritas</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
          </select>

          <!-- Date Filter -->
          <select
            v-model="filters.date"
            class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500"
          >
            <option value="">Semua Tanggal</option>
            <option value="today">Hari Ini</option>
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <!-- Tickets Table -->
  <div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              ID Tiket
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Judul
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Pelapor
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Kategori
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Status
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Prioritas
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tanggal
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Aksi
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
          <tr v-for="ticket in tickets" :key="ticket.code" class="hover:bg-gray-50">
            <td class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap">
              {{ ticket.code || '-' }}
            </td>
            <td class="px-4 py-2 text-gray-700 whitespace-nowrap">
              {{ ticket.title || '-' }}
            </td>
            <td class="px-4 py-2 text-gray-700 whitespace-nowrap">
              <div class="flex items-center gap-2">
                <img
                  :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(ticket.user?.name || '')}&background=0D8ABC&color=fff`"
                  class="w-8 h-8 rounded-full"
                  alt="avatar"
                />
                <span>{{ ticket.user?.name || '-' }}</span>
              </div>
            </td>
            <td class="px-4 py-2 text-gray-700 whitespace-nowrap">
              {{ ticket.category?.name || '-' }}
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <span
                class="px-2 py-1 text-xs font-medium rounded-full"
                :class="{
                  'text-blue-700 bg-blue-100': ticket.status === 'open',
                  'text-yellow-700 bg-yellow-100': ticket.status === 'on_progress',
                  'text-green-700 bg-green-100': ticket.status === 'resolved',
                  'text-red-700 bg-red-100': ticket.status === 'rejected'
                }"
              >
                {{ capitalize(ticket.status) || '-' }}
              </span>
            </td>
            <td class="px-4 py-2 whitespace-nowrap">
              <span
                class="px-3 py-1 text-xs font-medium rounded-full"
                :class="{
                  'text-red-700 bg-red-100': ticket.priority === 'high',
                  'text-yellow-700 bg-yellow-100': ticket.priority === 'medium',
                  'text-green-700 bg-green-100': ticket.priority === 'low'
                }"
              >
                {{ capitalize(ticket.priority) || '-' }}
              </span>
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-600">
              {{ ticket.created_at ? DateTime.fromISO(ticket.created_at).toFormat('dd MMMM yyyy HH:mm') : '-' }}
            </td>
            <td class="px-4 py-2 whitespace-nowrap text-sm">
              <RouterLink
                :to="{ name: 'admin.ticket.detail', params: { code: ticket.code } }"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium"
              >
                <i data-feather="message-square" class="w-4 h-4 mr-2"></i>
                Jawab
              </RouterLink>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>