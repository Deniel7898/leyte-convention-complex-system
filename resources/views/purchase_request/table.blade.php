<table id="pr_table" class="lcc-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($requests as $pr)
            <tr>
                <td>{{ $pr->id }}</td>
                <td>{{ $pr->request_date }}</td>
                <td>
                  <span class="badge-status status-{{ strtolower($pr->status) }}">
                      {{ ucfirst($pr->status) }}
                  </span>
                </td>
                <td>{{ $pr->creator->name ?? 'Unknown' }}</td>
                <td>
                    <a href="{{ route('purchase_request.show', $pr->id) }}"
                     class="btn-modern btn-info-modern">
                      View
                    </a>


                    @if($pr->status == 'pending')
                        <form method="POST"
                              action="{{ route('purchase_request.updateStatus', [$pr->id, 'approved']) }}"
                              style="display:inline;">
                            @csrf
                            <button class="btn-modern btn-success-modern">
                                Approve
                            </button>

                        </form>

                        <form method="POST"
                              action="{{ route('purchase_request.updateStatus', [$pr->id, 'rejected']) }}"
                              style="display:inline;">
                            @csrf
                            <button class="btn-modern btn-warning-modern">
                                Reject
                            </button>
                        </form>
                    @endif
                  
                    @if(in_array($pr->status, ['pending', 'rejected']))
                        <button type="button"
                                class="btn-modern btn-danger-modern delete-pr"
                                data-url="{{ route('purchase_request.destroy', $pr->id) }}">
                            Delete
                        </button>
                    @endif

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">
                    No purchase requests found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
