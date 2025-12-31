<!--
2025 DeveMain

All rights reserved. For internal use only.
Unauthorized copying, modification, or distribution is prohibited.

@author    DeveMain <devemain@gmail.com>
@copyright 2025 DeveMain
@license   PROPRIETARY
@link      https://github.com/DeveMain
-->

<script setup>
import { ref, onMounted } from 'vue'

// Reactive state
const currentFact = ref(null)
const isLoading = ref(false)
const errorMessage = ref('')
const viewsCount = ref('0')
const factId = ref('-')
const lastUpdated = ref('-')
const copySuccess = ref(false)

// API URL
const apiUrl = '/api/v1/fact'

// Methods
async function fetchFact() {
  isLoading.value = true
  errorMessage.value = ''
  copySuccess.value = false

  try {
    const response = await fetch(apiUrl)
    const data = await response.json()

    if (data.success) {
      currentFact.value = data.data
      factId.value = data.data.id || '-'
      viewsCount.value = data.data.views || '0'
      lastUpdated.value = new Date().toLocaleTimeString()
    } else {
      errorMessage.value = data.message || 'Failed to load fact'
    }
  } catch (error) {
    console.error('Fetch error:', error)
    errorMessage.value = 'Network error. Please try again.'
  } finally {
    isLoading.value = false
  }
}

async function copyFact() {
  if (!currentFact.value) {
    errorMessage.value = 'No fact to copy! Get one first.'
    return
  }

  try {
    await navigator.clipboard.writeText(currentFact.value.content)
    copySuccess.value = true

    setTimeout(() => {
      copySuccess.value = false
    }, 500)
  } catch (err) {
    console.error('Copy failed:', err)
    errorMessage.value = 'Failed to copy fact'
  }
}

function loadFactOnStart() {
  setTimeout(() => fetchFact(), 500)
}

// Lifecycle hooks
onMounted(() => {
  loadFactOnStart()
})
</script>

<template>
  <div class="bg-white rounded-lg shadow-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
      <span class="blink">AI:</span> Did You Know <span class="blink">?</span>
    </h2>

    <div id="fact-content" class="min-h-36">
      <p class="fact" :class="{ 'placeholder-fact': !currentFact && !errorMessage, 'fact-error': errorMessage }">
        {{ errorMessage ? `❌ ${errorMessage}` : (currentFact ? currentFact.content : 'Click the button to get an interesting fact!') }}
      </p>
    </div>

    <div class="flex gap-3 justify-center mt-6 flex-wrap">
      <button
          id="get-fact"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          :disabled="isLoading"
          @click="fetchFact"
      >
        <i class="fas" :class="isLoading ? 'fa-spinner fa-spin' : 'fa-rotate'"></i>
        {{ isLoading ? 'Loading...' : 'Get New Fact' }}
      </button>
      <button
          id="copy-fact"
          class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
          :class="{ 'bg-green-700': copySuccess }"
          @click="copyFact"
      >
        <i class="fas" :class="copySuccess ? 'fa-check' : 'fa-copy'"></i>
        {{ copySuccess ? 'Copied!' : 'Copy Fact' }}
      </button>
    </div>

    <div class="mt-6 pt-4 border-t border-gray-200 text-sm text-gray-500 text-center">
      <span id="views-count">{{ viewsCount }}</span> views •
      ID: <span id="fact-id">{{ factId }}</span> •
      Updated: <span id="last-updated">{{ lastUpdated }}</span>
    </div>
  </div>
</template>
