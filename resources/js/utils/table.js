function sortByField(field, fetchFunctionName = 'fetchProducts') {
    if (this.filters.sort_by === field) {
        this.filters.sort_order = this.filters.sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        this.filters.sort_by = field;
        this.filters.sort_order = 'asc';
    }
    this[fetchFunctionName](1);
}
function getSortIcon(field) {
    if (this.filters.sort_by !== field) {
        return 'fa fa-sort fa-solid opacity-50';
    }   
    return this.filters.sort_order === 'asc' ? 'fa-solid fa-sort-up' : 'fa-solid fa-sort-down';
}
export { sortByField, getSortIcon };