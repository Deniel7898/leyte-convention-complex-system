<!DOCTYPE html>
<html>
<head>
    <title>Approved Purchase Requests</title>
    <link rel="stylesheet" href="{{ asset('css/custom/print_pr.css') }}">
</head>
<body>

<div class="print-container">

    <div class="print-card">
        <div class="print-header">
            <h2>Approved Purchase Requests</h2>
            <span class="print-date">{{ now()->format('F d, Y') }}</span>
        </div>

<table class="modern-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Request Date</th>
            <th>Created By</th>
            <th>Items</th>
            <th>Quantity</th>
            <th>Status</th>
        </tr>
    </thead>

        <tbody>
            @foreach($approvedRequests as $request)
            <tr>
                <td>{{ $request->id }}</td>
                <td>{{ $request->request_date }}</td>
                <td>{{ $request->creator->name ?? 'N/A' }}</td>

                <!-- ITEMS -->
                <td>
                    @forelse($request->items as $item)
                        <div>{{ $item->description }}</div>
                    @empty
                        <div>No items</div>
                    @endforelse
                </td>

                <!-- QUANTITY -->
                <td>
                    @forelse($request->items as $item)
                        <div>{{ $item->quantity }}</div>
                    @empty
                        <div>-</div>
                    @endforelse
                </td>

                <!-- STATUS -->
                <td>
                    <span class="status-badge approved">
                        {{ ucfirst($request->status) }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Signature Section BELOW TABLE -->
    <div class="signature-section">

        <div class="signature-box">
            <p class="signature-label">Prepared By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">
                {{ Auth::user()->name ?? 'System User' }}
            </p>
            <p class="signature-title">Requesting Officer</p>
        </div>

        <div class="signature-box">
            <p class="signature-label">Approved By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">________________________</p>
            <p class="signature-title">Purchasing Officer</p>
        </div>

        <div class="signature-box">
            <p class="signature-label">Verified By:</p>
            <div class="signature-line"></div>
            <p class="signature-name">________________________</p>
            <p class="signature-title">Department Head</p>
        </div>

    </div>

        <button onclick="window.print()" class="print-btn">
            Print
        </button>


    </div>

</div>

</body>
</html>
