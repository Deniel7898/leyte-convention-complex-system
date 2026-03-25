<!DOCTYPE html>
<html>
<head>
    <title>LCC Purchase Requests</title>

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 30px;
        }

        .print-container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        /* HEADER */
        .header-container {
            display: flex;
            align-items: center;
            gap: 20px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 15px;
        }

        .logo-container img {
            width: 70px;
        }

        .print-header h2 {
            margin: 0;
            font-size: 22px;
        }

        .print-date {
            font-size: 14px;
            color: #6b7280;
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        thead {
            background: #f1f5f9;
        }

        th, td {
            padding: 12px 10px;
            border: 1px solid #e5e7eb;
            text-align: left;
            font-size: 14px;
        }

        th {
            font-weight: 600;
        }

        td div {
            margin-bottom: 4px;
        }

        /* STATUS BADGE */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .approved {
            background: #dcfce7;
            color: #166534;
        }

        /* SIGNATURE SECTION */
        .signature-section {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
            gap: 40px;
        }

        .signature-box {
            width: 30%;
            text-align: center;
        }

        .signature-label {
            font-weight: 600;
            margin-bottom: 50px;
            text-align: left;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }

        .signature-name {
            font-weight: bold;
        }

        .signature-title {
            font-size: 13px;
            color: #555;
        }

        /* PRINT BUTTON */
        .print-btn {
            margin-top: 40px;
            padding: 10px 25px;
            border: none;
            background: #4f46e5;
            color: #fff;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .print-btn:hover {
            background: #4338ca;
        }

        /* PRINT STYLE */
        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-container {
                box-shadow: none;
                border-radius: 0;
                padding: 20px;
            }

            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="print-container">

    <!-- HEADER -->
    <div class="header-container">
        <div class="logo-container">
            <img src="{{ asset('images/logo/leyte_province_logo.jpg') }}" alt="Logo">
        </div>

        <div class="print-header">
            <h2>LCC Purchase Requests</h2>
            <div class="print-date">
                {{ now()->format('F d, Y') }}
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <table>
        <thead>
            <tr>
                <th width="5%">#</th>
                <th width="15%">Request Date</th>
                <th width="20%">Created By</th>
                <th width="25%">Items</th>
                <th width="10%">Quantity</th>
                <th width="15%">Status</th>
            </tr>
        </thead>

        <tbody>
        @forelse($approvedRequests as $request)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($request->request_date)->format('Y-m-d') }}</td>
                <td>{{ $request->creator->name ?? 'N/A' }}</td>

                <td>
                    @forelse($request->items as $item)
                        <div>{{ $item->description }}</div>
                    @empty
                        <div>-</div>
                    @endforelse
                </td>

                <td>
                    @forelse($request->items as $item)
                        <div>{{ $item->quantity }}</div>
                    @empty
                        <div>-</div>
                    @endforelse
                </td>

                <td>
                    <span class="status-badge approved">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;">No Approved Requests Found</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- SIGNATURE SECTION -->
    <div class="signature-section">
        <div class="signature-box">
            <p class="signature-label">Prepared By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">
                {{ optional($approvedRequests->first()?->creator)->name ?? 'N/A' }}
            </p>
            <p class="signature-title">Requesting Officer</p>
        </div>

        <div class="signature-box">
            <p class="signature-label">Approved By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">&nbsp;</p>
            <p class="signature-title">Purchasing Officer</p>
        </div>

        <div class="signature-box">
            <p class="signature-label">Verified By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">&nbsp;</p>
            <p class="signature-title">Department Head</p>
        </div>
    </div>

    <button onclick="window.print()" class="print-btn">
        Print
    </button>

</div>

</body>
</html>