<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-indigo-600 shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-white">Commit Diff</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <div v-if="debug" class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-md">
          <h2 class="font-bold">Debug Information</h2>
          <pre class="mt-2 text-sm overflow-x-auto">{{ debugInfo }}</pre>
        </div>

        <div v-if="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-md">
          <p class="font-bold">Error</p>
          <p>{{ error }}</p>
        </div>

        <div v-else-if="commitData" class="bg-white shadow-md rounded-lg overflow-hidden">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-2xl font-semibold mb-2 text-gray-800">{{ commitData.commit.message }}</h2>
            <p class="text-sm text-gray-600">
              By {{ commitData.commit.author.name }} on {{ new Date(commitData.commit.author.date).toLocaleString() }}
            </p>
          </div>

          <div v-if="commitData.files && commitData.files.length" class="p-6">
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Changed Files</h2>
            <ul class="space-y-6">
              <li v-for="file in commitData.files" :key="file.filename" class="bg-gray-50 rounded-lg overflow-hidden">
                <h3 class="text-lg font-medium p-4 bg-gray-100 border-b">{{ file.filename }}</h3>
                <div class="p-4 overflow-x-auto">
                  <pre class="text-sm"><code v-html="highlightDiff(file.patch)"></code></pre>
                </div>
              </li>
            </ul>
          </div>
        </div>

        <div v-else class="flex justify-center items-center h-32">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import Layout from '../Layout.vue'

export default defineComponent({
  name: 'CommitDiff',
  layout: Layout,
  props: {
    commitData: Object,
    error: String,
    debug: Object,
  },
  computed: {
    debugInfo() {
      if (!this.debug) return ''

      const relevantInfo = {
        commitSHA: this.commitData?.sha,
        filesChanged: this.commitData?.files?.length,
        error: this.error,
        ...this.debug
      }

      return JSON.stringify(relevantInfo, null, 2)
    }
  },
  methods: {
    highlightDiff(patch) {
      if (!patch) return ''
      return patch.split('\n').map(line => {
        if (line.startsWith('+')) {
          return `<span class="text-green-700 bg-green-100 block">${this.escapeHtml(line)}</span>`
        } else if (line.startsWith('-')) {
          return `<span class="text-red-700 bg-red-100 block">${this.escapeHtml(line)}</span>`
        } else {
          return `<span class="text-gray-700 block">${this.escapeHtml(line)}</span>`
        }
      }).join('\n')
    },
    escapeHtml(unsafe) {
      return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
    }
  }
})
</script>

<style scoped>
pre {
  tab-size: 2;
}
</style>
