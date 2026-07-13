<template>
    <div class="card-header bg-gradient-primary text-light py-3 px-4 border-0 bg-primary-linear">
        <div class="row align-items-center filter-header">
            <div class="col-12 col-md-auto mt-1">
                <h5 class="mb-0 fw-bold d-flex align-items-center">
                    <i class="fas fa-box me-2"></i>Products<span class="badge bg-white color-primary ms-2 px-2 py-1 small fw-semibold">{{ productCount }}</span>
                </h5>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <div class="search-input-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="search" class="form-control p-1 ps-5" v-model="filters.search" placeholder="Search...">
                </div>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.category_id"
                    @change="fetchProducts(1)">
                    <option value="">All Categories</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}
                    </option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.currency"
                    @change="fetchProducts(1)">
                    <option value="">All Currencies</option>
                    <option value="USD">USD - US Dollar ($)</option>
                    <option value="EUR">EUR - Euro (€)</option>
                    <option value="GBP">GBP - British Pound (£)</option>
                    <option value="CAD">CAD - Canadian Dollar (C$)</option>
                    <option value="AUD">AUD - Australian Dollar (A$)</option>
                    <option value="INR">INR - Indian Rupee (₹)</option>
                    <option value="AED">AED - UAE Dirham (د.إ)</option>
                </select>
            </div>
            <div class="col-12 col-md-auto mt-1">
                <select class="form-select form-select-sm py-1 pe-5 w-auto" v-model="filters.status"
                    @change="fetchProducts(1)">
                    <option value="">All Status</option>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-auto ms-auto mt-1">
                <button class="btn btn-outline-light btn-sm p-2 shadow-sm me-2" @click="exportData('pdf')" title="Export PDF" ><i class="fa fa-file-pdf"></i></button>
                <button class="btn btn-outline-light btn-sm p-2 shadow-sm me-2" @click="exportData('csv')" title="Export CSV" ><i class="fa fa-file-csv"></i></button>
                <router-link class="btn btn-dark btn-sm p-2 shadow-sm me-2" to="/products/create" title="Add New Product" ><i class="fa fa-plus"></i></router-link>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-container overflow-y-auto">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-light bg-primary-linear sticky-top">
                    <tr>
                        <th @click="sortByField('name', 'fetchProducts')" class="cursor-pointer">Name <i :class="getSortIcon('name')" class="ms-1"></i></th>
                        <th @click="sortByField('price', 'fetchProducts')" class="cursor-pointer">Price <i :class="getSortIcon('price')" class="ms-1"></i></th>
                        <th @click="sortByField('stock', 'fetchProducts')" class="cursor-pointer">Qty Available <i :class="getSortIcon('stock')" class="ms-1"></i></th>
                        <th @click="sortByField('category_id', 'fetchProducts')" class="cursor-pointer">Category <i :class="getSortIcon('category_id')" class="ms-1"></i></th>
                        <th @click="sortByField('currency', 'fetchProducts')" class="cursor-pointer">Currency <i :class="getSortIcon('currency')" class="ms-1"></i></th>
                        <th @click="sortByField('status', 'fetchProducts')" class="cursor-pointer">Status <i :class="getSortIcon('status')" class="ms-1"></i></th>
                        <th @click="sortByField('created_at','fetchProducts')" class="cursor-pointer">Created At <i :class="getSortIcon('created_at')" class="ms-1"></i></th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="productsList.length > 0" v-for="product in productsList" :key="product.id" class="hover-row">
                        <td class="cursor-pointer" @click="$router.push(`/product/detail/${product.id}`)">
                            <div class="d-flex align-items-center gap-3">
                                <img class="rounded shadow-sm border" width="40" height="40" :src="getImageUrl(product.image)" :alt="product.name">
                                <div>
                                    <div class="fw-semibold text-primary">{{ product.name }}</div>
                                    <small class="text-muted">ID: #{{ product.id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-bold text-primary">{{ product.currency_sign }} {{ product.price }}</span>
                        </td>
                        <td>
                            <span class="badge" :class="product.stock <= 0 ? 'bg-danger' : product.stock > 0 && product.stock <= 10 ? 'bg-warning' : 'bg-success'">{{ product.stock }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2"><i class="fa fa-tag text-muted"></i><span>{{product.category?.name || 'N/A' }}</span></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ product.currency }}</span>
                        </td>
                        <td>
                            <span class="badge" :class="product.status === 'Active' ? 'bg-success' : 'bg-secondary'">{{product.status }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa fa-calendar text-muted"></i><span>{{ formatDate(product.created_at) }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <router-link class="btn btn-sm btn-outline-primary p-2 fw-semibold rounded-1" :to="`/products/edit/${product.id}`" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </router-link>
                                <button class="btn btn-sm btn-outline-danger p-2 fw-semibold rounded-1" type="button" @click="deleteProduct(product.id)" title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr v-else>
                        <td colspan="9" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fa fa-box fs-1 d-block mb-3"></i>
                                <h5>No products found</h5>
                                <p>Try adjusting your filters or add a new product.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pagination-div">
                <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, productCount) }} of {{ productCount }}</span>
                <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="fetchProducts(currentPage - 1)"><i class="fa fa-chevron-left fa-xs"></i></button>
                    <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm"
                        :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="fetchProducts(page)">
                        {{ page }}
                    </button>
                    <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage"
                        @click="fetchProducts(currentPage + 1)">
                        <i class="fa fa-chevron-right fa-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Swal from 'sweetalert2'
