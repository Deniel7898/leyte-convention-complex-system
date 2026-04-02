<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Request Date</th>
            <th>Items</th>
            <th style="width:120px;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $req)
        <tr>
            <td>{{ $loop->iteration }}</td>

            <td>
                {{ \Carbon\Carbon::parse($req->request_date)->format('M d, Y') }}
            </td>

            <td>
                @foreach($req->items as $item)
                    <div class="item-name">
                        {{ $item['item_name'] }}
                        <span class="badge-qty">
                            {{ $item['quantity'] }}
                        </span>
                    </div>
                    <div class="item-sub">
                        {{ $item['unit'] ?? '' }}
                    </div>
                @endforeach
            </td>

            <td>
                <div class="action-btns">
                    <i class="bi bi-pencil-square icon-edit btnEdit" data-id="{{ $req->id }}"></i>
                    <i class="bi bi-printer icon-print btnPrint" data-id="{{ $req->id }}"></i>
                    <i class="bi bi-trash icon-delete btnDelete" data-id="{{ $req->id }}"></i>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>