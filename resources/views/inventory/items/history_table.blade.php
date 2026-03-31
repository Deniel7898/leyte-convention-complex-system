<p class="mt-4 fw-500" style="color: hsl(237, 34%, 30%);">Item History Table</p>
<div class="card shadow-lg rounded-4 modern-card">
    <div class="card-body p-0">
        <div class="table-responsive rounded-4">
            <table class="table align-middle table-hover mb-0">
                <thead class="bg-light">
                    <tr class="text-uppercase text-muted small">
                        <th>#</th>
                        <th>Action</th>
                        <th>Quantity</th>
                        @if($item->type == 'non-consumable')
                            <th>Item QR Code</th>
                            <th>Holder / Borrower </th>
                        @endif
                        <th>Recorded At</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody id="history-table-body" class="text-muted small">
                    @forelse($history as $index => $record)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ ucfirst($record->action ?? '--') }}</td>
                            <td>{{ $record->quantity ?? '--' }}</td>
                            @if($item->type == 'non-consumable')
                                <td>{{ $record->inventory->qrCode->code ?? '--' }}</td>
                                <td>{{ $record->holder_or_borrower ?? '--' }}</td>
                            @endif
                            <td>{{ $record->created_at->format('M d, Y H:i') ?? '--' }}</td>
                            <td>{{ $record->creator->last_name ?? '-' }}</td>
                            <td>{{ $record->notes ?? '--' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">No history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Show/More/Less Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-2 mx-2 px-2">
                <span id="historyShowLessBtn" class="clickable small fw-500"
                    style="display:none; cursor:pointer; color: rgb(43, 45, 87);">
                    Show Less
                </span>

                <div>
                    <span id="historyShowMoreBtn" class="clickable small fw-500"
                        style="cursor:pointer; margin-right:10px; color: rgb(43, 45, 87);">
                        Show More
                    </span>
                    <span id="historyShowAllBtn" class="clickable small fw-500"
                        style="cursor:pointer; color: rgb(43, 45, 87);">
                        Show All
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const rows = document.querySelectorAll("#history-table-body tr");

        const initialVisible = 10; // show 10 rows by default
        const step = 10;           // rows to add per "Show More"
        let visibleCount = initialVisible;

        const showMoreBtn = document.getElementById("historyShowMoreBtn");
        const showLessBtn = document.getElementById("historyShowLessBtn");
        const showAllBtn = document.getElementById("historyShowAllBtn");

        function updateHistoryTable() {
            rows.forEach((row, index) => {
                row.style.display = index < visibleCount ? "" : "none";
            });

            if (rows.length <= initialVisible) {
                showMoreBtn.style.display = "none";
                showAllBtn.style.display = "none";
                showLessBtn.style.display = "none";
            } else if (visibleCount >= rows.length) {
                showMoreBtn.style.display = "none";
                showAllBtn.style.display = "none";
                showLessBtn.style.display = "inline";
            } else if (visibleCount > initialVisible) {
                showMoreBtn.style.display = "inline";
                showAllBtn.style.display = "inline";
                showLessBtn.style.display = "inline";
            } else {
                showMoreBtn.style.display = "inline";
                showAllBtn.style.display = "inline";
                showLessBtn.style.display = "none";
            }
        }

        showMoreBtn.addEventListener("click", function () {
            visibleCount = Math.min(visibleCount + step, rows.length);
            updateHistoryTable();
        });

        showAllBtn.addEventListener("click", function () {
            visibleCount = rows.length;
            updateHistoryTable();
        });

        showLessBtn.addEventListener("click", function () {
            visibleCount = initialVisible;
            updateHistoryTable();
        });

        updateHistoryTable();
    });
</script>