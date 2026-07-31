<template>
  <div class="card card-vuexy card-admin-spaced">
    <!-- Header -->
    <div
      class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 pb-3 border-bottom"
    >
      <div class="d-flex align-items-center gap-3">
        <div class="badge bg-label-primary rounded-3 p-3">
          <i class="fas fa-users-gear fs-4"></i>
        </div>
        <div>
          <h5 class="mb-0 fw-bold text-heading">User Accounts Management</h5>
          <small class="text-muted fs-8">Total {{ userCount }} registered accounts</small>
        </div>
      </div>

      <router-link
        to="/users/create"
        class="btn btn-sm btn-primary rounded-pill px-3.5 shadow-primary"
      >
        <i class="fas fa-user-plus me-1"></i>Add New User
      </router-link>
    </div>

    <!-- Filter Bar -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-transparent border-end-0 text-muted"
            ><i class="fas fa-search fs-8"></i
          ></span>
          <input
            type="search"
            class="form-control form-control-sm border-start-0 ps-0"
            v-model="filters.search"
            placeholder="Search name or email..."
          />
        </div>
      </div>
      <div class="col-12 col-md-6">
        <select class="form-select form-select-sm" v-model="filters.status" @change="fetchUsers(1)">
          <option value="">All Status</option>
          <option value="Active">Active</option>
          <option value="Inactive">Inactive</option>
        </select>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
      <table class="table table-hover table-admin-sm align-middle mb-0">
        <thead>
          <tr>
            <th @click="sortByField('name', 'fetchUsers')" class="cursor-pointer">User</th>
            <th @click="sortByField('email', 'fetchUsers')" class="cursor-pointer">Email</th>
            <th @click="sortByField('status', 'fetchUsers')" class="cursor-pointer">Status</th>
            <th @click="sortByField('currency', 'fetchUsers')" class="cursor-pointer">Currency</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="usersList.length > 0" v-for="u in usersList" :key="u.id">
            <td>
              <div class="d-flex align-items-center gap-3">
                <img
                  :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(u?.name || 'User')}&background=7367f0&color=fff&size=40`"
                  class="rounded-circle border"
                  width="40"
                  height="40"
                />
                <div>
                  <div class="fw-bold text-heading fs-7">{{ u.name }}</div>
                  <small class="text-muted fs-8">ID: #{{ u.id }}</small>
                </div>
              </div>
            </td>
            <td class="fs-7"><i class="far fa-envelope me-1 text-muted"></i>{{ u.email }}</td>
            <td>
              <span
                class="badge"
                :class="u.status === 'Active' ? 'bg-label-success' : 'bg-label-danger'"
              >
                {{ u.status }}
              </span>
            </td>
            <td>
              <span class="badge bg-label-info fs-8">{{ u.currency || 'USD' }}</span>
            </td>
            <td class="text-end">
              <div class="d-inline-flex gap-1">
                <router-link
                  :to="`/users/detail/${u.id}`"
                  class="btn btn-sm btn-icon btn-label-secondary"
                  title="View"
                >
                  <i class="fas fa-eye fs-8"></i>
                </router-link>
                <router-link
                  :to="`/users/edit/${u.id}`"
                  class="btn btn-sm btn-icon btn-label-primary"
                  title="Edit"
                >
                  <i class="fas fa-pen-to-square fs-8"></i>
                </router-link>
                <button
                  type="button"
                  class="btn btn-sm btn-icon btn-label-danger"
                  @click="deleteUser(u.id)"
                  title="Delete"
                >
                  <i class="fas fa-trash-can fs-8"></i>
                </button>
              </div>
            </td>
          </tr>
          <tr v-else>
            <td colspan="5" class="text-center py-5 text-muted">
              <i class="fas fa-users fa-3x mb-3 opacity-50"></i>
              <h6>No users found</h6>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-4 border-top mt-3"
      v-if="lastPage > 1"
    >
      <small class="text-muted fs-8"
        >Showing {{ (currentPage - 1) * perPage + 1 }} to
        {{ Math.min(currentPage * perPage, userCount) }} of {{ userCount }} entries</small
      >
      <nav>
        <ul class="pagination mb-0 gap-1">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button
              class="page-link rounded-circle border-0 text-primary"
              :disabled="currentPage === 1"
              @click="fetchUsers(currentPage - 1)"
            >
              <i class="fas fa-chevron-left fs-8"></i>
            </button>
          </li>
          <li
            v-for="page in lastPage"
            :key="page"
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button
              class="page-link rounded-circle border-0 fw-bold"
              :class="page === currentPage ? 'bg-primary text-white' : 'text-primary'"
              @click="fetchUsers(page)"
            >
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === lastPage }">
            <button
              class="page-link rounded-circle border-0 text-primary"
              :disabled="currentPage === lastPage"
              @click="fetchUsers(currentPage + 1)"
            >
              <i class="fas fa-chevron-right fs-8"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import { sortByField, getSortIcon } from '../utils/table'

export default {
  name: 'Users',
  data() {
    return {
      usersList: [],
      userCount: 0,
      currentPage: 1,
      perPage: 10,
      lastPage: 1,
      filters: {
        status: '',
        sort_by: 'id',
        sort_order: 'desc',
        search: '',
      },
      searchTimeout: null,
    }
  },
  watch: {
    'filters.search'() {
      if (this.searchTimeout) clearTimeout(this.searchTimeout)
      this.searchTimeout = setTimeout(() => this.fetchUsers(1), 300)
    },
  },
  mounted() {
    this.fetchUsers()
  },
  methods: {
    sortByField,
    getSortIcon,
    fetchUsers(page = 1) {
      const params = new URLSearchParams({
        page: page,
        ...this.filters,
      })
      this.$axios
        .get(`/api/users?${params.toString()}`)
        .then((res) => {
          this.usersList = res.data.data.data
          this.userCount = res.data.data.total
          this.currentPage = res.data.data.current_page
          this.lastPage = res.data.data.last_page
          this.perPage = res.data.data.per_page
        })
        .catch((err) => console.error('Failed to fetch users', err))
    },
    deleteUser(id) {
      if (!confirm('Are you sure you want to delete this user?')) return
      this.$axios
        .delete(`/api/users/${id}`)
        .then((res) => {
          if (res.data.success) {
            this.fetchUsers(this.currentPage)
          }
        })
        .catch((err) => console.error('Failed to delete user', err))
    },
  },
}
</script>