import { sortByField, getSortIcon } from '../utils/table'
import { getImageUrl } from '../utils/ImageUrl'
import { formatDate } from '../utils/formatDate'
import { showSwalMessage, confirmAction } from '../utils/showMessage'

export default {
    data() {
        return {
            productsList: [],
            productCount: 0,
            currentPage: 1,
            lastPage: 1,
            perPage: 3,
            categories: [],
            filters: {
                status: '',
                category_id: '',
                currency: '',
                sort_by: 'id',
                sort_order: 'desc',
                search: ''
            },
        }
    },
    watch: {
        'filters.search'(newSearch, oldSearch) {
            this.fetchProducts(1);
        }
    },
    mounted() {
        this.fetchCategories();
        this.fetchProducts();
    },
    methods: {
        exportData(type = 'pdf'){
            const params = new URLSearchParams({
                type: type,
                ...this.filters
            });
            this.$axios.get(`/api/export-products?${params.toString()}`, {
                responseType: 'blob'
            }).then(res => {
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', `products.${type}`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);
            }).catch(err => console.error('Failed to export products', err));
        },
        fetchCategories() {
            this.$axios.get('/api/get-categories').then(res => {
                this.categories = res.data.data
            }).catch(err => console.error('Failed to fetch categories', err));
        },
        fetchProducts(page = 1) {
            const params = new URLSearchParams({
                page: page,
                ...this.filters
            });
            this.$axios.get(`/api/products?${params.toString()}`)
                .then(res => {
                    this.productsList = res.data.data.data
                    this.productCount = res.data.data.total
                    this.currentPage = res.data.data.current_page
                    this.lastPage = res.data.data.last_page
                    this.perPage = res.data.data.per_page
                })
                .catch(err => console.error('Failed to fetch products', err))

        },
        deleteProduct(id) {
            confirmAction(
                'Are you sure you want to delete this product?',
                '',
                (productId) => {
                    this.$axios.delete(`/api/products/${productId}`)
                        .then(res => {
                            if (res.data.success === true) {
                                showSwalMessage('Success', 'Product Deleted')
                                this.fetchProducts(this.currentPage)
                            } else {
                                showSwalMessage('Error', 'Delete failed')
                            }
                        })
                        .catch(err => showSwalMessage('Error', 'Error deleting product'))
                },
                id
            );
        },
        sortByField,
        getSortIcon,
        getImageUrl,
        formatDate
    }
}
</script>
