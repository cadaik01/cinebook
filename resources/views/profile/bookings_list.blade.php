@extends('profile.profilepage')

@section('title','Your Bookings')

@section('content')
<div class="admin-header">
    <h2><i class="fas fa-history"></i> Your Booking History</h2>
    <p class="text-muted">Review your bookings and details</p>
</div>

<!-- Upcoming Bookings -->
<div class="mb-4">
    <h4 class="mb-3"><i class="fas fa-calendar-check text-success"></i> Upcoming Bookings</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Movie</th>
                    <th scope="col">Showtime</th>
                    <th scope="col">Room</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Booked At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($upcomingBookings as $booking)
                <tr>
                    <td>{{ $booking->showtime->movie->title ?? 'N/A' }}</td>
                    <td>
                        {{ $booking->showtime->show_date->format('d M Y') }}, 
                        {{ $booking->showtime->show_time->format('H:i') }}
                    </td>
                    <td>{{ $booking->showtime->room->name ?? 'N/A' }}</td>
                    <td>{{ $booking->total_price }} VND</td>
                    <td><span class="badge bg-success">{{ ucfirst($booking->payment_status) }}</span></td>
                    <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No upcoming bookings.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Past Bookings -->
<div class="mb-4">
    <h4 class="mb-3"><i class="fas fa-history text-secondary"></i> Past Bookings</h4>
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th scope="col">Movie</th>
                    <th scope="col">Showtime</th>
                    <th scope="col">Room</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Status</th>
                    <th scope="col">Booked At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pastBookings as $booking)
                 <tr>
                    <td>{{ $booking->showtime->movie->title ?? 'N/A' }}</td>
                    <td>
                        {{ $booking->showtime->show_date->format('d M Y') }}, 
                        {{ $booking->showtime->show_time->format('H:i') }}
                    </td>
                    <td>{{ $booking->showtime->room->name ?? 'N/A' }}</td>
                    <td>{{ $booking->total_price }} VND</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($booking->payment_status) }}</span></td>
                    <td>{{ $booking->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No past bookings.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection