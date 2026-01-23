@extends('layouts.admin')

@section('title', 'Edit Room & Seats')

@push('styles')
<style>
/* ==================== Admin Seat Map Styles (Cinema Style) ==================== */
.admin-room-container {
    max-width: 1400px;
    margin: 0 auto;
}

.room-info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
}

.room-info-card h4 {
    margin: 0 0 20px 0;
    color: #1a1a2e;
    font-weight: 600;
}

/* ========== Seat Map Section ========== */
.seat-map-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
}

.seat-map-section h4 {
    margin: 0 0 8px 0;
    color: #1a1a2e;
    font-weight: 600;
}

.seat-map-section .section-subtitle {
    color: #6c757d;
    font-size: 0.9em;
    margin-bottom: 20px;
}

/* Legend */
.seat-legend {
    background: white;
    padding: 20px 30px;
    border-radius: 10px;
    margin-bottom: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.seat-legend h4 {
    font-size: 1.2em;
    margin: 0 0 15px 0;
    color: #1a1a1a;
    font-weight: 600;
}

.legend-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    align-items: center;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1em;
    color: #333;
}

.legend-color {
    width: 30px;
    height: 30px;
    border-radius: 5px;
    display: inline-block;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.legend-color.standard { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
.legend-color.vip { background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); }
.legend-color.couple { background: linear-gradient(135deg, #e84393 0%, #d63384 100%); }
.legend-color.selected { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }

/* Cinema Screen */
.cinema-screen {
    text-align: center;
    margin-bottom: 40px;
}

.screen-label {
    display: inline-block;
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    color: white;
    padding: 15px 60px;
    border-radius: 10px 10px 100px 100px;
    font-size: 1.1em;
    font-weight: 600;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    letter-spacing: 2px;
    text-transform: uppercase;
}

/* Seat Map Container */
.seat-map-wrapper {
    background: #f8f9fa;
    padding: 40px 20px;
    border-radius: 15px;
    margin-bottom: 30px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
}

#adminSeatMap {
    max-width: 100%;
    overflow-x: auto;
}

/* Seat Rows */
.seat-row {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    gap: 5px;
}

.seat-row-label {
    font-weight: 700;
    font-size: 1.1em;
    color: #2c3e50;
    min-width: 80px;
    text-align: right;
    padding-right: 15px;
    letter-spacing: 1px;
}

.seats-container {
    display: flex;
    gap: 5px;
    flex-wrap: nowrap;
}

/* Seat Buttons - Cinema Style */
.seat-btn {
    width: 45px;
    height: 45px;
    margin: 0;
    font-weight: bold;
    font-size: 0.9em;
    border: 2px solid transparent;
    border-radius: 8px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.seat-btn:hover:not(:disabled) {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.seat-btn:active:not(:disabled) {
    transform: translateY(-1px) scale(1.02);
}

/* Seat type colors */
.seat-btn[data-seat-type="1"],
.seat-btn.seat-type-1 {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
}

.seat-btn[data-seat-type="2"],
.seat-btn.seat-type-2 {
    background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
}

.seat-btn[data-seat-type="3"],
.seat-btn.seat-type-3 {
    background: linear-gradient(135deg, #e84393 0%, #d63384 100%);
}

/* Couple seat button: wider */
.seat-btn.couple {
    width: 95px;
    min-width: 90px;
    max-width: 120px;
    text-align: center;
    font-size: 1em;
    letter-spacing: 1px;
}

/* Selected state */
.seat-btn.selected {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%) !important;
    border-color: #2980b9 !important;
    box-shadow: 0 4px 8px rgba(52, 152, 219, 0.4);
}

/* ========== Sidebar (Offcanvas) ========== */
.seat-sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.3);
    z-index: 1040;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.seat-sidebar-overlay.show {
    opacity: 1;
    visibility: visible;
}

.seat-sidebar {
    position: fixed;
    top: 0;
    right: -400px;
    width: 380px;
    max-width: 90vw;
    height: 100vh;
    background: white;
    box-shadow: -4px 0 20px rgba(0,0,0,0.15);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.seat-sidebar.show {
    right: 0;
}

.sidebar-header {
    padding: 20px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.sidebar-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1em;
}

.sidebar-header .close-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.2s;
}

.sidebar-header .close-btn:hover {
    background: rgba(255,255,255,0.3);
}

.sidebar-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}

.sidebar-footer {
    padding: 16px 24px;
    border-top: 1px solid #eee;
    background: #f8f9fa;
}

/* Selected seats info */
.selected-seats-info {
    background: #e8f4fd;
    border: 1px solid #b8daff;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.selected-seats-info h6 {
    margin: 0 0 8px 0;
    color: #004085;
    font-weight: 600;
}

.selected-seats-list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.selected-seat-tag {
    background: #3498db;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: 500;
}

/* Seat type options */
.seat-type-options {
    margin-bottom: 20px;
}

.seat-type-options > label:first-child {
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    display: block;
}

.seat-type-option {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.seat-type-option:hover {
    border-color: #adb5bd;
    background: #f8f9fa;
}

.seat-type-option.active {
    border-color: #3498db;
    background: #e8f4fd;
}

.seat-type-option input[type="radio"] {
    display: none;
}

.seat-type-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    margin-right: 14px;
    flex-shrink: 0;
}

.seat-type-icon.standard { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
.seat-type-icon.vip { background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%); }
.seat-type-icon.couple { background: linear-gradient(135deg, #e84393 0%, #d63384 100%); }

.seat-type-details {
    flex: 1;
}

.seat-type-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
}

.seat-type-desc {
    font-size: 0.85em;
    color: #6c757d;
}

/* Couple mode notice */
.couple-mode-notice {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 20px;
    display: none;
}

.couple-mode-notice.show {
    display: block;
}

.couple-mode-notice i {
    color: #856404;
    margin-right: 8px;
}

.couple-mode-notice p {
    margin: 0;
    color: #856404;
    font-size: 0.9em;
}

/* Action buttons */
.sidebar-actions {
    display: flex;
    gap: 10px;
}

.sidebar-actions .btn {
    flex: 1;
    padding: 12px 16px;
    font-weight: 600;
    border-radius: 8px;
}

.btn-apply {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    border: none;
    color: white;
}

.btn-apply:hover {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    color: white;
}

.btn-apply:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

/* Selection mode indicator */
.selection-mode-bar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: none;
    align-items: center;
    justify-content: space-between;
}

.selection-mode-bar.show {
    display: flex;
}

.selection-mode-bar .mode-text {
    font-weight: 500;
}

.selection-mode-bar .selected-count {
    background: rgba(255,255,255,0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.9em;
}

/* Responsive */
@media (max-width: 992px) {
    .seat-btn {
        width: 40px;
        height: 40px;
        font-size: 0.85em;
    }
    .seat-btn.couple {
        width: 85px;
    }
    .seat-row-label {
        min-width: 70px;
        font-size: 1em;
    }
}

@media (max-width: 768px) {
    .seat-sidebar {
        width: 100%;
        max-width: 100%;
    }
    .seat-btn {
        width: 35px;
        height: 35px;
        font-size: 0.75em;
        border-radius: 6px;
    }
    .seat-btn.couple {
        width: 75px;
        font-size: 0.75em;
    }
    .seat-row-label {
        min-width: 50px;
        font-size: 0.9em;
        padding-right: 10px;
    }
    .seat-row {
        margin-bottom: 10px;
        gap: 3px;
    }
    .seats-container {
        gap: 3px;
    }
}

@media (max-width: 480px) {
    .seat-btn {
        width: 30px;
        height: 30px;
        font-size: 0.7em;
    }
    .seat-btn.couple {
        width: 65px;
    }
    .seat-row-label {
        min-width: 40px;
        font-size: 0.85em;
    }
}
</style>
@endpush

@section('content')
<div class="admin-room-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Edit Room: {{ $room->name }}</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Rooms
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Room Info Form -->
    <div class="room-info-card">
        <h4><i class="bi bi-info-circle me-2"></i>Room Information</h4>
        <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Room Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $room->name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="screen_type_id" class="form-label">Screen Type</label>
                    <select class="form-select" id="screen_type_id" name="screen_type_id" required>
                        @foreach($screenTypes as $type)
                            <option value="{{ $type->id }}" @if($room->screen_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-2"></i>Update Room Info
            </button>
        </form>
    </div>

    <!-- Seat Prices Section đã được chuyển thành section chung toàn hệ thống -->

    <!-- Seat Map Section -->
    <div class="seat-map-section">
        <h4><i class="bi bi-grid-3x3 me-2"></i>Seat Map Configuration</h4>
        <p class="section-subtitle">Click on seats to select them, then choose a seat type from the sidebar. You can select multiple seats at once.</p>

        <!-- Legend -->
        <div class="seat-legend">
            <h4>Legend:</h4>
            <div class="legend-items">
                <div class="legend-item">
                    <span class="legend-color standard"></span>
                    <span>Standard</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color vip"></span>
                    <span>VIP</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color couple"></span>
                    <span>Couple</span>
                </div>
                <div class="legend-item">
                    <span class="legend-color selected"></span>
                    <span>Selected</span>
                </div>
            </div>
        </div>

        <!-- Selection Mode Bar -->
        <div class="selection-mode-bar" id="selectionModeBar">
            <span class="mode-text"><i class="bi bi-cursor me-2"></i>Selection Mode</span>
            <span class="selected-count"><span id="selectedCountText">0</span> seats selected</span>
        </div>

        <!-- Cinema Screen -->
        <div class="cinema-screen">
            <div class="screen-label">Screen</div>
        </div>

        <!-- Seat Map Form -->
        <form action="{{ route('admin.rooms.update-seats', $room->id) }}" method="POST" id="adminSeatMapForm">
            @csrf
            <div class="seat-map-wrapper">
                <div id="adminSeatMap">
                    @foreach($seatsByRow as $row => $seats)
                    <div class="seat-row">
                        <div class="seat-row-label">Row {{ $row }}:</div>
                        <div class="seats-container">
                            @php $i = 0; @endphp
                            @while($i < count($seats))
                                @php $seat = $seats[$i]; @endphp
                                @if($seat->seat_type_id == 3 && isset($seats[$i+1]) && $seats[$i+1]->seat_type_id == 3)
                                    {{-- Couple seat - render as single wide button --}}
                                    @php $seat2 = $seats[$i+1]; @endphp
                                    <button type="button"
                                        class="seat-btn couple seat-type-3"
                                        data-seat-id="{{ $seat->id }}"
                                        data-seat-id2="{{ $seat2->id }}"
                                        data-seat-type="3"
                                        data-seat-row="{{ $row }}"
                                        data-seat-number="{{ $seat->seat_number }}"
                                        data-seat-number2="{{ $seat2->seat_number }}"
                                        data-seat-index="{{ $i }}">
                                        {{ $seat->seat_number }}-{{ $seat2->seat_number }}
                                    </button>
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_id]" value="{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_type_id]" value="{{ $seat->seat_type_id }}" id="seat-type-input-{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat2->id }}][seat_id]" value="{{ $seat2->id }}">
                                    <input type="hidden" name="seats[{{ $seat2->id }}][seat_type_id]" value="{{ $seat2->seat_type_id }}" id="seat-type-input-{{ $seat2->id }}">
                                    @php $i += 2; @endphp
                                @else
                                    {{-- Regular seat --}}
                                    <button type="button"
                                        class="seat-btn seat-type-{{ $seat->seat_type_id }}"
                                        data-seat-id="{{ $seat->id }}"
                                        data-seat-type="{{ $seat->seat_type_id }}"
                                        data-seat-row="{{ $row }}"
                                        data-seat-number="{{ $seat->seat_number }}"
                                        data-seat-index="{{ $i }}">
                                        {{ $seat->seat_number }}
                                    </button>
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_id]" value="{{ $seat->id }}">
                                    <input type="hidden" name="seats[{{ $seat->id }}][seat_type_id]" value="{{ $seat->seat_type_id }}" id="seat-type-input-{{ $seat->id }}">
                                    @php $i++; @endphp
                                @endif
                            @endwhile
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-save me-2"></i>Save All Changes
                </button>
                <button type="button" class="btn btn-outline-primary" id="openSidebarBtn">
                    <i class="bi bi-pencil me-2"></i>Edit Selected Seats
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sidebar Overlay -->
<div class="seat-sidebar-overlay" id="sidebarOverlay"></div>

