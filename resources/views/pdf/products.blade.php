<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Products Export</title>
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
        <h1>
            Products
            <span class="badge">{{ $data->count() }}</span>
        </h1>
    </div>
    
    @if($data->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="color: #636060;">Name</th>
                    <th style="color: #636060;">Price</th>
                    <th style="color: #636060;">Qty Available</th>
                    <th style="color: #636060;">Category</th>
                    <th style="color: #636060;">Currency</th>
                    <th style="color: #636060;">Status</th>
                    <th style="color: #636060;">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $product)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <div class="product-info">
                                    <div class="product-name">{{ $product->name }}</div>
                                    <div class="product-id">ID: #{{ $product->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="price">{{ number_format($product->price, 2) }}</span>
                        </td>
                        <td>
                            <span class="badge-stock {{ $product->stock > 0 ? 'badge-stock-warning' : 'badge-stock-danger' }}">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td>
                            <div class="category-name">
                                <span>{{ $product->category ? $product->category->name : 'N/A' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-currency">{{ $product->currency }}</span>
                        </td>
                        <td>
                            <span class="badge-status-{{ $product->status === 'Active' ? 'active' : 'inactive' }}">
                                {{ $product->status }}
                            </span>
                        </td>
                        <td>
                            <div class="date-cell">
                                <span>{{ \Carbon\Carbon::parse($product->created_at)->format('M d, Y') }}</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            Total Products: {{ $data->count() }}
        </div>
    @else
        <div class="no-products">
            <div class="no-products-icon">📦</div>
            <h3>No products found</h3>
            <p>Try adjusting your filters or add a new product.</p>
        </div>
    @endif
</body>
</html>
