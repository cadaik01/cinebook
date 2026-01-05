@extends('layouts.admin')

@section('title', 'Create Room')

@section('content')
<div class="admin-header">
    <div class="d-flex justify-content-between align-items-center">
        <h2><i class="bi bi-door-open"></i> Create New Room</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Rooms
        </a>
    </div>
</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.rooms.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Room Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" value="{{ old('name') }}" placeholder="e.g., Room 1, Cinema Hall A"
                                required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="screen_type_id" class="form-label">Screen Type</label>
                            <select class="form-select @error('screen_type_id') is-invalid @enderror"
                                id="screen_type_id" name="screen_type_id" required>
                                <option value="">Select Screen Type</option>
                                @foreach($screenTypes as $screenType)
                                <option value="{{ $screenType->id }}"
                                    {{ old('screen_type_id') == $screenType->id ? 'selected' : '' }}>
                                    {{ $screenType->name }} (+{{ number_format($screenType->price) }}₫)
                                </option>
                                @endforeach
                            </select>
                            @error('screen_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="total_rows" class="form-label">Number of Rows</label>
                                <input type="number" class="form-control @error('total_rows') is-invalid @enderror"
                                    id="total_rows" name="total_rows" value="{{ old('total_rows', 8) }}" min="1"
                                    max="20" required>
                                <small class="text-muted">Rows will be labeled A, B, C... (Max: 20)</small>
                                @error('total_rows')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="seats_per_row" class="form-label">Seats per Row</label>
                                <input type="number" class="form-control @error('seats_per_row') is-invalid @enderror"
                                    id="seats_per_row" name="seats_per_row" value="{{ old('seats_per_row', 10) }}"
                                    min="1" max="30" required>
                                <small class="text-muted">Number of seats in each row (Max: 30)</small>
                                @error('seats_per_row')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Note:</strong> Seats will be automatically generated with the pattern A1, A2, B1,
                            B2, etc.
                            All seats will be set as "Standard" type by default. You can change seat types after
                            creation.
                        </div>

                        <button type="submit" class="btn btn-primary-cinebook">
                            <i class="bi bi-check-circle"></i> Create Room & Generate Seats
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm" style="position: sticky; top: 20px;">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Preview</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Seat Layout Preview</h6>
                    <div id="preview" class="text-center">
                        <div class="mb-3 p-2"
                            style="background-color: var(--prussian-blue); color: white; border-radius: 5px;">
                            <small>SCREEN</small>
                        </div>
                        <div id="seat-grid">
                            <small class="text-muted">Enter rows and seats to preview</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small><strong>Total Seats:</strong> <span id="total-seats">0</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowsInput = document.getElementById('total_rows');
    const seatsInput = document.getElementById('seats_per_row');
    const preview = document.getElementById('seat-grid');
    const totalSeats = document.getElementById('total-seats');

    function updatePreview() {
        const rows = parseInt(rowsInput.value) || 0;
        const seatsPerRow = parseInt(seatsInput.value) || 0;

        if (rows > 0 && seatsPerRow > 0 && rows <= 20 && seatsPerRow <= 30) {
            const rowLabels = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P',
                'Q', 'R', 'S', 'T'
            ];
            let html = '';

            for (let i = 0; i < rows; i++) {
                html += `<div class="mb-2"><small class="text-muted">${rowLabels[i]}</small> `;
                for (let j = 1; j <= seatsPerRow; j++) {
                    html +=
                        `<span style="display: inline-block; width: 20px; height: 20px; background-color: var(--deep-teal); border-radius: 3px; margin: 2px;"></span>`;
                }
                html += '</div>';
            }

            preview.innerHTML = html;
            totalSeats.textContent = rows * seatsPerRow;
        } else {
            preview.innerHTML = '<small class="text-muted">Enter valid values</small>';
            totalSeats.textContent = '0';
        }
    }

    rowsInput.addEventListener('input', updatePreview);
    seatsInput.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
@endsection