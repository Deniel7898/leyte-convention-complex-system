
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
                        <th>Recorded At</th>
                        <th>Recorded By</th>
                        <th>Notes</th>
                    </tr>
                </thead>

                <tbody id="history-table-body" class="text-muted small">
                    @forelse($history as $index => $record)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $record->action ?? '--' }}</td>
                        <td>{{ $record->quantity ?? '--' }}</td>
                        <td>{{ $record->created_at->format('M d, Y H:i') ?? '--' }}</td>
                        <td>{{ $record->creator->name ?? '-' }}</td>
                        <td>{{ $record->notes ?? '--' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-3">No history found.</td>
                    </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>
</div>