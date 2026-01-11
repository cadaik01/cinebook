@extends('layouts.main')

@section('title', 'Upcoming Movies - TCA Cine')

@section('content')
<h2>Showtimes for: {{ $movie->title }}</h2>
@if (count($showtimes)>0)
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Room</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($showtimes as $showtime)
        <tr>
            <td>{{ $showtime->show_date->format('d/m/Y') }}</td>
            <td>{{ $showtime->show_time->format('H:i') }}</td>
            <td>{{ $showtime->room->name }}</td>
            <td><a href="{{ route('booking.seatmap', ['showtime_id' => $showtime->id]) }}">Select Seats</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<p>No showtimes available for this movie.</p>
@endif
<div>
    <a href="{{ route('homepage') }}">Back to Homepage</a>
</div>
@endsection

