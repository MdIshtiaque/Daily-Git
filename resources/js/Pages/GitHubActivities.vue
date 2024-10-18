<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 class="text-3xl font-bold text-gray-900 mb-8">GitHub Activities</h1>

      <!-- Filter Section -->
      <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-6 mb-8">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Filters</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label for="repo-filter" class="block text-sm font-medium text-gray-700 mb-1">Repository</label>
            <input
              id="repo-filter"
              v-model="filters.repo"
              type="text"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g. octocat/Hello-World"
            >
          </div>
          <div>
            <label for="start-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
            <input
              id="start-date"
              v-model="filters.startDate"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
          </div>
          <div>
            <label for="end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
            <input
              id="end-date"
              v-model="filters.endDate"
              type="date"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            >
          </div>
        </div>
      </div>

      <!-- Activity List -->
      <div v-if="paginatedActivities.length > 0" class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <ul role="list" class="divide-y divide-gray-200">
          <li v-for="activity in paginatedActivities" :key="activity.id" class="p-4 hover:bg-gray-50 transition duration-150 ease-in-out">
            <div class="flex items-center space-x-4">
              <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full" :class="getEventTypeColor(activity.event_type)">
                  {{ getEventTypeIcon(activity.event_type) }}
                </span>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">
                  {{ activity.event_type }}
                </p>
                <p class="text-sm text-gray-500 truncate">
                  {{ activity.repo_name }}
                </p>
                <p class="text-xs text-gray-400">
                  {{ formatDate(activity.github_created_at) }}
                </p>
              </div>
              <div v-if="activity.event_type === 'PushEvent' && activity.payload.commits && activity.payload.commits.length > 0">
                <button
                  @click="viewCommit(activity.repo_name, activity.payload.commits[0].sha)"
                  class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  View Commit
                </button>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div v-else class="text-center py-8 text-gray-500 bg-white shadow-sm border border-gray-200 rounded-lg">
        No activities found matching the current filters.
      </div>

      <!-- Pagination -->
      <div class="mt-6 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="prevPage"
            :disabled="currentPage === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Previous
          </button>
          <button
            @click="nextPage"
            :disabled="currentPage === totalPages"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Next
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">{{ (currentPage - 1) * itemsPerPage + 1 }}</span> to <span class="font-medium">{{ Math.min(currentPage * itemsPerPage, filteredActivities.length) }}</span> of <span class="font-medium">{{ filteredActivities.length }}</span> results
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <button
                @click="prevPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
              <button
                v-for="page in displayedPages"
                :key="page"
                @click="goToPage(page)"
                :class="[
                  page === currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                  'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                ]"
              >
                {{ page }}
              </button>
              <button
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
              >
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineComponent } from 'vue'
import Layout from '../Layout.vue'

export default defineComponent({
  layout: Layout,
  props: {
    activities: Array,
  },
  data() {
    return {
      filters: {
        repo: '',
        startDate: '',
        endDate: '',
      },
      currentPage: 1,
      itemsPerPage: 10,
    }
  },
  computed: {
    filteredActivities() {
      return this.activities.filter(activity => {
        const matchesRepo = !this.filters.repo || activity.repo_name.toLowerCase().includes(this.filters.repo.toLowerCase())
        const activityDate = new Date(activity.github_created_at)
        const afterStartDate = !this.filters.startDate || activityDate >= new Date(this.filters.startDate)
        const beforeEndDate = !this.filters.endDate || activityDate <= new Date(this.filters.endDate)
        return matchesRepo && afterStartDate && beforeEndDate
      })
    },
    paginatedActivities() {
      const start = (this.currentPage - 1) * this.itemsPerPage
      const end = start + this.itemsPerPage
      return this.filteredActivities.slice(start, end)
    },
    totalPages() {
      return Math.ceil(this.filteredActivities.length / this.itemsPerPage)
    },
    displayedPages() {
      const totalPages = this.totalPages
      const currentPage = this.currentPage
      const delta = 2
      const range = []
      const rangeWithDots = []
      let l

      for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
          range.push(i)
        }
      }

      for (let i of range) {
        if (l) {
          if (i - l === 2) {
            rangeWithDots.push(l + 1)
          } else if (i - l !== 1) {
            rangeWithDots.push('...')
          }
        }
        rangeWithDots.push(i)
        l = i
      }

      return rangeWithDots
    }
  },
  methods: {
    formatDate(date) {
      const options = { year: 'numeric', month: 'long', day: 'numeric' }
      return new Date(date).toLocaleDateString(undefined, options)
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++
      }
    },
    viewCommit(repoName, commitSha) {
      window.open(`/github/commit/${repoName}/${commitSha}`, '_blank')
    },
    goToPage(page) {
      if (typeof page === 'number') {
        this.currentPage = page
      }
    },
    getEventTypeColor(eventType) {
      const colors = {
        PushEvent: 'bg-green-100 text-green-800',
        PullRequestEvent: 'bg-purple-100 text-purple-800',
        IssuesEvent: 'bg-yellow-100 text-yellow-800',
        CreateEvent: 'bg-blue-100 text-blue-800',
        DeleteEvent: 'bg-red-100 text-red-800',
        // Add more event types and colors as needed
      }
      return colors[eventType] || 'bg-gray-100 text-gray-800'
    },
    getEventTypeIcon(eventType) {
      const icons = {
        PushEvent: '🔀',
        PullRequestEvent: '🔃',
        IssuesEvent: '❗',
        CreateEvent: '➕',
        DeleteEvent: '🗑️',
        // Add more event types and icons as needed
      }
      return icons[eventType] || '📅'
    },
  }
})
</script>
