<table id="pr_table" class="lcc-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Request Date</th>
            <th>Status</th>
            <th>Created By</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>

    <tbody>
        @forelse($requests as $pr)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $pr->request_date }}</td>
                <td>
                    <span class="badge-status status-{{ strtolower($pr->status) }}">
                        {{ ucfirst($pr->status) }}
                    </span>
                </td>
                <td>{{ $pr->creator->name ?? 'Unknown' }}</td>

                <td class="text-center pr-actions">

                    <!-- VIEW -->
                    <a href="{{ route('purchase_request.show', $pr->id) }}"
                       title="View Request"
                       class="icon-btn text-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                             viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </a>

                    @if($pr->status == 'pending')

                        <!-- APPROVE -->
                        <form method="POST"
                              action="{{ route('purchase_request.updateStatus', [$pr->id, 'approved']) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    title="Approve"
                                    class="icon-btn text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </button>
                        </form>

                        <!-- REJECT -->
                        <form method="POST"
                              action="{{ route('purchase_request.updateStatus', [$pr->id, 'rejected']) }}"
                              style="display:inline;">
                            @csrf
                            <button type="submit"
                                    title="Reject"
                                    class="icon-btn text-warning">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                        </form>

                    @endif

                    @if(in_array($pr->status, ['pending', 'rejected']))
                        <!-- DELETE -->
                        <button type="button"
                                title="Delete"
                                class="icon-btn text-danger delete-pr"
                                data-url="{{ route('purchase_request.destroy', $pr->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" x2="10" y1="11" y2="17"></line>
                                <line x1="14" x2="14" y1="11" y2="17"></line>
                            </svg>
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