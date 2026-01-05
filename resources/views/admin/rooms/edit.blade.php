@extends('layouts.admin')

@section('title', 'Edit Room - ' . $room->name)

@section('content')
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-pencil-square"></i> Edit Room: {{ $room->name }}</h2>
            <p class="text-muted mb-0">Configure seat types for each seat</p>
        </div>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Room Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.update', $room) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Room Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="{{ old('name', $room->name) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="screen_type_id" class="form-label">Screen Type</label>
                                <select class="form-select" id="screen_type_id" name="screen_type_id" required>
                                    @foreach($screenTypes as $screenType)
                                    <option value="{{ $screenType->id }}"
                                        {{ $room->screen_type_id == $screenType->id ? 'selected' : '' }}>
                                        {{ $screenType->name }} (+{{ number_format($screenType->price) }}₫)
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Layout:</strong> {{ $room->total_rows }} rows × {{ $room->seats_per_row }} seats
                            (Total: {{ $room->seats->count() }} seats)
                        </div>

                        <button type="submit" class="btn btn-primary-cinebook">
                            <i class="bi bi-check-circle"></i> Update Room Info
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header" style="background-color: var(--burnt-peach); color: white;">
                    <h5 class="mb-0"><i class="bi bi-grid-3x3"></i> Seat Layout & Types</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.rooms.update-seats', $room) }}" id="seatForm">
                        @csrf

                        <div class="mb-3 text-center">
                            <div class="screen mb-4"
                                style="background-color: var(--prussian-blue); color: white; padding: 10px; border-radius: 5px;">
                                <i class="bi bi-display"></i> SCREEN
                            </div>
                        </div>

                        <div class="seat-layout">
                            @foreach($seatsByRow as $rowLabel => $seats)
                            <div class="row mb-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 30px; font-weight: bold; color: var(--deep-teal);">
                                            {{ $rowLabel }}
                                        </div>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($seats->sortBy('seat_number') as $seat)
                                            <div class="seat-item" data-seat-id="{{ $seat->id }}">
                                                <input type="hidden"
                                                    name="seats[{{ $loop->parent->index * 100 + $loop->index }}][id]"
                                                    value="{{ $seat->id }}">
                                                <select
                                                    name="seats[{{ $loop->parent->index * 100 + $loop->index }}][seat_type_id]"
                                                    class="seat-select" data-seat-code="{{ $seat->seat_code }}"
                                                    style="display: none;">
                                                    @foreach($seatTypes as $seatType)
                                                    <option value="{{ $seatType->id }}"
                                                        {{ $seat->seat_type_id == $seatType->id ? 'selected' : '' }}
                                                        data-type="{{ $seatType->name }}">
                                                        {{ ucfirst($seatType->name) }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <div class="seat-visual {{ $seat->seatType->name }}"
                                                    onclick="toggleSeatType(this)"
                                                    title="{{ $seat->seat_code }} - Click to change type">
                                                    <small>{{ $seat->seat_number }}</small>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary-cinebook btn-lg">
                                <i class="bi bi-save"></i> Save Seat Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm" style="position: sticky; top: 20px;">
                <div class="card-header" style="background-color: var(--deep-space-blue); color: white;">
                    <h5 class="mb-0"><i class="bi bi-palette"></i> Seat Types Legend</h5>
                </div>
                <div class="card-body">
                    @foreach($seatTypes as $seatType)
                    <div class="mb-3 p-3 border rounded" style="cursor: pointer;"
                        onclick="selectAllSeatsOfType('{{ $seatType->name }}')">
                        <div class="d-flex align-items-center gap-3">
                            <div class="seat-visual {{ $seatType->name }}" style="width: 40px; height: 40px;">
                                <i class="bi bi-check"></i>
                            </div>
                            <div>
                                <strong>{{ ucfirst($seatType->name) }}</strong><br>
                                <small class="text-muted">{{ number_format($seatType->base_price) }}₫</small><br>
                                <small class="text-muted">{{ $seatType->description }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <hr>

                    <h6 class="mb-3">Quick Actions:</h6>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="selectRowSeats('A', 'vip')">
                            Make Row A → VIP
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            onclick="selectLastRow('couple')">
                            Make Last Row → Couple
                        </button>
                    </div>

                    <div class="mt-4 alert alert-warning">
                        <small><strong>Tip:</strong> Click on individual seats to cycle through seat types,
                            or use the quick actions above.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.seat-layout {
    max-width: 100%;
    overflow-x: auto;
}

.seat-item {
    display: inline-block;
    position: relative;
}

.seat-visual {
    width: 35px;
    height: 35px;
    border: 2px solid;
    border-radius: 5px;
    display: flex;
    align-items-center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 0.75rem;
    font-weight: bold;
}

.seat-visual:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
}

.seat-visual.standard {
    background-color: white;
    border-color: var(--deep-teal);
    color: var(--deep-teal);
}

.seat-visual.vip {
    background-color: var(--burnt-peach);
    border-color: var(--burnt-peach);
    color: white;
}

.seat-visual.couple {
    background-color: var(--tan);
    border-color: var(--tan);
    color: var(--prussian-blue);
    width: 50px;
}
</style>
@endpush

@push('scripts')
<script>
const seatTypes = ['standard', 'vip', 'couple'];
let currentTypeIndex = {};

function toggleSeatType(element) {
    const seatItem = element.closest('.seat-item');
    const select = seatItem.querySelector('.seat-select');
    const currentValue = select.value;
    const options = Array.from(select.options);

    // Find current index
    const currentIndex = options.findIndex(opt => opt.value === currentValue);

    // Get next index
    const nextIndex = (currentIndex + 1) % options.length;
    const nextOption = options[nextIndex];

    // Update select and visual
    select.value = nextOption.value;
    const newType = nextOption.dataset.type;

    // Update visual class
    element.className = 'seat-visual ' + newType;
}

function selectAllSeatsOfType(typeName) {
    document.querySelectorAll('.seat-visual').forEach(visual => {
        const seatItem = visual.closest('.seat-item');
        const select = seatItem.querySelector('.seat-select');
        const option = Array.from(select.options).find(opt => opt.dataset.type === typeName);
        if (option) {
            select.value = option.value;
            visual.className = 'seat-visual ' + typeName;
        }
    });
}

function selectRowSeats(row, typeName) {
    document.querySelectorAll('.seat-select').forEach(select => {
        const seatCode = select.dataset.seatCode;
        if (seatCode.startsWith(row)) {
            const option = Array.from(select.options).find(opt => opt.dataset.type === typeName);
            if (option) {
                select.value = option.value;
                const visual = select.closest('.seat-item').querySelector('.seat-visual');
                visual.className = 'seat-visual ' + typeName;
            }
        }
    });
}

function selectLastRow(typeName) {
    const rows = document.querySelectorAll('.row.mb-3');
    const lastRow = rows[rows.length - 1];
    if (lastRow) {
        lastRow.querySelectorAll('.seat-select').forEach(select => {
            const option = Array.from(select.options).find(opt => opt.dataset.type === typeName);
            if (option) {
                select.value = option.value;
                const visual = select.closest('.seat-item').querySelector('.seat-visual');
                visual.className = 'seat-visual ' + typeName;
            }
        });
    }
}
</script>
@endpush
@endsection