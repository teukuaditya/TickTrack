<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)
const { logout } = authStore

const showUserMenu = ref(false)

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = async () => {
  await logout()
}
</script>

<template>
  <nav class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Kiri -->
        <div class="flex items-center">
          <a href="#" class="flex items-center">
            <i data-feather="activity" class="w-8 h-8 text-blue-600"></i>
            <span class="ml-2 text-xl font-bold text-blue-600">TickTrack</span>
          </a>
        </div>

        <!-- Kanan -->
        <div class="flex items-center space-x-4">
          <!-- Notifikasi -->
          <button
            class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-full"
          >
            <i data-feather="bell" class="w-6 h-6"></i>
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
          </button>

          <!-- User Dropdown -->
          <div class="relative">
            <button
              class="flex items-center space-x-2"
              @click="toggleUserMenu"
            >
              <img
                src="https://i.pravatar.cc/300"
                alt="User Avatar"
                class="w-8 h-8 rounded-full"
              />
              <span class="text-sm font-medium text-gray-700">{{ user.name }}</span>
              <i data-feather="chevron-down" class="w-4 h-4 text-gray-500"></i>
            </button>

            <!-- Dropdown -->
            <div
              v-if="showUserMenu"
              class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50"
            >
              <a
                href="#"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >Profile</a>
              <a
                href="#"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >Settings</a>
              <button
                @click="handleLogout"
                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
              >Logout</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>
