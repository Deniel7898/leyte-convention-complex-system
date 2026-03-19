<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Request #{{ $purchaseRequest->id }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" />
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                max-width: 100%;
                box-shadow: none;
                border-radius: 0;
            }
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .print-container {
            background-color: white;
            padding: 40px;
            margin: 20px auto;
            max-width: 900px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2b2d57;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #2b2d57;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 35px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 6px;
        }

        .info-group {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-size: 12px;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .info-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .items-title {
            font-size: 18px;
            font-weight: 700;
            color: #2b2d57;
            margin-bottom: 20px;
            margin-top: 30px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table thead {
            background-color: #2b2d57;
            color: white;
        }

        .items-table th {
            padding: 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 14px;
            color: #333;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .items-table .item-name {
            font-weight: 600;
            color: #2b2d57;
        }

        .items-table .item-quantity {
            text-align: center;
            font-weight: 600;
        }

        .print-btn {
            background-color: #2b2d57;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            margin-right: 10px;
        }

        .print-btn:hover {
            background-color: #1f2138;
        }

        .back-btn {
            background-color: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }

        .button-section {
            margin-bottom: 20px;
        }

        .items-count {
            font-size: 13px;
            color: #666;
            font-style: italic;
        }

        .empty-description {
            color: #999;
            font-style: italic;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <h1>Purchase Request</h1>
            <p>Leyte Convention Complex – Local System</p>
        </div>

        <!-- Information Section -->
        <div class="info-section">
            <div class="info-group">
                <span class="info-label">Request ID</span>
                <span class="info-value">#{{ $purchaseRequest->id }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Request Date</span>
                <span class="info-value">{{ optional($purchaseRequest->request_date)->format('F d, Y') ?: 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Created By</span>
                <span class="info-value">{{ $purchaseRequest->creator->name ?? 'N/A' }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Created Date</span>
                <span class="info-value">{{ $purchaseRequest->created_at->format('F d, Y') }}</span>
            </div>
            <div class="info-group">
                <span class="info-label">Items Count</span>
                <span class="info-value">{{ count($purchaseRequest->items ?? []) }}</span>
            </div>
        </div>

        <!-- Items Section -->
        <div>
            <h3 class="items-title">Request Items</h3>
            @if(count($purchaseRequest->items ?? []) > 0)
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">Item Name</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 20%;">Unit</th>
                            <th style="width: 25%;">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchaseRequest->items as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td class="item-name">{{ $item['item_name'] ?? 'N/A' }}</td>
                                <td class="item-quantity">{{ $item['quantity'] ?? 'N/A' }}</td>
                                <td>{{ $item['unit'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($item['description']) && $item['description'])
                                        {{ $item['description'] }}
                                    @else
                                        <span class="empty-description">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center pr-text-muted">No items in this purchase request.</p>
            @endif
        </div>
    </div>

    <!-- Buttons -->
    <div class="button-section text-center no-print" style="margin-top: 20px;">
        <button class="print-btn" onclick="window.print()">
            Print
        </button>
        <a href="{{ route('purchase-requests.index') }}" class="back-btn">
            Back to List
        </a>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