<!-- Seat Edit Sidebar -->
<div class="seat-sidebar" id="seatSidebar">
    <div class="sidebar-header d-flex justify-content-between align-items-center">
        <h5><i class="bi bi-grid-1x2 me-2"></i>Edit Seat Type</h5>
        <button type="button" class="close-btn" id="closeSidebarBtn">
            <i class="bi bi-x"></i>
        </button>
    </div>

    <div class="sidebar-body">
        <!-- Selected Seats Info -->
        <div class="selected-seats-info">
            <h6><i class="bi bi-check-square me-2"></i>Selected Seats</h6>
            <div class="selected-seats-list" id="selectedSeatsList">
                <span class="text-muted">No seats selected</span>
            </div>
        </div>

        <!-- Couple Mode Notice -->
        <div class="couple-mode-notice" id="coupleModeNotice">
            <i class="bi bi-exclamation-triangle"></i>
            <p><strong>Couple Seat:</strong> Please select exactly 2 adjacent seats in the same row to create a couple seat.</p>
        </div>

        <!-- Seat Type Options -->
        <div class="seat-type-options">
            <label>Select Seat Type:</label>

            @foreach($seatTypes as $type)
            <label class="seat-type-option" data-type-id="{{ $type->id }}">
                <input type="radio" name="sidebar_seat_type" value="{{ $type->id }}" {{ $loop->first ? 'checked' : '' }}>
                <div class="seat-type-icon {{ strtolower($type->name) }}"></div>
                <div class="seat-type-details">
                    <div class="seat-type-name">{{ $type->name }}</div>
                    <div class="seat-type-desc">
                        @if($type->name == 'Standard')
                            Regular seating
                        @elseif($type->name == 'VIP')
                            Premium comfort seats
                        @elseif($type->name == 'Couple')
                            Paired seats for couples
                        @else
                            {{ $type->description ?? '' }}
                        @endif
                    </div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-actions">
            <button type="button" class="btn btn-secondary" id="cancelSidebarBtn">Cancel</button>
            <button type="button" class="btn btn-apply" id="applySeatTypeBtn" disabled>
                <i class="bi bi-check me-2"></i>Apply
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const seatSidebar = document.getElementById('seatSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const cancelSidebarBtn = document.getElementById('cancelSidebarBtn');
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const applySeatTypeBtn = document.getElementById('applySeatTypeBtn');
    const selectedSeatsList = document.getElementById('selectedSeatsList');
    const coupleModeNotice = document.getElementById('coupleModeNotice');
    const selectionModeBar = document.getElementById('selectionModeBar');
    const selectedCountText = document.getElementById('selectedCountText');
    const seatTypeOptions = document.querySelectorAll('.seat-type-option');
    const seatBtns = document.querySelectorAll('.seat-btn');

    // State
    let selectedSeats = []; // Array of {id, id2 (for couple), row, number, number2 (for couple), element, currentType, isCouple}
    let selectedSeatType = 1; // Default to Standard

    // Functions
    function openSidebar() {
        seatSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        updateSidebarContent();
    }

    function closeSidebar() {
        seatSidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    function updateSelectionBar() {
        const totalSeats = selectedSeats.reduce((sum, seat) => sum + (seat.isCouple ? 2 : 1), 0);
        if (totalSeats > 0) {
            selectionModeBar.classList.add('show');
            selectedCountText.textContent = totalSeats;
        } else {
            selectionModeBar.classList.remove('show');
        }
    }

    function updateSidebarContent() {
        if (selectedSeats.length === 0) {
            selectedSeatsList.innerHTML = '<span class="text-muted">No seats selected</span>';
            applySeatTypeBtn.disabled = true;
        } else {
            selectedSeatsList.innerHTML = selectedSeats.map(seat => {
                if (seat.isCouple) {
                    return `<span class="selected-seat-tag">${seat.row}${seat.number}-${seat.number2}</span>`;
                }
                return `<span class="selected-seat-tag">${seat.row}${seat.number}</span>`;
            }).join('');

            // Check if couple type is selected
            if (selectedSeatType == 3) {
                const canCouple = checkCanCouple();
                applySeatTypeBtn.disabled = !canCouple;
                coupleModeNotice.classList.toggle('show', !canCouple);
            } else {
                applySeatTypeBtn.disabled = false;
                coupleModeNotice.classList.remove('show');
            }
        }
    }

    function checkCanCouple() {
        // Get all individual seats (expand couple selections)
        let allSeats = [];
        selectedSeats.forEach(seat => {
            if (seat.isCouple) {
                allSeats.push({ row: seat.row, number: seat.number, index: seat.index });
                allSeats.push({ row: seat.row, number: seat.number2, index: seat.index + 1 });
            } else {
                allSeats.push({ row: seat.row, number: seat.number, index: seat.index });
            }
        });

        // Must have exactly 2 seats
        if (allSeats.length !== 2) return false;

        // Same row
        if (allSeats[0].row !== allSeats[1].row) return false;

        // Adjacent
        return Math.abs(allSeats[0].index - allSeats[1].index) === 1;
    }

    function selectSeat(btn) {
        const seatId = btn.dataset.seatId;
        const seatId2 = btn.dataset.seatId2; // For couple seats
        const isCouple = btn.classList.contains('couple');

        const existing = selectedSeats.find(s => s.id === seatId);

        if (existing) {
            // Deselect
            selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            btn.classList.remove('selected');
        } else {
            // Select
            const seatInfo = {
                id: seatId,
                id2: seatId2 || null,
                row: btn.dataset.seatRow,
                number: parseInt(btn.dataset.seatNumber),
                number2: btn.dataset.seatNumber2 ? parseInt(btn.dataset.seatNumber2) : null,
                element: btn,
                currentType: parseInt(btn.dataset.seatType),
                index: parseInt(btn.dataset.seatIndex),
                isCouple: isCouple
            };
            selectedSeats.push(seatInfo);
            btn.classList.add('selected');
        }

        updateSelectionBar();
        if (seatSidebar.classList.contains('show')) {
            updateSidebarContent();
        }
    }

    function applySeatType() {
        if (selectedSeats.length === 0) return;

        const newType = parseInt(selectedSeatType);

        // Special handling for couple type
        if (newType == 3) {
            if (!checkCanCouple()) {
                alert('Please select exactly 2 adjacent seats in the same row for couple seats.');
                return;
            }
        }

        // Collect all seat IDs to update
        let seatIdsToUpdate = [];
        selectedSeats.forEach(seat => {
            seatIdsToUpdate.push(seat.id);
            if (seat.id2) {
                seatIdsToUpdate.push(seat.id2);
            }
        });

        // Update hidden inputs
        seatIdsToUpdate.forEach(seatId => {
            const hiddenInput = document.getElementById('seat-type-input-' + seatId);
            if (hiddenInput) {
                hiddenInput.value = newType;
            }
        });

        // Clear selection and reload page to show updated layout
        // (Since changing from couple to single or vice versa requires DOM restructuring)
        clearSelection();
        closeSidebar();

        // Submit the form to save and reload
        document.getElementById('adminSeatMapForm').submit();
    }

    function clearSelection() {
        selectedSeats.forEach(seat => {
            seat.element.classList.remove('selected');
        });
        selectedSeats = [];
        updateSelectionBar();
        updateSidebarContent();
    }

    // Event Listeners

    // Seat button clicks
    seatBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            selectSeat(this);
        });
    });

    // Seat type option clicks
    seatTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            seatTypeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input').checked = true;
            selectedSeatType = this.dataset.typeId;
            updateSidebarContent();
        });
    });

    // Initialize first option as active
    if (seatTypeOptions.length > 0) {
        seatTypeOptions[0].classList.add('active');
    }

    // Open sidebar button
    openSidebarBtn.addEventListener('click', function() {
        if (selectedSeats.length === 0) {
            alert('Please select at least one seat first.');
            return;
        }
        openSidebar();
    });

    // Close sidebar
    closeSidebarBtn.addEventListener('click', closeSidebar);
    cancelSidebarBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);

    // Apply seat type
    applySeatTypeBtn.addEventListener('click', applySeatType);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && seatSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Double-click to open sidebar
    document.getElementById('adminSeatMap').addEventListener('dblclick', function(e) {
        if (selectedSeats.length > 0) {
            openSidebar();
        }
    });

    // ========== Seat Prices AJAX Form ==========
    const seatPricesForm = document.getElementById('seatPricesForm');
    const savePricesBtn = document.getElementById('savePricesBtn');
    const pricesSaveStatus = document.getElementById('pricesSaveStatus');

    seatPricesForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Disable button and show loading
        savePricesBtn.disabled = true;
        savePricesBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';
        pricesSaveStatus.innerHTML = '';

        const formData = new FormData(seatPricesForm);

        fetch('{{ route("admin.rooms.update-prices", $room->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pricesSaveStatus.innerHTML = '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' + data.message + '</span>';
            } else {
                pricesSaveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>' + data.message + '</span>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            pricesSaveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Failed to save prices</span>';
        })
        .finally(() => {
            // Re-enable button
            savePricesBtn.disabled = false;
            savePricesBtn.innerHTML = '<i class="bi bi-save me-2"></i>Save Prices';

            // Clear status after 3 seconds
            setTimeout(() => {
                pricesSaveStatus.innerHTML = '';
            }, 3000);
        });
    });
});
</script>
@endpush
