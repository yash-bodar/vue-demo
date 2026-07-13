<template>
    <div class="card-footer bg-white border-0 p-3" v-if="lastPage > 1">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="text-muted small">Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, productCount) }} of {{ productCount }}</span>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === 1" @click="changePage(currentPage - 1)">
                    <i class="fa fa-chevron-left fa-xs"></i>
                </button>
                <button v-for="page in lastPage" :key="page" :disabled="page === currentPage" class="btn opacity-100 rounded-2 btn-sm" :class="page === currentPage ? 'bg-primary btn-outline-primary text-white' : 'btn-outline-secondary'" @click="changePage(page)">
                    {{ page }}
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-2" :disabled="currentPage === lastPage" @click="changePage(currentPage + 1)">
                    <i class="fa fa-chevron-right fa-xs"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        lastPage: {
            type: Number,
            required: true
        },
        perPage: {
            type: Number,
            required: true
        },
        productCount: {
            type: Number,
            required: true
        }
    },
    methods: {
        changePage(page) {
            if (page < 1 || page > this.lastPage) return;
            this.$emit('page-changed', page);
        }
    }
}
</script>