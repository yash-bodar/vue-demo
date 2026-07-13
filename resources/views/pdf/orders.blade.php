<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background-color: #e9e9e9;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #667eea;
            padding: 5px 8px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .badge {
            background-color: white;
            color: #667eea;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: bold;
            padding: 12px 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        tr:hover {
            background-color: #e9ecef;
        }
        .product-name {
            font-weight: 600;
            color: #667eea;
            font-size: 12px;
        }
        .product-id {
            color: #6c757d;
            font-size: 10px;
        }
        .price {
            font-weight: bold;
            color: #667eea;
        }
        .badge-stock {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-stock-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-stock-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-currency {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-status-active {
            background-color: #28a745;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-status-inactive {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-status {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-payment-status {
            background-color: #6c757d;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
        }
        .bg-success {
            background-color: #28a745;
            color: white;
        }
        .bg-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .bg-danger {
            background-color: #dc3545;
            color: white;
        }
        .bg-info {
            background-color: #17a2b8;
            color: white;
        }
        .bg-secondary {
            background-color: #6c757d;
            color: white;
        }
        .category-name {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .category-icon {
            color: #6c757d;
        }
        .date-cell {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #6c757d;
        }
        .date-icon {
            color: #6c757d;
        }
        .product-image {
            width: 40px;
            height: 40px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            margin-right: 10px;
            vertical-align: middle;
        }
        .product-cell {
            display: flex;
            align-items: center;
        }
        .product-info {
            display: flex;
            flex-direction: column;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 11px;
        }
        .no-products {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        .no-products-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders <span class="badge">{{ $data->count() }}</span></h1>
    </div>
    
    @if($data->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="color: #636060;">User</th>
                    <th style="color: #636060;">No. of Products</th>
                    <th style="color: #636060;">Currency</th>
                    <th style="color: #636060;">Total</th>
                    <th style="color: #636060;">Status</th>
                    <th style="color: #636060;">Payment Status</th>
                    <th style="color: #636060;">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $order)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <div class="product-info">
                                    <div class="product-name">{{ $order->user->name ?? 'N/A' }}</div>
                                    <div class="product-id">ID: #{{ $order->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="price">{{ count($order->items ?? []) }}</span>
                        </td>
                        <td>
                            <span class="badge-currency">{{ $order->currency }}</span>
                        </td>
                        <td>
                            <span class="price">{{ number_format($order->total_amount, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge-status {{ in_array(strtolower($order->status), ['completed', 'delivered', 'paid']) ? 'bg-success' : (in_array(strtolower($order->status), ['processing', 'pending']) ? 'bg-warning' : (in_array(strtolower($order->status), ['cancelled']) ? 'bg-danger' : (in_array(strtolower($order->status), ['shipped', 'refunded']) ? 'bg-info' : 'bg-secondary'))) }}">{{ $order->status }}</span>
                        </td>
                        <td>
                            <span class="badge-payment-status {{ in_array(strtolower($order->payment_status ?? ''), ['completed', 'delivered', 'paid']) ? 'bg-success' : (in_array(strtolower($order->payment_status ?? ''), ['processing', 'pending']) ? 'bg-warning' : (in_array(strtolower($order->payment_status ?? ''), ['cancelled']) ? 'bg-danger' : (in_array(strtolower($order->payment_status ?? ''), ['shipped', 'refunded']) ? 'bg-info' : 'bg-secondary'))) }}">{{ $order->payment_status ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="date-cell">
                                <span>{{ \Carbon\Carbon::parse($order->created_at)->format('Y-m-d') }}</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            Total Orders: {{ $data->count() }}
        </div>
    @else
        <div class="no-products">
            <div class="no-products-icon">📦</div>
            <h3>No orders found</h3>
        </div>
    @endif
</body>
</html>
