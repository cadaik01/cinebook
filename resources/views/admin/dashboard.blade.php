@extends('layouts.admin')

@section('title', 'Dashboard - Admin Panel')

@section('content')
<div class="admin-header">
    <h2><i class="bi bi-speedometer2"></i> Dashboard</h2>
    <p class="text-muted">Overview of CineBook System</p>
</div>

<div class="container-fluid">
    <!-- Row 1: Main Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background: linear-gradient(135deg, var(--deep-teal), var(--deep-space-blue));">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Users</h6>
                            <h2 class="mb-0">{{ $totalUsers }}</h2>
                        </div>
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white" style="background: linear-gradient(135deg, var(--burnt-peach), #ff6b35);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Movies</h6>
                            <h2 class="mb-0">{{ $totalMovies }}</h2>
                        </div>
                        <i class="bi bi-film fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Tickets Today</h6>
                            <h2 class="mb-0">{{ $ticketsSoldToday }}</h2>
                        </div>
                        <i class="bi bi-ticket-perforated fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Active Showtimes</h6>
                            <h2 class="mb-0">{{ $activeShowtimes }}</h2>
                        </div>
                        <i class="bi bi-calendar-event fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Revenue Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Today's Revenue</h6>
                    <h3 class="text-success mb-0">{{ number_format($revenueToday) }}₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="text-muted mb-2">This Month's Revenue</h6>
                    <h3 class="text-primary mb-0">{{ number_format($revenueThisMonth) }}₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-info">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Revenue</h6>
                    <h3 class="text-info mb-0">{{ number_format($totalRevenue) }}₫</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: Movie Stats and Recent Bookings -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header" style="background-color: var(--deep-teal); color: white;">
                    <h5 class="mb-0"><i class="bi bi-graph-up"></i> Movie Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3">
                                <i class="bi bi-play-circle fs-1 text-success"></i>
                                <h4 class="mt-2">{{ $nowShowingMovies }}</h4>
                                <p class="text-muted mb-0">Now Showing</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <i class="bi bi-clock-history fs-1 text-warning"></i>
                                <h4 class="mt-2">{{ $comingSoonMovies }}</h4>
                                <p class="text-muted mb-0">Coming Soon</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3">
                                <i class="bi bi-film fs-1 text-primary"></i>
                                <h4 class="mt-2">{{ $totalMovies }}</h4>
                                <p class="text-muted mb-0">Total Movies</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header" style="background-color: var(--burnt-peach); color: white;">
                    <h5 class="mb-0"><i class="bi bi-trophy"></i> Top Revenue Movies</h5>
                </div>
                <div class="card-body d-flex flex-column">
                    @if($highestRevenueMovie)
                        <div class="mb-3 pb-3 border-bottom">
                            <h6 class="text-success mb-1"><i class="bi bi-arrow-up-circle"></i> Highest Revenue</h6>
                            <strong>{{ $highestRevenueMovie->title }}</strong>
                            <p class="text-success mb-0 fs-5">{{ number_format($highestRevenueMovie->revenue) }}₫</p>
                        </div>
                    @endif

                    @if($lowestRevenueMovie)
                        <div>
                            <h6 class="text-danger mb-1"><i class="bi bi-arrow-down-circle"></i> Lowest Revenue</h6>
                            <strong>{{ $lowestRevenueMovie->title }}</strong>
                            <p class="text-danger mb-0 fs-5">{{ number_format($lowestRevenueMovie->revenue) }}₫</p>
                        </div>
                    @endif

                    @if(!$highestRevenueMovie && !$lowestRevenueMovie)
                        <p class="text-muted mb-0">No revenue data available yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Row 4: Recent Bookings -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background-color: var(--prussian-blue); color: white;">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h5>
                </div>
                <div class="card-body">
                    @if($recentBookings->isEmpty())
                        <p class="text-muted mb-0">No bookings yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Movie</th>
                                        <th>Showtime</th>
                                        <th>Seats</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td><strong>#{{ $booking->id }}</strong></td>
                                            <td>{{ $booking->user->name }}</td>
                                            <td>{{ $booking->showtime->movie->title }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($booking->showtime->show_date)->format('M d, Y') }}<br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($booking->showtime->show_time)->format('h:i A') }}</small>
                                            </td>
                                            <td>{{ $booking->bookingSeats->count() }} seats</td>
                                            <td><strong>{{ number_format($booking->total_price) }}₫</strong></td>
                                            <td>
                                                @if($booking->status == 'confirmed')
                                                    <span class="badge bg-success">Confirmed</span>
                                                @elseif($booking->status == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($booking->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($booking->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @else
                                                    <span class="badge bg-warning">{{ ucfirst($booking->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.bookings.show', $booking) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection