@if($users->count() > 0)
@foreach($users as $user)
<tr class="text-start">
    <td>{{ $loop->iteration }}</td>
    <td>{{ $user->last_name }}</td> 
    <td>{{ $user->first_name }}</td>
    <td>
        {{ $user->middle_name ? strtoupper(substr($user->middle_name,0,1)).'.' : '' }}
    </td>
    <td>{{ $user->email }}</td>
    <td>
        <span class="badge bg-{{ $user->role == 'admin' ? 'primary' : 'secondary' }}">{{ ucfirst($user->role) }}</span>
    </td>
    <td>{{ $user->phone ?? '--' }}</td>
    <td>
        {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('M d, Y') : '--' }}
    </td>
    <td>
        <span class="badge bg-success-subtle text-success">Online</span>
    </td>
    <td class="text-center">
        <div class="d-flex justify-content-center align-items-center gap-2">
            <!-- Dropdown -->
            <div class="dropdown">
                <button class="btn p-0 border-0 bg-transparent text-gray"
                    type="button"
                    id="actionMenu{{ $user->id }}"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                    title="Actions">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                    aria-labelledby="actionMenu{{ $user->id }}">

                    <!-- Edit User -->
                    <li>
                        <button type="button"
                            title="Edit User"
                            class="dropdown-item d-flex align-items-center text-gray edit"
                            data-url="{{ route('users.edit', $user->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen me-2">
                                <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"></path>
                            </svg>
                            Edit
                        </button>
                    </li>

                    <!-- Delete User -->
                    <li>
                        <button type="button"
                            title="Delete User"
                            class="dropdown-item d-flex align-items-center text-danger delete"
                            data-url="{{ route('users.destroy', $user->id) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2 me-2">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                <line x1="10" x2="10" y1="11" y2="17"></line>
                                <line x1="14" x2="14" y1="11" y2="17"></line>
                            </svg>
                            Delete
                        </button>
                    </li>

                </ul>
            </div>

        </div>
    </td>
</tr>
@endforeach
@else
<tr>
    <td colspan="7" class="text-center text-muted text-danger">{{ __('No users found.') }}</td>
</tr>
@endif