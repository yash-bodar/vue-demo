<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Invoice #{{ $order->id }}</title>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                background-color: #f8f9fa;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                padding: 30px;
            }
            .invoice-container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            .invoice-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: rgb(34, 16, 16);
                padding: 30px;
            }
            .header-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
            }
            .header-left {
                flex: 1;
            }
            .header-right {
                flex: 1;
                text-align: right;
            }
            .invoice-title {
                font-size: 1.75rem;
                font-weight: 700;
                margin-bottom: 5px;
            }
            .invoice-number {
                font-size: 1.2rem;
                opacity: 0.9;
            }
            .company-name {
                font-size: 1.25rem;
                margin-bottom: 5px;
            }
            .company-address {
                opacity: 0.75;
                font-size: 0.9rem;
            }
            .invoice-body {
                padding: 40px;
            }
            .row {
                display: table;
                width: 100%;
                table-layout: fixed;
            }
            .col-md-6 {
                display: table-cell;
                width: 50%;
                padding: 0 10px;
                vertical-align: top;
            }
            .col-md-4 {
                display: table-cell;
                width: 33.333%;
                padding: 0 10px;
                vertical-align: top;
            }
            .mb-4 {
                margin-bottom: 20px;
            }
            .info-card {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 20px;
                margin-bottom: 20px;
            }
            .info-label {
                font-weight: 600;
                color: #666;
                font-size: 0.85rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 12px;
            }
            .info-value {
                font-size: 1.05rem;
                color: #333;
                margin-bottom: 5px;
            }
            .divider {
                height: 2px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
                margin: 30px 0;
            }
            .section-title {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 15px;
                color: #333;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th {
                border-bottom: 1px solid #e0e0e0;
                color: #000;
                padding: 12px 15px;
                text-align: left;
                font-weight: 600;
                text-transform: uppercase;
                font-size: 0.8rem;
                letter-spacing: 0.5px;
            }
            th.text-center {
                text-align: center;
            }
            th.text-end {
                text-align: right;
            }
            td {
                padding: 15px;
                border-bottom: 1px solid #e9ecef;
                vertical-align: middle;
            }
            td.text-center {
                text-align: center;
            }
            td.text-end {
                text-align: right;
            }
            tr:last-child td {
                border-bottom: none;
            }
            .product-name {
                font-weight: 600;
                color: #333;
            }
            .product-price {
                font-weight: 600;
                color: #667eea;
            }
            .text-muted {
                color: #6c757d;
            }
            .total-section {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 20px;
                margin-top: 20px;
            }
            .total-label {
                font-weight: 600;
                color: #666;
            }
            .total-amount {
                font-size: 1.5rem;
                font-weight: 700;
                color: #667eea;
            }
            .status-badge {
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
                text-transform: uppercase;
                display: inline-block;
            }
            .status-paid {
                background: #d4edda;
                color: #155724;
            }
            .status-pending {
                background: #fff3cd;
                color: #856404;
            }
            .status-completed {
                background: #d1ecf1;
                color: #0c5460;
            }
            .status-processing {
                background: #cce5ff;
                color: #004085;
            }
            .invoice-footer {
                background: #f8f9fa;
                padding: 20px 40px;
                text-align: center;
                color: #666;
                font-size: 0.9rem;
            }
            .invoice-footer p {
                margin-bottom: 5px;
            }
            @media (max-width: 768px) {
                .col-md-6, .col-md-4 {
                    flex: 0 0 100%;
                    max-width: 100%;
                }
                .header-row {
                    flex-direction: column;
                    text-align: center;
                }
                .header-right {
                    text-align: center;
                    margin-top: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="invoice-container">
            <!-- Header -->
            <div class="invoice-header">
                <div class="header-row">
                    <div class="header-left">
                        <h3 class="invoice-title">
                            <i class="fas fa-file-invoice" style="margin-right: 8px;"></i>INVOICE
                        </h3>
                        <p class="invoice-number" style="margin: 0;">#{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="header-right">
                        <h4 class="company-name" style="margin: 0 0 5px 0;">Your Company Name</h4>
                        <p class="company-address" style="margin: 0;">123 Business Street<br>City, State 12345</p>
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="invoice-body">
                <!-- Order Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6 class="info-label"><i class="fas fa-user" style="margin-right: 5px;"></i>Bill To</h6>
                            <div class="info-value">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="info-value">{{ $order->user->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card">
                            <h6 class="info-label"><i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>Ship To</h6>
                            @if($order->address)
                                <div class="info-value">{{ $order->address->full_name ?? 'N/A' }}</div>
                                <div class="info-value">{{ $order->address->address_line1 ?? 'N/A' }}</div>
                                <div class="info-value">{{ $order->address->address_line2 ?? 'N/A' }}</div>
                                <div class="info-value">{{ $order->address->city ?? '' }}, {{ $order->address->state ?? '' }} {{ $order->address->postal_code ?? '' }}</div>
                                <div class="info-value">{{ $order->address->country ?? '' }}</div>
                            @else
                                <div class="info-value">No address provided</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="info-card">
                            <h6 class="info-label">Order Date</h6>
                            <div class="info-value">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <h6 class="info-label">Payment Status</h6>
                            <div class="info-value">
                                <span class="status-badge {{ $order->payment_status === 'paid' ? 'status-paid' : 'status-pending' }}">
                                    {{ $order->payment_status }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card">
                            <h6 class="info-label">Order Status</h6>
                            <div class="info-value">
                                <span class="status-badge {{ $order->status === 'Completed' ? 'status-completed' : ($order->status === 'Processing' ? 'status-processing' : 'status-pending') }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Products Table -->
                <h5 class="section-title"><i class="fas fa-shopping-bag" style="margin-right: 8px;"></i>Order Items</h5>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Currency</th>
                            <th class="text-end">Price</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($order->items) && is_array($order->items))
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="product-name">{{ $item['product_name'] ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                                    <td class="text-end product-price">{{ $order->currency }}</td>
                                    <td class="text-end product-price">{{ number_format($item['price'] ?? 0, 2) }}</td>
                                    <td class="text-end product-price">{{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center text-muted">No items found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Total Section -->
                <div class="total-section">
                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin-bottom: 5px;"><strong>Payment Method:</strong> Credit Card</p>
                            <p style="margin: 0; color: #6c757d;">Transaction ID: {{ $order->payment_intent_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span class="total-label">Total Amount:</span>
                                <span class="total-amount">{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="invoice-footer">
                <p style="margin-bottom: 3px;">Thank you for your business!</p>
                <p style="margin: 0;">If you have any questions, please contact us at support@laravue.com</p>
            </div>
        </div>
    </body>
</html>