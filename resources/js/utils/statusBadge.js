export function getOrderStatusBadgeClass(status) {
  const statusLower = status?.toLowerCase() || ''
  switch (statusLower) {
    case 'completed':
    case 'delivered':
    case 'paid':
      return 'bg-success'
    case 'processing':
    case 'pending':
      return 'bg-warning text-dark'
    case 'cancelled':
      return 'bg-danger'
    case 'shipped':
    case 'refunded':
      return 'bg-info text-dark'
    default:
      return 'bg-secondary'
  }
}
