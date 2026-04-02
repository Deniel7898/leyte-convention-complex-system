<!DOCTYPE html>
<html>
<head>
    <title>Purchase Request</title>

    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 40px;
            color: #1f2937;
        }

        /* ===== HEADER ===== */
        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .title {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            letter-spacing: 1px;
        }

        .subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-top: 4px;
        }

        /* ===== INFO ===== */
        .info {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .info strong {
            color: #374151;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        thead {
            background: #2d2f5b;
            color: #fff;
        }

        th {
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            width: 45%;
            text-align: center;
        }

        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 13px;
            color: #374151;
        }

        /* ===== PRINT BUTTON ===== */
        .print-btn {
            margin-top: 30px;
            padding: 10px 20px;
            background: #2d2f5b;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* ===== PRINT MODE ===== */
        @media print {
            .print-btn {
                display: none;
            }

            body {
                padding: 10px;
            }
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <div class="title">PURCHASE REQUEST</div>
    <div class="subtitle">Leyte Convention Complex</div>
</div>

<!-- INFO -->
<div class="info">
    <strong>Date:</strong> {{ $request->request_date->format('M d, Y') }}
</div>

<!-- TABLE -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        @foreach($request->items as $index => $item)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $item['item_name'] }}</td>
            <td>{{ $item['quantity'] }}</td>
            <td>{{ $item['unit'] ?? '' }}</td>
            <td>{{ $item['description'] ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- FOOTER -->
<div class="footer">
    <div class="signature">
        <div class="signature-line">Requested By</div>
    </div>

    <div class="signature">
        <div class="signature-line">Approved By</div>
    </div>
</div>

<!-- PRINT BUTTON -->
<button class="print-btn" onclick="window.print()">Print</button>

</body>
</html>