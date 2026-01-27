{{--
/**
 * Admin Booking Details
 * 
 * Detailed booking information including:
 * - Complete booking information display
 * - Customer details and contact
 * - Seat and payment information
 * - QR code and ticket details
 * - Booking status management
 */
--}}
@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Bookings
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                <h4 class="mb-0"><i class="bi bi-ticket-detailed"></i> Booking #{{ $booking->id }}</h4>
            </div>
            <div class="card-body">
                <!-- Status Badges -->
                <div class="mb-4">
                    @if($booking->status == 'confirmed')
                    <span class="badge bg-success fs-6">Confirmed</span>
                    @elseif($booking->status == 'pending')
                    <span class="badge bg-warning fs-6">Pending</span>
                    @elseif($booking->status == 'cancelled')
                    <span class="badge bg-danger fs-6">Cancelled</span>
                    @else
                    <span class="badge bg-secondary fs-6">{{ ucfirst($booking->status) }}</span>
                    @endif

                    @if($booking->payment_status == 'paid')
                    <span class="badge bg-success fs-6">Paid</span>
                    @elseif($booking->payment_status == 'refunded')
                    <span class="badge bg-info fs-6">Refunded</span>
                    @else
                    <span class="badge bg-warning fs-6">{{ ucfirst($booking->payment_status) }}</span>
                    @endif
                </div>

                <!-- Movie & Showtime Info -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <img src="{{ $booking->showtime->movie->poster_url }}"
                            alt="{{ $booking->showtime->movie->title }}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h4 style="color: var(--prussian-blue)">{{ $booking->showtime->movie->title }}</h4>

                        <div class="row mt-3">
                            <div class="col-6 mb-2">
                                <p class="text-muted mb-1"><small>SHOW DATE</small></p>
                                <p class="mb-0">
                                    <strong>{{ $booking->showtime->show_date->format('l, M d, Y') }}</strong>
                                </p>
                            </div>
                            <div class="col-6 mb-2">
                                <p class="text-muted mb-1"><small>SHOW TIME</small></p>
                                <p class="mb-0">
                                    <strong>{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</strong>
                                </p>
                            </div>
                            <div class="col-6 mb-2">
                                <p class="text-muted mb-1"><small>ROOM</small></p>
                                <p class="mb-0"><strong>{{ $booking->showtime->room->name }}</strong></p>
                            </div>
                            <div class="col-6 mb-2">
                                <p class="text-muted mb-1"><small>SCREEN TYPE</small></p>
                                <p class="mb-0">
                                    @php
                                    $screenTypeBg = match($booking->showtime->room->screenType->name) {
                                    '2D' => 'bg-primary',
                                    '3D' => 'bg-success',
                                    'IMAX' => 'bg-danger',
                                    default => 'bg-secondary'
                                    };
                                    @endphp
                                    <span class="badge {{ $screenTypeBg }}">
                                        {{ $booking->showtime->room->screenType->name }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Seats Info -->
                <h5 style="color: var(--prussian-blue)"><i class="bi bi-grid-3x3"></i> Booked Seats</h5>
                <div class="row">
                    @foreach($booking->bookingSeats as $bookingSeat)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h3 class="mb-1" style="color: var(--deep-teal)">{{ $bookingSeat->seat->seat_code }}
                                </h3>
                                <small class="text-muted">{{ ucfirst($bookingSeat->seat->seatType->name) }}</small>
                                <p class="mb-0 mt-2"><strong>{{ number_format($bookingSeat->price) }}₫</strong></p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <hr>

                <!-- Price Breakdown -->
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <table class="table">
                            <tr>
                                <td>Seats ({{ $booking->bookingSeats->count() }})</td>
                                <td class="text-end">{{ number_format($booking->bookingSeats->sum('price')) }}₫</td>
                            </tr>
                            <tr class="table-active">
                                <td><strong>Total</strong></td>
                                <td class="text-end"><strong
                                        class="text-success h5">{{ number_format($booking->total_price) }}₫</strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--tan);">
                <h5 class="mb-0"><i class="bi bi-person"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2"><strong>Name:</strong><br>{{ $booking->user->name }}</p>
                <p class="mb-2"><strong>Email:</strong><br>{{ $booking->user->email }}</p>
                @if($booking->user->phone)
                <p class="mb-2"><strong>Phone:</strong><br>{{ $booking->user->phone }}</p>
                @endif
                @if($booking->user->city)
                <p class="mb-0"><strong>City:</strong><br>{{ $booking->user->city }}</p>
                @endif
            </div>
        </div>

        <!-- Payment Info -->
        <div class="card mb-4">
            <div class="card-header" style="background-color: var(--tan);">
                <h5 class="mb-0"><i class="bi bi-credit-card"></i> Payment Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Method:</strong><br>
                    {{ ucfirst($booking->payment_method) }}
                </p>
                <p class="mb-2">
                    <strong>Status:</strong><br>
                    @if($booking->payment_status == 'paid')
                    <span class="badge bg-success">Paid</span>
                    @elseif($booking->payment_status == 'refunded')
                    <span class="badge bg-info">Refunded</span>
                    @else
                    <span class="badge bg-warning">{{ ucfirst($booking->payment_status) }}</span>
                    @endif
                </p>
                <p class="mb-0">
                    <strong>Total:</strong><br>
                    <span class="h4 text-success">{{ number_format($booking->total_price) }}₫</span>
                </p>
            </div>
        </div>

        <!-- Booking Timeline -->
        <div class="card">
            <div class="card-header" style="background-color: var(--tan);">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Timeline</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Booked On:</strong><br>
                    {{ $booking->booking_date->format('M d, Y h:i A') }}
                </p>
                <p class="mb-2">
                    <strong>QR Code:</strong><br>
                    <code>{{ $booking->qr_code }}</code>
                </p>
                @if($booking->expired_at)
                <p class="mb-2">
                    <strong>Expired At:</strong><br>
                    {{ $booking->expired_at->format('M d, Y h:i A') }}
                </p>
                @endif
                <p class="mb-0">
                    <strong>Last Updated:</strong><br>
                    {{ $booking->updated_at->format('M d, Y h:i A') }}
                </p>
            </div>
        </div>

        <!-- Actions -->
        @if($booking->status == 'confirmed' || $booking->status == 'pending')
        @php
        // Check if showtime has ended
        $showtimeEnded = $booking->showtime->status === 'done';
        @endphp

        <div class="card mt-4 border-danger">
            <div class="card-body">
                <h6 class="text-danger">Admin Actions</h6>

                @if($showtimeEnded)
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Showtime Ended:</strong> Cannot cancel booking as the showtime has already finished.
                </div>
                <button type="button" class="btn btn-secondary w-100" disabled>
                    <i class="bi bi-x-circle"></i> Cancel Booking (Not Available)
                </button>
                @else
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100"
                        onclick="return confirm('Are you sure you want to cancel this booking? This will refund the customer if payment was made.')">
                        <i class="bi bi-x-circle"></i> Cancel Booking
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection