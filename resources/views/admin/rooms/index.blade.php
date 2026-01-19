@extends('layouts.admin')

@section('title', 'Manage Rooms')

@section('content')
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-door-open"></i> Manage Rooms</h2>
            <p class="text-muted mb-0">Total: {{ $rooms->count() }} rooms</p>
        </div>
        <a href="{{ route('admin.rooms.create') }}" class="btn btn-primary-cinebook">
            <i class="bi bi-plus-circle"></i> Add New Room
        </a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        @forelse($rooms as $room)
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"
                    style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue)); color: white;">
                    <h5 class="mb-0">
                        <i class="bi bi-door-closed"></i> {{ $room->name }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Screen Type:</strong>
                        @php
                        $screenTypeBg = match($room->screenType->name) {
                        '2D' => 'bg-primary',
                        '3D' => 'bg-success',
                        'IMAX' => 'bg-danger',
                        default => 'bg-secondary'
                        };
                        @endphp
                        <span class="badge {{ $screenTypeBg }}">
                            {{ $room->screenType->name }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <strong>Layout:</strong> {{ $room->total_rows }} rows × {{ $room->seats_per_row }} seats
                    </div>
                    <div class="mb-3">
                        <strong>Total Seats:</strong> {{ $room->seats->count() }}
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-primary-cinebook flex-fill">
                            <i class="bi bi-pencil"></i> Edit Seats
                        </a>
                        <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST"
                            onsubmit="return confirm('Are you sure? This will delete all seats in this room.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle"></i> No rooms found. Create your first room to get started!
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection