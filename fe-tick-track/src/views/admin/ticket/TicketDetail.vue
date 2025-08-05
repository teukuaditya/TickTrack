<script setup>
import { useTicketStore } from '@/stores/ticket'
import feather from 'feather-icons'
import { capitalize } from 'lodash'
import { DateTime } from 'luxon'
import { storeToRefs } from 'pinia'
import { onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const ticket = ref({})
const form = ref({
  status: '',
  content: '',
})

const ticketStore = useTicketStore()
const { succes, error, loading } = storeToRefs(ticketStore)
const { fetchTicket, createTicketReply } = ticketStore

const fetchTicketDetails = async () => {
    const response = await fetchTicket(route.params.code)

    ticket.value = response
    form.value.status = response.status
}

const handleSubmit = async () => {
    await createTicketReply(route.params.code, form.value)

    await fetchTicketDetails()
}

onMounted(() => {
    fetchTicketDetails()
    feather.replace()
})
</script>

<template>
  <div class="p-6">
    <!-- Ticket Info -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">{{ ticket.title }}</h3>
            <div class="mt-4 flex items-center space-x-4">
              <span class="px-3 py-1 text-sm rounded-lg" :class="{
                'text-blue-700 bg-blue-100': ticket.status === 'open',
                'text-yellow-700 bg-yellow-100': ticket.status === 'onprogress' || ticket.status === 'onprogress',
                'text-green-700 bg-green-100': ticket.status === 'resolved',
                'text-red-700 bg-red-100': ticket.status === 'rejected'
              }">
                {{ capitalize(ticket.status) }}
              </span>
              <span class="px-3 py-1 text-sm rounded-lg" :class="{
                'text-red-700 bg-red-100': ticket.priority === 'high',
                'text-yellow-700 bg-yellow-100': ticket.priority === 'medium',
                'text-green-700 bg-green-100': ticket.priority === 'low'
              }">
                {{ capitalize(ticket.priority) }}
              </span>
            </div>
            <span class="text-sm text-gray-500">Dilaporkan oleh {{ ticket.user?.name }}</span>
            <div class="mt-2 text-sm text-gray-500">
              Dibuat: {{ ticket.created_at ? DateTime.fromISO(ticket.created_at).toFormat('dd MMM yyyy HH:mm') : '-' }}
            </div>
            <div class="mt-2 text-sm text-gray-500" v-if="ticket.category">
              Kategori: {{ ticket.category.name }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="flex items-center justify-end space-x-4 mb-6">
      <button
        class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50"
      >
        <i data-feather="download" class="w-4 h-4 inline-block mr-2"></i>
        Lampiran
      </button>
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"
      >
        <i data-feather="check-circle" class="w-4 h-4 inline-block mr-2"></i>
        Selesaikan Tiket
      </button>
    </div>

    <!-- Discussion Thread -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
      <h4 class="text-lg font-semibold mb-4">Diskusi</h4>
      <div v-if="ticket.replies && ticket.replies.length">
        <div v-for="reply in ticket.replies" :key="reply.id" class="mb-4">
          <div class="flex items-center mb-1">
            <span class="font-semibold text-gray-700">{{ reply.user?.name || 'User' }}</span>
            <span class="ml-2 text-xs text-gray-400">
              {{ reply.created_at ? DateTime.fromISO(reply.created_at).toFormat('dd MMM yyyy HH:mm') : '' }}
            </span>
          </div>
          <div class="bg-gray-50 rounded p-3 text-sm text-gray-800">
            {{ reply.content }}
          </div>
        </div>
      </div>
      <div v-else class="text-gray-400 mb-4">Belum ada diskusi.</div>
      <!-- Form Balas -->
      <form @submit.prevent="submitReply" class="mt-4">
        <textarea
          v-model="form.content"
          rows="3"
          class="w-full border border-gray-200 rounded-lg p-2 text-sm focus:outline-none focus:border-blue-500"
          placeholder="Tulis balasan..."
        ></textarea>
        <div class="flex justify-end mt-2">
          <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700"
            :disabled="loading"
          >
            <span v-if="!loading">
                Kirim jawaban
            </span>
            <span v-else>
                loading...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>