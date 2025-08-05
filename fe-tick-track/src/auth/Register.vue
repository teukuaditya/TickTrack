<script setup>
    import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

    const authStore = useAuthStore();
    const {loading, error} = storeToRefs(authStore);
    const {register} = authStore;

    const form = ref({
        name: null,
        email: null,
        password: null,
    });

    const handleSubmit = async () => {
        await register(form.value);
    }
</script>

<template>
  <form @submit.prevent="handleSubmit" class="max-w-md mx-auto bg-white p-8 rounded-xl shadow space-y-6">
    <h2 class="text-2xl font-bold text-center mb-4">Daftar Akun Baru</h2>

    <!-- Error Alert Global -->
    <div v-if="error && typeof error === 'string'" class="bg-red-100 text-red-700 px-4 py-2 rounded text-sm">
      {{ error }}
    </div>

    <!-- Name -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        required
        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500"
        placeholder="Nama Lengkap"
        :class="{ 'border-red-500': error && error.name }"
      />
      <p v-if="error && error.name" class="text-xs text-red-600 mt-1">{{ error.name }}</p>
    </div>

    <!-- Email -->
    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        required
        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-blue-500"
        placeholder="Email"
        :class="{ 'border-red-500': error && error.email }"
      />
      <p v-if="error && error.email" class="text-xs text-red-600 mt-1">{{ error.email }}</p>
    </div>

    <!-- Password -->
    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
      <div class="mt-1 relative">
        <input
          type="password"
          id="password"
          v-model="form.password"
          required
          class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500"
          placeholder="••••••"
          :class="{ 'border-red-500': error && error.password }"
        />
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
          <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none" tabindex="-1" disabled>
            <i data-feather="eye" class="w-4 h-4" id="password-toggle"></i>
          </button>
        </div>
      </div>
      <p v-if="error && error.password" class="text-xs text-red-600 mt-1">{{ error.password }}</p>
    </div>

    <!-- Submit Button -->
    <div>
      <button
        type="submit"
        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 transition"
        :disabled="loading"
      >
        <span v-if="loading">Mendaftar...</span>
        <span v-else>Daftar</span>
      </button>
    </div>

    <!-- Divider -->
    <div class="flex items-center my-4">
      <div class="flex-grow border-t border-gray-200"></div>
      <span class="mx-2 text-gray-400 text-xs">atau</span>
      <div class="flex-grow border-t border-gray-200"></div>
    </div>

    <!-- Link ke login -->
    <div class="text-center text-sm text-gray-600">
      Sudah punya akun?
      <router-link to="/login" class="text-blue-600 hover:underline">Masuk</router-link>
    </div>
  </form>
</template>

<style scoped>
/* Add any component-specific styles here */
</style>