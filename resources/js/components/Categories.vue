<template>
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
        <div class="row align-items-center filter-header">
            <div class="col-12 col-md-auto mt-1">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="fas fa-tags me-2"></i>Categories<span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ categoriesCount }}</span>
                </h5>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search...">
                </div>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.status" @change="fetchCategories(1)">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-auto ms-auto mt-1">
                <router-link class="btn btn-dark btn-sm p-2 shadow-sm" title="Add New Category" to="/categories/create"><i class="fa fa-plus"></i></router-link>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container overflow-y-auto">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary-linear text-light sticky-top">
                    <tr>
                        <th @click="sortByField('name', 'fetchCategories')" class="cursor-pointer ps-4">Name <i :class="getSortIcon('name')" class="ms-1"></i></th>
                        <th @click="sortByField('status', 'fetchCategories')" class="cursor-pointer">Status <i :class="getSortIcon('status')" class="ms-1"></i></th>
                        <th @click="sortByField('products_count', 'fetchCategories')" class="cursor-pointer">No. of Products <i :class="getSortIcon('products_count')" class="ms-1"></i></th>
                        <th @click="sortByField('created_at', 'fetchCategories')" class="cursor-pointer">Created At <i :class="getSortIcon('created_at')" class="ms-1"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="categoriesList.length > 0" v-for="category in categoriesList" :key="category.id" class="hover-row">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                
                                <div>
                                    <div class="fw-semibold text-primary">{{ category.name }}</div>
                                    <small class="text-muted">ID: #{{ category.id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge" :class="category.status === 'Active' ? 'bg-success' : 'bg-secondary'">{{ category.status }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-box text-muted"></i><span class="fw-bold">{{ category.products_count ?? 0 }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-calendar text-muted"></i><span>{{ formatDate(category.created_at) }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <router-link class="btn btn-sm btn-outline-primary fw-semibold rounded-1 p-2" :to="`/categories/edit/${category.id}`" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </router-link>
                                <button class="btn btn-sm btn-outline-danger fw-semibold rounded-1 p-2" type="button" @click="deleteCategory(category.id)" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="5" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa fa-folder fs-1 d-block mb-3"></i>
                                <h5>No categories found</h5>
                                <p>Try adjusting your search or filter criteria.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
                <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, categoriesCount) }} of {{ categoriesCount }}</span>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="fetchCategories(currentPage - 1)"><i class="fa fa-chevron-left fa-xs"></i></button>
                    <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm"
                        :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="fetchCategories(page)">
                        {{ page }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage"
                        @click="fetchCategories(currentPage + 1)">
                        <i class="fa fa-chevron-right fa-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { sortByField, getSortIcon } from '../utils/table'
import { formatDate } from '../utils/formatDate'
    export default {
        data() {
            return {
                categoriesList : [],
                categoriesCount: 0,
                currentPage: 1,
                lastPage: 1,
                perPage: 3,
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
                this.fetchCategories(1); 
            }
        },
        mounted() {
            this.fetchCategories();
        },
        methods: {
            fetchCategories(page = 1){
                const params = new URLSearchParams({
                    page: page,
                    ...this.filters
                });
                this.$axios.get(`/api/categories?${params.toString()}`)
                .then(res => {
                    this.categoriesList = res.data.data.data
                    this.categoriesCount = res.data.data.total
                    this.currentPage = res.data.data.current_page
                    this.lastPage = res.data.data.last_page
                    this.perPage = res.data.data.per_page
                })
                .catch(err => console.error('Failed to fetch users', err))

            },
            deleteCategory(id){
                if(!confirm('Are you sure you want to delete this category?')) return
                this.$axios.delete('/api/categories/' + id)
                .then(res => {
                    if(res.data.success === true) {
                        this.fetchCategories(1);
                    } else {
                        alert('Delete failed')
                    }
                })
                .catch(err => alert('Error deleting category'))
            },
            sortByField,
            getSortIcon,
            formatDate
        }
    }
</script>