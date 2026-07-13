<template>
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
        <div class="row align-items-center filter-header">
            <div class="col-12 col-md-auto mt-1">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="fa fa-users me-2"></i>Users<span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ userCount }}</span>
                </h5>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search...">
                </div>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.status" @change="fetchUsers(1)">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-auto ms-auto mt-1">
                <router-link class="btn btn-dark btn-sm p-2 shadow-sm" title="Add New User" to="/users/create"><i class="fa fa-plus"></i></router-link>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container overflow-y-auto">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary-linear text-light sticky-top">
                    <tr>
                        <th @click="sortByField('name', 'fetchUsers')" class="cursor-pointer">Name <i :class="getSortIcon('name')" class="ms-1"></i></th>
                        <th @click="sortByField('email', 'fetchUsers')" class="cursor-pointer">Email <i :class="getSortIcon('email')" class="ms-1"></i></th>
                        <th @click="sortByField('status', 'fetchUsers')" class="cursor-pointer">Status <i :class="getSortIcon('status')" class="ms-1"></i></th>
                        <th @click="sortByField('currency', 'fetchUsers')" class="cursor-pointer">Currency <i :class="getSortIcon('currency')" class="ms-1"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="usersList.length > 0" v-for="user in usersList" :key="user.id" class="hover-row">
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img :src="`https://ui-avatars.com/api/?name=${user?.name || 'User'}&background=0d6efd&color=fff&size=40`" class="rounded-circle">
                                <div>
                                    <div class="fw-semibold text-primary">{{ user.name }}</div>
                                    <small class="text-muted">ID: #{{ user.id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-envelope text-muted"></i>
                                <span>{{ user.email }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge" :class="user.status === 'Active' ? 'bg-success' : 'bg-secondary'">
                                {{ user.status }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ user.currency }}</span>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <router-link class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" :to="`/users/detail/${user.id}`" title="View Details">
                                    <i class="fa fa-eye"></i> 
                                </router-link>
                                <router-link class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" :to="`/users/edit/${user.id}`" title="Edit">
                                    <i class="fa fa-pencil"></i> 
                                </router-link>
                                <button class="btn btn-sm btn-outline-danger p-2 fw-semibold rounded-1" type="button" @click="deleteUser(user.id)" title="Delete">
                                    <i class="fa fa-trash"></i> 
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa fa-people fs-1 d-block mb-3"></i>
                                <h5>No users found</h5>
                                <p>Try adjusting your search or filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
                <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, userCount) }} of {{ userCount }}</span>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="fetchUsers(currentPage - 1)"><i class="fa fa-chevron-left fa-xs"></i></button>
                    <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm"
                        :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="fetchUsers(page)">
                        {{ page }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage"
                        @click="fetchUsers(currentPage + 1)">
                        <i class="fa fa-chevron-right fa-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { sortByField, getSortIcon } from '../utils/table'

export default {
    data(){
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
                search: ''
            },

        }
    },
    watch: {
        'filters.search'(newSearch, oldSearch) {
            this.fetchUsers(1);
        }
    },
    mounted(){
        this.fetchUsers()
    },
    methods: {
        fetchUsers(page = 1) {
            const params = new URLSearchParams({
                page: page,
                ...this.filters
            });
            this.$axios.get(`/api/users?${params.toString()}`)
                .then(res => {
                    this.usersList = res.data.data.data
                    this.userCount = res.data.data.total
                    this.currentPage = res.data.data.current_page
                    this.lastPage = res.data.data.last_page
                    this.perPage = res.data.data.per_page
                })
                .catch(err => console.error('Failed to fetch users', err))
        },
        deleteUser(id) {
            if(!confirm('Are you sure you want to delete this user?')) return
            this.$axios.delete('/api/users/' + id)
                .then(res => {
                    if(res.data.success === true) {
                        this.usersList = this.usersList.filter(user => user.id !== id);
                        this.userCount--;

                    } else {
                        alert('Delete failed')
                    }
                })
                .catch(err => alert('Error deleting user'))
        },
        sortByField,
        getSortIcon
    }
}
</script>
