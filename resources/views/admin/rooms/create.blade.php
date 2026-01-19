@extends('layouts.admin')

@section('title', 'Add New Room')

@push('styles')
<style>
/* ==================== Admin Create Room Styles (Cinema Style) ==================== */
.admin-room-container {
    max-width: 1400px;
    margin: 0 auto;
}

.room-form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
}

.room-form-card h4 {
    margin: 0 0 20px 0;
    color: #1a1a2e;
    font-weight: 600;
}

/* ========== Seat Map Preview Section ========== */
.seat-preview-section {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    padding: 24px;
    margin-bottom: 24px;
}

.seat-preview-section h4 {
    margin: 0 0 8px 0;
    color: #1a1a2e;
    font-weight: 600;
}

.seat-preview-section .section-subtitle {
    color: #6c757d;
    font-size: 0.9em;
    margin-bottom: 20px;
}

/* Quick templates */
.quick-templates {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.quick-templates h6 {
    margin: 0 0 12px 0;
    color: #333;
    font-weight: 600;
}

.template-btns {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.template-btn {
    padding: 8px 16px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: white;
    cursor: pointer;
    font-size: 0.85em;
    transition: all 0.2s;
}

.template-btn:hover {
    border-color: #3498db;
    background: #e8f4fd;
}

.template-btn.active {
    border-color: #28a745;
    background: #d4edda;
    color: #155724;
    font-weight: 600;
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

#previewSeatMap {
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
.seat-btn.seat-type-1 {
    background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
}

.seat-btn.seat-type-2 {
    background: linear-gradient(135deg, #f1c40f 0%, #f39c12 100%);
}

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

/* Empty state */
.empty-preview {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-preview i {
    font-size: 4em;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-preview p {
    margin: 0;
    font-size: 1.1em;
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
        <h2 class="mb-0">Add New Room</h2>
        <a href="{{ route('admin.rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Rooms
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.rooms.store') }}" method="POST" id="createRoomForm">
        @csrf

        <!-- Room Info Form -->
        <div class="room-form-card">
            <h4><i class="bi bi-info-circle me-2"></i>Room Information</h4>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Room Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="e.g. Room 1, Hall A" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="screen_type_id" class="form-label">Screen Type <span class="text-danger">*</span></label>
                    <select class="form-select" id="screen_type_id" name="screen_type_id" required>
                        @foreach($screenTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="total_rows" class="form-label">Total Rows <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_rows" name="total_rows" min="1" max="26" value="8" required>
                    <small class="text-muted">Maximum 26 rows (A-Z)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="seats_per_row" class="form-label">Seats Per Row <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="seats_per_row" name="seats_per_row" min="1" max="30" value="10" required>
                    <small class="text-muted">Maximum 30 seats per row</small>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary" id="generatePreviewBtn">
                <i class="bi bi-eye me-2"></i>Generate Preview
            </button>
        </div>

        <!-- Seat Map Preview Section -->
        <div class="seat-preview-section">
            <h4><i class="bi bi-grid-3x3 me-2"></i>Seat Map Preview</h4>
            <p class="section-subtitle">Preview the seat layout and optionally customize seat types before creating the room. Click seats to select, then use sidebar to change type.</p>

            <!-- Quick Templates -->
            <div class="quick-templates">
                <h6><i class="bi bi-magic me-2"></i>Quick Templates</h6>
                <div class="template-btns">
                    <button type="button" class="template-btn" data-template="all-standard">All Standard</button>
                    <button type="button" class="template-btn" data-template="vip-center">VIP Center Rows</button>
                    <button type="button" class="template-btn" data-template="couple-back">Couple Back Rows</button>
                    <button type="button" class="template-btn" data-template="cinema-style">Cinema Style</button>
                </div>
            </div>

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

            <!-- Seat Map Preview -->
            <div class="seat-map-wrapper">
                <div id="previewSeatMap">
                    <div class="empty-preview">
                        <i class="bi bi-grid-1x2"></i>
                        <p>Enter room dimensions and click "Generate Preview" to see the seat layout</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-plus me-2"></i>Create Room
                </button>
                <button type="button" class="btn btn-outline-primary" id="openSidebarBtn" disabled>
                    <i class="bi bi-pencil me-2"></i>Edit Selected Seats
                </button>
            </div>
        </div>

        <!-- Hidden inputs for seat configurations -->
        <div id="seatConfigInputs"></div>
    </form>
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
    const totalRowsInput = document.getElementById('total_rows');
    const seatsPerRowInput = document.getElementById('seats_per_row');
    const generatePreviewBtn = document.getElementById('generatePreviewBtn');
    const previewSeatMap = document.getElementById('previewSeatMap');
    const seatConfigInputs = document.getElementById('seatConfigInputs');
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
    const templateBtns = document.querySelectorAll('.template-btn');

    // State
    let seatData = []; // Array of {row, number, type}
    let selectedSeats = []; // Array of {row, number, index, isCouple, number2}
    let selectedSeatType = 1; // Default to Standard
    let previewGenerated = false;

    const rowLabels = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

    // Functions
    function generatePreview() {
        const totalRows = parseInt(totalRowsInput.value) || 0;
        const seatsPerRow = parseInt(seatsPerRowInput.value) || 0;

        if (totalRows < 1 || seatsPerRow < 1) {
            alert('Please enter valid row and seat numbers.');
            return;
        }

        // Initialize seat data with default type (Standard = 1)
        seatData = [];
        for (let r = 0; r < totalRows; r++) {
            for (let s = 1; s <= seatsPerRow; s++) {
                seatData.push({
                    row: rowLabels[r],
                    number: s,
                    type: 1 // Standard
                });
            }
        }

        renderSeatMap();
        previewGenerated = true;
        updateHiddenInputs();
    }

    function renderSeatMap() {
        const totalRows = parseInt(totalRowsInput.value) || 0;
        const seatsPerRow = parseInt(seatsPerRowInput.value) || 0;

        if (seatData.length === 0) {
            previewSeatMap.innerHTML = `
                <div class="empty-preview">
                    <i class="bi bi-grid-1x2"></i>
                    <p>Enter room dimensions and click "Generate Preview" to see the seat layout</p>
                </div>
            `;
            return;
        }

        let html = '';

        for (let r = 0; r < totalRows; r++) {
            const rowLabel = rowLabels[r];
            html += `<div class="seat-row"><div class="seat-row-label">Row ${rowLabel}:</div><div class="seats-container">`;

            let s = 0;
            while (s < seatsPerRow) {
                const seatIndex = r * seatsPerRow + s;
                const seat = seatData[seatIndex];

                if (!seat) {
                    s++;
                    continue;
                }

                // Check if this and next seat are both couple type
                const nextSeat = seatData[seatIndex + 1];
                if (seat.type === 3 && nextSeat && nextSeat.type === 3 && nextSeat.row === seat.row) {
                    // Render as couple (wide button)
                    html += `
                        <button type="button" class="seat-btn couple seat-type-3"
                            data-row="${seat.row}" data-number="${seat.number}" data-number2="${nextSeat.number}"
                            data-type="3" data-index="${seatIndex}" data-is-couple="true">
                            ${seat.number}-${nextSeat.number}
                        </button>
                    `;
                    s += 2;
                } else {
                    // Render as single seat
                    html += `
                        <button type="button" class="seat-btn seat-type-${seat.type}"
                            data-row="${seat.row}" data-number="${seat.number}" data-type="${seat.type}" data-index="${seatIndex}">
                            ${seat.number}
                        </button>
                    `;
                    s++;
                }
            }

            html += '</div></div>';
        }

        previewSeatMap.innerHTML = html;

        // Re-bind click events
        bindSeatClicks();
    }

    function bindSeatClicks() {
        const seatBtns = previewSeatMap.querySelectorAll('.seat-btn');

        seatBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                selectSeat(this);
            });
        });
    }

    function updateHiddenInputs() {
        let html = '';
        seatData.forEach((seat, index) => {
            html += `<input type="hidden" name="seat_configs[${index}][row]" value="${seat.row}">`;
            html += `<input type="hidden" name="seat_configs[${index}][number]" value="${seat.number}">`;
            html += `<input type="hidden" name="seat_configs[${index}][type]" value="${seat.type}">`;
        });
        seatConfigInputs.innerHTML = html;
    }

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
            openSidebarBtn.disabled = false;
        } else {
            selectionModeBar.classList.remove('show');
            openSidebarBtn.disabled = true;
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
        const row = btn.dataset.row;
        const number = parseInt(btn.dataset.number);
        const number2 = btn.dataset.number2 ? parseInt(btn.dataset.number2) : null;
        const index = parseInt(btn.dataset.index);
        const isCouple = btn.dataset.isCouple === 'true';

        const existing = selectedSeats.find(s => s.row === row && s.number === number);

        if (existing) {
            selectedSeats = selectedSeats.filter(s => !(s.row === row && s.number === number));
            btn.classList.remove('selected');
        } else {
            selectedSeats.push({
                row: row,
                number: number,
                number2: number2,
                index: index,
                element: btn,
                currentType: parseInt(btn.dataset.type),
                isCouple: isCouple
            });
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

        if (newType === 3 && !checkCanCouple()) {
            alert('Please select exactly 2 adjacent seats in the same row for couple seats.');
            return;
        }

        // Update seat data
        selectedSeats.forEach(seat => {
            if (seat.isCouple) {
                // Update both seats in couple
                seatData[seat.index].type = newType;
                seatData[seat.index + 1].type = newType;
            } else {
                seatData[seat.index].type = newType;
            }
        });

        // If setting to couple with 2 single seats, need to update both
        if (newType === 3 && selectedSeats.length === 2 && !selectedSeats[0].isCouple && !selectedSeats[1].isCouple) {
            // Both are already updated above
        }

        // Clear selection and re-render
        clearSelection();
        renderSeatMap();
        updateHiddenInputs();
        closeSidebar();
    }

    function clearSelection() {
        selectedSeats.forEach(seat => {
            if (seat.element) {
                seat.element.classList.remove('selected');
            }
        });
        selectedSeats = [];
        updateSelectionBar();
        updateSidebarContent();
    }

    // Track active templates
    let activeTemplates = new Set();

    // Templates - VIP Center and Couple Back can be combined
    function applyTemplate(template) {
        if (!previewGenerated || seatData.length === 0) {
            alert('Please generate preview first.');
            return;
        }

        const totalRows = parseInt(totalRowsInput.value);
        const seatsPerRow = parseInt(seatsPerRowInput.value);

        // Handle template toggle logic
        if (template === 'all-standard' || template === 'cinema-style') {
            // These reset everything
            activeTemplates.clear();
            if (template === 'cinema-style') {
                activeTemplates.add('cinema-style');
            }
        } else if (template === 'vip-center' || template === 'couple-back') {
            // These can be combined - toggle on/off
            if (activeTemplates.has(template)) {
                activeTemplates.delete(template);
            } else {
                // Remove cinema-style if adding individual templates
                activeTemplates.delete('cinema-style');
                activeTemplates.add(template);
            }
        }

        // Reset all to standard first
        seatData.forEach(seat => seat.type = 1);

        // Apply active templates
        if (activeTemplates.has('cinema-style')) {
            // Cinema style: Standard front, VIP middle, Couple back
            const frontRows = Math.floor(totalRows / 3);
            const middleRows = Math.ceil(totalRows * 2 / 3);

            seatData.forEach((seat, index) => {
                const rowIndex = rowLabels.indexOf(seat.row);

                if (rowIndex >= frontRows && rowIndex < middleRows) {
                    // Middle rows - VIP for center seats
                    const vipStart = Math.floor(seatsPerRow / 4);
                    const vipEnd = Math.ceil(seatsPerRow * 3 / 4);
                    if (seat.number > vipStart && seat.number <= vipEnd) {
                        seat.type = 2;
                    }
                } else if (rowIndex >= middleRows) {
                    // Back rows - Couple
                    if (seat.number % 2 === 1 && seat.number < seatsPerRow) {
                        seat.type = 3;
                        if (seatData[index + 1] && seatData[index + 1].row === seat.row) {
                            seatData[index + 1].type = 3;
                        }
                    }
                }
            });
        } else {
            // Apply VIP Center if active
            if (activeTemplates.has('vip-center')) {
                const vipStartRow = Math.floor(totalRows / 3);
                const vipEndRow = Math.ceil(totalRows * 2 / 3) - (activeTemplates.has('couple-back') ? 1 : 0);
                const vipStartSeat = Math.floor(seatsPerRow / 4);
                const vipEndSeat = Math.ceil(seatsPerRow * 3 / 4);

                seatData.forEach(seat => {
                    const rowIndex = rowLabels.indexOf(seat.row);
                    if (rowIndex >= vipStartRow && rowIndex < vipEndRow &&
                        seat.number > vipStartSeat && seat.number <= vipEndSeat) {
                        seat.type = 2; // VIP
                    }
                });
            }

            // Apply Couple Back if active (last row only)
            if (activeTemplates.has('couple-back')) {
                const lastRowIndex = totalRows - 1;
                seatData.forEach((seat, index) => {
                    const rowIndex = rowLabels.indexOf(seat.row);
                    if (rowIndex === lastRowIndex) {
                        // Make pairs (1-2, 3-4, etc.)
                        if (seat.number % 2 === 1 && seat.number < seatsPerRow) {
                            seat.type = 3; // Couple
                            if (seatData[index + 1] && seatData[index + 1].row === seat.row) {
                                seatData[index + 1].type = 3;
                            }
                        } else if (seat.number % 2 === 0 && seat.number === seatsPerRow) {
                            // Last seat if odd number - keep as couple pair
                            seat.type = 3;
                        }
                    }
                });
            }
        }

        // Update button states
        updateTemplateButtons();
        renderSeatMap();
        updateHiddenInputs();
        clearSelection();
    }

    function updateTemplateButtons() {
        templateBtns.forEach(btn => {
            const template = btn.dataset.template;
            if (activeTemplates.has(template)) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Event Listeners
    generatePreviewBtn.addEventListener('click', generatePreview);

    // Auto-generate on input change
    [totalRowsInput, seatsPerRowInput].forEach(input => {
        input.addEventListener('change', function() {
            if (previewGenerated) {
                generatePreview();
            }
        });
    });

    // Template buttons
    templateBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            applyTemplate(this.dataset.template);
        });
    });

    // Seat type options
    seatTypeOptions.forEach(option => {
        option.addEventListener('click', function() {
            seatTypeOptions.forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            this.querySelector('input').checked = true;
            selectedSeatType = this.dataset.typeId;
            updateSidebarContent();
        });
    });

    if (seatTypeOptions.length > 0) {
        seatTypeOptions[0].classList.add('active');
    }

    // Sidebar controls
    openSidebarBtn.addEventListener('click', function() {
        if (selectedSeats.length === 0) {
            alert('Please select at least one seat first.');
            return;
        }
        openSidebar();
    });

    closeSidebarBtn.addEventListener('click', closeSidebar);
    cancelSidebarBtn.addEventListener('click', closeSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    applySeatTypeBtn.addEventListener('click', applySeatType);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && seatSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });

    // Double-click to open sidebar
    previewSeatMap.addEventListener('dblclick', function(e) {
        if (selectedSeats.length > 0) {
            openSidebar();
        }
    });
});
</script>
@endpush
